<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistrictRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:100'],
            'ubigeo' => ['required', 'string', 'max:6', 'unique:tb_distrito,ubigeo'],
            'provincia_id' => ['required', 'integer', 'exists:tb_provincia,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del distrito es obligatorio.',
            'nombre.string' => 'El nombre del distrito debe ser una cadena de texto.',
            'nombre.max' => 'El nombre del distrito no debe exceder los 100 caracteres.',
            'ubigeo.required' => 'El código ubigeo es obligatorio.',
            'ubigeo.string' => 'El código ubigeo debe ser una cadena de texto.',
            'ubigeo.max' => 'El código ubigeo no debe exceder los 6 caracteres.',
            'ubigeo.unique' => 'El código ubigeo ya existe.',
            'provincia_id.required' => 'La provincia es obligatoria.',
            'provincia_id.integer' => 'La provincia debe ser un número entero.',
            'provincia_id.exists' => 'La provincia seleccionada no existe.',
        ];
    }
}
