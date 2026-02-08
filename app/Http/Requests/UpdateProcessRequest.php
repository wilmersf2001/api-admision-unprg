<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessRequest extends FormRequest
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
            'numero' => 'sometimes|required|string|max:50',
            'descripcion' => 'sometimes|nullable|string|max:255',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'sometimes|required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'numero.required' => 'El campo número es obligatorio.',
            'numero.string' => 'El campo número debe ser una cadena de texto.',
            'numero.max' => 'El campo número no debe exceder los 50 caracteres.',
            'descripcion.string' => 'El campo descripción debe ser una cadena de texto.',
            'descripcion.max' => 'El campo descripción no debe exceder los 255 caracteres.',
            'fecha_inicio.required' => 'El campo fecha de inicio es obligatorio.',
            'fecha_inicio.date' => 'El campo fecha de inicio debe ser una fecha válida.',
            'fecha_fin.date' => 'El campo fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'El campo fecha de fin debe ser una fecha posterior o igual a la fecha de inicio.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.boolean' => 'El campo estado debe ser verdadero o falso.',
        ];
    }
}
