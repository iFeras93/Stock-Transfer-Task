<?php

namespace App\Services;

use App\Models\StockTransfer;
use App\Models\User;
use App\Enums\StockTransferStatus;
use App\Exceptions\StockTransferException;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    public function create(array $data, User $user): StockTransfer
    {
        return DB::transaction(function () use ($data, $user) {
            $stockTransfer = StockTransfer::query()->create([
                'delivery_integration_id' => $data['delivery_integration_id'] ?? null,
                'warehouse_from_id' => $data['warehouse_from_id'],
                'warehouse_to_id' => $data['warehouse_to_id'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $user->id,
                'status' => StockTransferStatus::NEW,
            ]);

            foreach ($data['products'] as $productData) {
                $stockTransfer->products()->create([
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);
            }

            return $stockTransfer->load(['products.product', 'warehouseFrom', 'warehouseTo']);
        });
    }

    public function changeStatus(StockTransfer $stockTransfer, StockTransferStatus $newStatus, User $user): StockTransfer
    {
        if (!$this->canChangeStatus($stockTransfer, $newStatus, $user)) {
            throw new StockTransferException("Cannot change status from {$stockTransfer->status->value} to {$newStatus->value}");
        }

        $stockTransfer->update(['status' => $newStatus]);

        return $stockTransfer;
    }

    public function canChangeStatus(StockTransfer $stockTransfer, StockTransferStatus $newStatus, User $user): bool
    {
        $allowedTransitions = StockTransferStatus::getWorkflowTransitions();
        $currentStatus = $stockTransfer->status->value;

        if (!in_array($newStatus->value, $allowedTransitions[$currentStatus] ?? [])) {
            return false;
        }

        return $this->hasPermissionForStatusChange($stockTransfer, $newStatus, $user);
    }

    public function getNextAllowedStatuses(StockTransfer $stockTransfer, User $user): array
    {
        $allowedTransitions = StockTransferStatus::getWorkflowTransitions();
        $currentStatus = $stockTransfer->status->value;
        $nextStatuses = $allowedTransitions[$currentStatus] ?? [];

        return array_filter($nextStatuses, function ($status) use ($stockTransfer, $user) {
            return $this->hasPermissionForStatusChange($stockTransfer, StockTransferStatus::from($status), $user);
        });
    }

    private function hasPermissionForStatusChange(StockTransfer $stockTransfer, StockTransferStatus $newStatus, User $user): bool
    {
        $currentStatus = $stockTransfer->status;

        // Sending warehouse permissions
        if (in_array($newStatus, [StockTransferStatus::PREPARING, StockTransferStatus::READY, StockTransferStatus::SHIPPING])) {
            return $user->hasWarehouseAccess($stockTransfer->warehouse_from_id);
        }

        // Cancellation permissions
        if ($newStatus === StockTransferStatus::CANCELLED &&
            in_array($currentStatus, [StockTransferStatus::NEW, StockTransferStatus::PREPARING, StockTransferStatus::READY])) {
            return $user->hasWarehouseAccess($stockTransfer->warehouse_from_id);
        }

        // Receiving warehouse permissions
        if (in_array($newStatus, [StockTransferStatus::COMPLETED, StockTransferStatus::RETURNING])) {
            return $user->hasWarehouseAccess($stockTransfer->warehouse_to_id);
        }

        // Automatic status changes (shipping integration)
        if ($newStatus === StockTransferStatus::RECEIVED) {
            return $user->hasRole('shipping_integration');
        }

        return false;
    }

    public function getStatusCounts(?User $user = null): array
    {
        $query = StockTransfer::query();

        if ($user && !$user->hasRole('admin')) {
            $warehouseIds = $user->getAccessibleWarehouses();
            $query->where(function ($q) use ($warehouseIds) {
                $q->whereIn('warehouse_from_id', $warehouseIds)
                    ->orWhereIn('warehouse_to_id', $warehouseIds);
            });
        }

        $counts = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $result = [];
        foreach (StockTransferStatus::cases() as $status) {
            $result[$status->value] = $counts[$status->value] ?? 0;
        }

        return $result;
    }
}
