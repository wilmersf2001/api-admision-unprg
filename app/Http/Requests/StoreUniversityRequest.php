<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUniversityRequest extends FormRequest
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
            'nombre' => 'required|string|max:255|unique:tb_universidad,nombre',
            'tipo' => 'required|string|in:Publica,Privada',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la universidad es obligatorio.',
            'nombre.string' => 'El nombre de la universidad debe ser una cadena de texto.',
            'nombre.max' => 'El nombre de la universidad no debe exceder los 255 caracteres.',
            'nombre.unique' => 'El nombre de la universidad ya existe.',
            'tipo.required' => 'El tipo de universidad es obligatorio.',
            'tipo.string' => 'El tipo de universidad debe ser una cadena de texto.',
            'tipo.in' => 'El tipo de universidad debe ser "Publica" o "Privada".',
        ];
    }
}
