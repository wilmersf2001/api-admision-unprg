<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModalityRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->anio_proceso === 0 || $this->anio_proceso === '0') {
            $this->merge([
                'anio_proceso' => null
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'descripcion' => 'required|string|max:255',
            'monto_nacional' => 'required|numeric|min:0',
            'monto_particular' => 'required|numeric|min:0',
            'anio_proceso' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'examen_id' => 'required|exists:tb_examen,id',
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

            'anio_proceso.integer' => 'El año del proceso debe ser un número entero.',
            'anio_proceso.min' => 'El año del proceso no puede ser anterior a 1950.',
            'anio_proceso.max' => 'El año del proceso no puede ser posterior al próximo año.',

            'examen_id.required' => 'El ID del examen es obligatorio.',
            'examen_id.exists' => 'El ID del examen proporcionado no existe.',
        ];
    }
}
