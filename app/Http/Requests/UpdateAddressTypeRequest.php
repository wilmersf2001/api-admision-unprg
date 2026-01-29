<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressTypeRequest extends FormRequest
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
            'descripcion' => 'sometimes|required|string|max:60',
            'estado' => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'La :attribute es obligatoria cuando se proporciona.',
            'descripcion.string' => 'La :attribute debe ser una cadena de texto.',
            'descripcion.max' => 'La :attribute no debe exceder los :max caracteres.',
            'estado.boolean' => 'El :attribute debe ser verdadero o falso.'
        ];
    }
}
