<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicGroupRequest extends FormRequest
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
            'letra' => 'required|string|max:5',
            'nombre' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'letra.required' => 'La letra es obligatoria.',
            'letra.string' => 'La letra debe ser una cadena de texto.',
            'letra.max' => 'La letra no debe exceder los 5 caracteres.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
        ];
    }
}
