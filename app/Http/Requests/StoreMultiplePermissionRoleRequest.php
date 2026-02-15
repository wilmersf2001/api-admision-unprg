<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMultiplePermissionRoleRequest extends FormRequest
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
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'role_id' => 'rol',
            'permissions' => 'permisos',
        ];
    }

    public function messages(): array
    {
        return [
            'role_id.required' => 'El campo :attribute es obligatorio.',
            'role_id.exists' => 'El :attribute seleccionado no existe.',
            'permissions.array' => 'El campo :attribute debe ser un arreglo.',
            'permissions.*.exists' => 'El permiso seleccionado no existe.',
        ];
    }
}
