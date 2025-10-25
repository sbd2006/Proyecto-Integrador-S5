<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ajusta si reactivan auth/roles
    }

    public function rules(): array
    {
        return [
            'desde' => ['required', 'date'],
            'hasta' => ['required', 'date', 'after_or_equal:desde'],
            'status' => ['nullable', 'in:todos,pendiente,pagado,cancelado'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'hasta.after_or_equal' => 'La fecha "hasta" debe ser mayor o igual que "desde".',
        ];
    }
}
