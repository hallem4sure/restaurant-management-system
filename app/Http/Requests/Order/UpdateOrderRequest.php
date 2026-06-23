<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_id'         => ['nullable', 'integer', 'exists:restaurant_tables,id'],
            'reservation_id'   => ['nullable', 'integer', 'exists:reservations,id'],
            'type'             => ['required', 'in:walk_in,reservation'],
            'status'           => ['required', 'in:pending,confirmed,preparing,ready,served,completed,cancelled'],
            'special_instructions' => ['nullable', 'string'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.special_instructions' => ['nullable', 'string'],
        ];
    }
}
