<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_integration_id' => 'nullable', // if we have daredevilry company will add this validation exists:delivery_integrations,id
            'warehouse_from_id' => 'required|exists:warehouses,id',
            'warehouse_to_id' => 'required|exists:warehouses,id|different:warehouse_from_id',
            'notes' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'warehouse_to_id.different' => 'The destination warehouse must be different from the source warehouse.',
            'products.required' => 'At least one product is required.',
            'products.*.quantity.min' => 'Product quantity must be at least 1.',
        ];
    }
}
