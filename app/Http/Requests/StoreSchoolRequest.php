<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'centro_poblado' => 'required|string|max:255',
            'tipo' => 'required|numeric|between:1,2',
            'ubigeo' => 'required|string|max:20',
            'distrito_id' => 'required|exists:tb_distrito,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del colegio es obligatorio.',
            'nombre.string' => 'El nombre del colegio debe ser una cadena de texto.',
            'nombre.max' => 'El nombre del colegio no puede exceder los 255 caracteres.',
            'centro_poblado.required' => 'El centro poblado es obligatorio.',
            'centro_poblado.string' => 'El centro poblado debe ser una cadena de texto.',
            'centro_poblado.max' => 'El centro poblado no puede exceder los 255 caracteres.',
            'tipo.required' => 'El tipo de colegio es obligatorio.',
            'tipo.numeric' => 'El tipo de colegio debe ser un número.',
            'tipo.between' => 'El tipo de colegio debe ser 1 (Público) o 2 (Privado).',
            'ubigeo.required' => 'El ubigeo es obligatorio.',
            'ubigeo.string' => 'El ubigeo debe ser una cadena de texto.',
            'ubigeo.max' => 'El ubigeo no puede exceder los 20 caracteres.',
            'distrito_id.required' => 'El distrito es obligatorio.',
            'distrito_id.exists' => 'El distrito seleccionado no existe.',
        ];
    }
}
