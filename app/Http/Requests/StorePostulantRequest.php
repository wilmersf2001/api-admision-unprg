<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

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
            // Datos personales
            'num_voucher' => 'required|string|max:50',
            'nombres' => 'required|string|max:100',
            'ap_paterno' => 'required|string|max:100',
            'ap_materno' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'num_documento' => 'required|string|max:20|unique:tb_postulante,num_documento',
            'tipo_documento' => 'required|string|max:10',

            // Datos del apoderado (opcionales)
            'num_documento_apoderado' => 'nullable|string|max:20',
            'nombres_apoderado' => 'nullable|string|max:100',
            'ap_paterno_apoderado' => 'nullable|string|max:100',
            'ap_materno_apoderado' => 'nullable|string|max:100',

            // Datos de contacto
            'direccion' => 'required|string|max:255',
            'correo' => 'required|email|max:100|unique:tb_postulante,correo',
            'telefono' => 'nullable|string|max:20',
            'telefono_ap' => 'nullable|string|max:20',

            // Datos académicos
            'anno_egreso' => 'required|integer|min:1900|max:' . date('Y'),
            'num_veces_unprg' => 'required|integer|min:0',
            'num_veces_otros' => 'required|integer|min:0',

            // Relaciones (IDs)
            'sexo_id' => 'required|exists:tb_sexo,id',
            'distrito_nac_id' => 'required|exists:tb_distrito,id',
            'distrito_res_id' => 'required|exists:tb_distrito,id',
            'tipo_direccion_id' => 'required|exists:tb_tipo_direccion,id',
            'programa_academico_id' => 'required|exists:tb_programa_academico,id',
            'colegio_id' => 'required|exists:tb_colegio,id',
            'universidad_id' => 'nullable|exists:tb_universidad,id',
            'modalidad_id' => 'required|exists:tb_modalidad,id',
            'sede_id' => 'required|exists:tb_sede,id',
            'pais_id' => 'nullable|exists:tb_pais,id',

            //Archivos (requeridos)
            'foto_postulante' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'dni_anverso' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'dni_reverso' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'unique' => 'El :attribute ya está registrado en el sistema.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min' => 'El campo :attribute debe tener un valor mínimo de :min.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombres' => 'nombres',
            'ap_paterno' => 'apellido paterno',
            'ap_materno' => 'apellido materno',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'num_documento' => 'número de documento',
            'tipo_documento' => 'tipo de documento',
            'direccion' => 'dirección',
            'correo' => 'correo electrónico',
            'telefono' => 'teléfono',
            'anno_egreso' => 'año de egreso',
            'sexo_id' => 'sexo',
            'distrito_nac_id' => 'distrito de nacimiento',
            'distrito_res_id' => 'distrito de residencia',
            'programa_academico_id' => 'programa académico',
            'colegio_id' => 'colegio',
            'modalidad_id' => 'modalidad',
            'sede_id' => 'sede',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
