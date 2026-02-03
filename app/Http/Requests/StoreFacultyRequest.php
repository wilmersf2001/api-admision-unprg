<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
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
            'codigo' => 'required|string|max:2',
            'nombre' => 'required|string|max:255',
            'abreviatura' => 'nullable|string|max:50',
            'decano' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El campo código es obligatorio.',
            'codigo.string' => 'El campo código debe ser una cadena de texto.',
            'codigo.max' => 'El campo código no debe exceder los 2 caracteres.',
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena de texto.',
            'nombre.max' => 'El campo nombre no debe exceder los 255 caracteres.',
            'abreviatura.string' => 'El campo abreviatura debe ser una cadena de texto.',
            'abreviatura.max' => 'El campo abreviatura no debe exceder los 50 caracteres.',
            'decano.string' => 'El campo decano debe ser una cadena de texto.',
            'decano.max' => 'El campo decano no debe exceder los 255 caracteres.',
        ];
    }
}
