<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModalityRequest extends FormRequest
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
            'descripcion' => 'sometimes|required|string|max:255',
            'monto_nacional' => 'sometimes|required|numeric|min:0',
            'monto_particular' => 'sometimes|required|numeric|min:0',
            'estado' => 'sometimes|required|boolean',
            'examen_id' => 'sometimes|required|exists:tb_examen,id',
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max' => 'La descripción no debe exceder los 255 caracteres.',

            'monto_nacional.required' => 'El monto nacional es obligatorio.',
            'monto_nacional.numeric' => 'El monto nacional debe ser un número.',
            'monto_nacional.min' => 'El monto nacional no debe ser negativo.',

            'monto_particular.required' => 'El monto internacional es obligatorio.',
            'monto_particular.numeric' => 'El monto internacional debe ser un número.',
            'monto_particular.min' => 'El monto internacional no debe ser negativo.',

            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',

            'examen_id.required' => 'El ID del examen es obligatorio.',
            'examen_id.exists' => 'El ID del examen proporcionado no existe.',
        ];
    }
}
