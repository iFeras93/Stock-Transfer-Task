<?php

namespace App\Http\Resources;

use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'delivery_integration_id' => $this->delivery_integration_id,
//            'delivery_integration' => $this->delivery_integration_id, when we create delivery_company table we will get the data from relation
            'warehouse_from' => [
                'id' => $this->warehouseFrom->id,
                'name' => $this->warehouseFrom->name,
            ],
            'warehouse_to' => [
                'id' => $this->warehouseTo->id,
                'name' => $this->warehouseTo->name,
            ],
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
            ],
            'notes' => $this->notes,
            'created_by' => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ],
            'products' => StockTransferProductResource::collection($this->whenLoaded('products')),
            'activities' => StockTransferActivityResource::collection($this->whenLoaded('activities')),
            $this->when(
                auth()->check(),
                function () {
                    $service = app(StockTransferService::class);
                    return $service->getNextAllowedStatuses($this->resource, auth()->user());
                }
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
