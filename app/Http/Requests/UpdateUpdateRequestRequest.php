<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUpdateRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected',
            'note'   => 'required_if:status,rejected|nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El estado de respuesta es obligatorio.',
            'status.in'       => 'El estado debe ser "approved" o "rejected".',
            'note.required_if' => 'El motivo del rechazo es obligatorio.',
            'note.string' => 'El motivo del rechazo debe ser una cadena de texto.',
            'note.max' => 'El motivo del rechazo no debe exceder los 500 caracteres.',
        ];
    }
}
