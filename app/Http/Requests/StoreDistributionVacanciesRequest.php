<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributionVacanciesRequest extends FormRequest
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
            'vacantes' => 'required|integer',
            'programa_academico_id' => 'required|integer|exists:tb_programa_academico,id',
            'modalidad_id' => 'required|integer|exists:tb_modalidad,id',
            'sede_id' => 'required|integer|exists:tb_sede,id',
        ];
    }

        public function messages(): array
        {
            return [
                'vacantes.required' => 'El campo vacantes es obligatorio.',
                'vacantes.integer' => 'El campo vacantes debe ser un número entero.',
                'programa_academico_id.required' => 'El campo programa académico es obligatorio.',
                'programa_academico_id.integer' => 'El campo programa académico debe ser un número entero.',
                'programa_academico_id.exists' => 'El programa académico seleccionado no existe.',
                'modalidad_id.required' => 'El campo modalidad es obligatorio.',
                'modalidad_id.integer' => 'El campo modalidad debe ser un número entero.',
                'modalidad_id.exists' => 'La modalidad seleccionada no existe.',
                'sede_id.required' => 'El campo sede es obligatorio.',
                'sede_id.integer' => 'El campo sede debe ser un número entero.',
                'sede_id.exists' => 'La sede seleccionada no existe.',
            ];
        }
}
