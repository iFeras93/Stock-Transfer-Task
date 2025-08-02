<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'from_status' => $this->from_status,
            'to_status' => $this->to_status,
            'user' => $this->when($this->user, [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ]),
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
        ];
    }
}
