<?php

namespace App\Http\Requests;

use App\Enums\StockTransferStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ChangeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(StockTransferStatus::class)],
            'notes' => 'nullable|string|max:500',
        ];
    }
}
