<?php

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Resources\StockTransferResource;
use App\Models\StockTransfer;
use App\Services\StockTransferService;
use App\Enums\StockTransferStatus;
use App\Exceptions\StockTransferException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockTransferController extends Controller
{
    public function __construct(
        private readonly StockTransferService $stockTransferService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = StockTransfer::with(['warehouseFrom', 'warehouseTo', 'creator'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('warehouse_from_id')) {
            $query->where('warehouse_from_id', $request->warehouse_from_id);
        }

        if ($request->has('warehouse_to_id')) {
            $query->where('warehouse_to_id', $request->warehouse_to_id);
        }

        // Apply user permissions
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            $warehouseIds = $user->getAccessibleWarehouses();
            $query->where(function ($q) use ($warehouseIds) {
                $q->whereIn('warehouse_from_id', $warehouseIds)
                    ->orWhereIn('warehouse_to_id', $warehouseIds);
            });
        }

        $transfers = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => StockTransferResource::collection($transfers),
            'meta' => [
                'current_page' => $transfers->currentPage(),
                'last_page' => $transfers->lastPage(),
                'per_page' => $transfers->perPage(),
                'total' => $transfers->total(),
            ]
        ]);
    }

    public function statusFilter(): JsonResponse
    {
        $counts = $this->stockTransferService->getStatusCounts(auth()->user());

        $statusOptions = collect(StockTransferStatus::cases())->map(function ($status) use ($counts) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
                'color' => $status->color(),
                'count' => $counts[$status->value] ?? 0,
            ];
        });

        return response()->json([
            'statuses' => $statusOptions,
            'total' => array_sum($counts),
        ]);
    }

    public function store(StoreStockTransferRequest $request): JsonResponse
    {
        try {
            $stockTransfer = $this->stockTransferService->create(
                $request->validated(),
                auth()->user()
            );

            return response()->json([
                'message' => 'Stock transfer created successfully',
                'data' => new StockTransferResource($stockTransfer)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create stock transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeStatus(ChangeStatusRequest $request, StockTransfer $stockTransfer): JsonResponse
    {
        try {
            $newStatus = StockTransferStatus::from($request->status);

            $updatedTransfer = $this->stockTransferService->changeStatus(
                $stockTransfer,
                $newStatus,
                auth()->user()
            );

            // Update notes if provided
            if ($request->notes) {
                $updatedTransfer->update(['notes' => $request->notes]);
            }

            return response()->json([
                'message' => 'Status changed successfully',
                'data' => new StockTransferResource($updatedTransfer->load(['warehouseFrom', 'warehouseTo', 'creator']))
            ]);
        } catch (StockTransferException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to change status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function infoDetails(StockTransfer $stockTransfer): JsonResponse
    {
        $stockTransfer->load([
            'products.product',
            'warehouseFrom',
            'warehouseTo',
            'creator',
            'activities.user'
        ]);

        return response()->json([
            'data' => new StockTransferResource($stockTransfer)
        ]);
    }

    public function cancelOrReturn(Request $request, StockTransfer $stockTransfer): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:cancel,return',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $newStatus = $request->action === 'cancel'
                ? StockTransferStatus::CANCELLED
                : StockTransferStatus::RETURNING;

            $updatedTransfer = $this->stockTransferService->changeStatus(
                $stockTransfer,
                $newStatus,
                auth()->user()
            );

            if ($request->notes) {
                $updatedTransfer->update(['notes' => $request->notes]);
            }

            return response()->json([
                'message' => ucfirst($request->action) . ' action completed successfully',
                'data' => new StockTransferResource($updatedTransfer->load(['warehouseFrom', 'warehouseTo', 'creator']))
            ]);
        } catch (StockTransferException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to ' . $request->action . ' transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
