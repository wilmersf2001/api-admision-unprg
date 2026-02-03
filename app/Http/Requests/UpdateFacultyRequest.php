<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacultyRequest extends FormRequest
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
            'codigo' => 'sometimes|required|string|max:2',
            'nombre' => 'sometimes|required|string|max:255',
            'abreviatura' => 'sometimes|required|string|max:50',
            'decano' => 'sometimes|required|string|max:255',
            'estado' => 'sometimes|required|boolean',
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
            'abreviatura.required' => 'El campo abreviatura es obligatorio.',
            'abreviatura.string' => 'El campo abreviatura debe ser una cadena de texto.',
            'abreviatura.max' => 'El campo abreviatura no debe exceder los 50 caracteres.',
            'decano.required' => 'El campo decano es obligatorio.',
            'decano.string' => 'El campo decano debe ser una cadena de texto.',
            'decano.max' => 'El campo decano no debe exceder los 255 caracteres.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.boolean' => 'El campo estado debe ser verdadero o falso.',
        ];
    }
}
