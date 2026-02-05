<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostulantRequest extends FormRequest
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
            'nombre' => 'required|string|max:100',
            'ap_paterno' => 'required|string|max:100',
            'ap_materno' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'num_documento' => 'required|string|max:20|unique:tb_postulante,num_documento',
            'tipo_documento' => 'required|string|max:10',
            'num_documento_apoderado' => 'nullable|string|max:20',
            'nombres_apoderado' => 'nullable|string|max:100',
            'ap_paterno_apoderado' => 'nullable|string|max:100',
            'ap_materno_apoderado' => 'nullable|string|max:100',
            'num_voucher' => 'required|string|max:50|unique:tb_postulante,num_voucher',
            'direccion' => 'required|string|max:255',
            'correo' => 'required|email|max:100|unique:tb_postulante,correo',
            'telefono' => 'nullable|string|max:20',
            'telefono_ap' => 'nullable|string|max:20',
            'anno_egreso' => 'required|integer|min:1900|max:' . date('Y'),
            'fecha_inscripcion' => 'required|date',
            'num_veces_unprg' => 'required|integer|min:0',
            'num_veces_otros' => 'required|integer|min:0',
            'codigo' => 'nullable|string|max:20|unique:tb_postulante,codigo',
            'ingreso' => 'required|numeric|min:0',
            'sexo_id' => 'required|exists:tb_sexo,id',
            'distrito_nac_id' => 'required|exists:tb_distrito,id',
            'distrito_res_id' => 'required|exists:tb_distrito,id',
            'tipo_direccion_id' => 'required|exists:tb_tipo_direccion,id',
            'programa_academico_id' => 'required|exists:tb_programa_academico,id',
            'colegio_id' => 'required|exists:tb_colegio,id',
            'universidad_id' => 'required|exists:tb_universidad,id',
            'modalidad_id' => 'required|exists:tb_modalidad,id',
            'sede_id' => 'required|exists:tb_sede,id',
            'pais_id' => 'required|exists:tb_pais,id',
            'estado_postulante_id' => 'required|exists:tb_estado_postulante,id',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'unique' => 'El valor del campo :attribute ya está en uso.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min' => 'El campo :attribute debe tener un valor mínimo de :min.',
            'numeric' => 'El campo :attribute debe ser un número.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
        ];
    }
}
