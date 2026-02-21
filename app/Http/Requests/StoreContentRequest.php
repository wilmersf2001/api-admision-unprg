<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
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
            'code' => 'required|string|max:255|unique:contents,code',
            'title' => 'required|string|max:255',
            'content' => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El campo código es obligatorio.',
            'code.string' => 'El campo código debe ser una cadena de texto.',
            'code.max' => 'El campo código no debe exceder los 255 caracteres.',
            'code.unique' => 'El código ya existe. Por favor, elige otro.',
            'title.required' => 'El campo título es obligatorio.',
            'title.string' => 'El campo título debe ser una cadena de texto.',
            'title.max' => 'El campo título no debe exceder los 255 caracteres.',
            'content.required' => 'El campo contenido es obligatorio.',
            'content.array' => 'El campo contenido debe ser un arreglo.',
        ];
    }
}
