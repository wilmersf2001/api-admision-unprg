<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicProgramRequest extends FormRequest
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
            'nombre' => 'required|string',
            'sede_id' => 'required|integer|exists:tb_sede,id',
            'facultad_id' => 'required|integer|exists:tb_facultad,id',
            'grupo_academico_id' => 'required|integer|exists:tb_grupo_academico,id',
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
            'sede_id.required' => 'El campo sede_id es obligatorio.',
            'sede_id.integer' => 'El campo sede_id debe ser un número entero.',
            'sede_id.exists' => 'El campo sede_id debe existir en la tabla tb_sede.',
            'facultad_id.required' => 'El campo facultad_id es obligatorio.',
            'facultad_id.integer' => 'El campo facultad_id debe ser un número entero.',
            'facultad_id.exists' => 'El campo facultad_id debe existir en la tabla tb_facultad.',
            'grupo_academico_id.required' => 'El campo grupo_academico_id es obligatorio.',
            'grupo_academico_id.integer' => 'El campo grupo_academico_id debe ser un número entero.',
            'grupo_academico_id.exists' => 'El campo grupo_academico_id debe existir en la tabla tb_grupo_academico.',
        ];
    }
}
