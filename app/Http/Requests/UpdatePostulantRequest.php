<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostulantRequest extends FormRequest
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
            'nombre' => 'sometimes|required|string|max:100',
            'ap_paterno' => 'sometimes|required|string|max:100',
            'ap_materno' => 'sometimes|required|string|max:100',
            'fecha_nacimiento' => 'sometimes|required|date',
            'num_documento' => 'sometimes|required|string|max:20',
            'tipo_documento' => 'sometimes|required|string|max:10',
            'num_documento_apoderado' => 'sometimes|nullable|string|max:20',
            'nombres_apoderado' => 'sometimes|nullable|string|max:100',
            'ap_paterno_apoderado' => 'sometimes|nullable|string|max:100',
            'ap_materno_apoderado' => 'sometimes|nullable|string|max:100',
            'num_voucher' => 'sometimes|required|string|max:50',
            'direccion' => 'sometimes|required|string|max:255',
            'correo' => 'sometimes|required|email|max:100',
            'telefono' => 'sometimes|nullable|string|max:20',
            'telefono_ap' => 'sometimes|nullable|string|max:20',
            'anno_egreso' => 'sometimes|required|integer|min:1900|max:' . date('Y'),
            'fecha_inscripcion' => 'sometimes|required|date',
            'num_veces_unprg' => 'sometimes|required|integer|min:0',
            'num_veces_otros' => 'sometimes|required|integer|min:0',
            'codigo' => 'sometimes|nullable|string|max:20',
            'ingreso' => 'sometimes|required|numeric|min:0',
            'sexo_id' => 'sometimes|required|exists:tb_sexo,id',
            'distrito_nac_id' => 'sometimes|required|exists:tb_distrito,id',
            'distrito_res_id' => 'sometimes|required|exists:tb_distrito,id',
            'tipo_direccion_id' => 'sometimes|required|exists:tb_tipo_direccion,id',
            'programa_academico_id' => 'sometimes|required|exists:tb_programa_academico,id',
            'colegio_id' => 'sometimes|required|exists:tb_colegio,id',
            'universidad_id' => 'sometimes|required|exists:tb_universidad,id',
            'modalidad_id' => 'sometimes|required|exists:tb_modalidad,id',
            'sede_id' => 'sometimes|required|exists:tb_sede,id',
            'pais_id' => 'sometimes|required|exists:tb_pais,id',
            'estado_postulante_id' => 'sometimes|required|exists:tb_estado_postulante,id',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'numeric' => 'El campo :attribute debe ser un número válido.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
        ];
    }
}
