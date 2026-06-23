<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_id'         => ['required', 'integer', 'exists:restaurant_tables,id'],
            'customer_name'    => ['required', 'string', 'max:255'],
            'customer_phone'   => ['nullable', 'string', 'max:20'],
            'customer_email'   => ['nullable', 'email', 'max:255'],
            'party_size'       => ['required', 'integer', 'min:1', 'max:50'],
            'type'             => ['required', 'in:immediate,scheduled'],
            'reserved_at'      => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'status'           => ['required', 'in:pending,confirmed,seated,completed,cancelled,no_show'],
            'notes'            => ['nullable', 'string'],
        ];
    }
}
