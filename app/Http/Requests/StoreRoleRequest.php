<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.string' => 'El nombre del rol debe ser una cadena de texto.',
            'name.max' => 'El nombre del rol no puede exceder los 255 caracteres.',
            'name.unique' => 'El nombre del rol ya existe. Por favor, elige otro nombre.',
            'description.string' => 'La descripción del rol debe ser una cadena de texto.',
            'description.max' => 'La descripción del rol no puede exceder los 255 caracteres.',
        ];
    }
}
