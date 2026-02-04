<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class
StoreTxtFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filetxt' => 'required|file|mimes:txt|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'filetxt.required' => 'El archivo txt es requerido',
            'filetxt.file' => 'Debe ser un archivo válido',
            'filetxt.mimes' => 'El archivo debe ser de tipo txt',
            'filetxt.max' => 'El archivo no debe superar los 10MB',
        ];
    }
}
