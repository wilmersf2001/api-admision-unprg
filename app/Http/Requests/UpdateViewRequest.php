<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateViewRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('views')->ignore($this->route('view')),
            ],
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:views,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la vista es requerido',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'description.max' => 'La descripción no puede exceder 255 caracteres',
            'slug.required' => 'El slug de la vista es requerido',
            'slug.max' => 'El slug no puede exceder 255 caracteres',
            'slug.unique' => 'Ya existe una vista con este slug',
            'route.max' => 'La ruta no puede exceder 255 caracteres',
            'icon.max' => 'El icono no puede exceder 255 caracteres',
            'order.integer' => 'El orden debe ser un número entero',
            'is_active.boolean' => 'El campo activo debe ser verdadero o falso',
            'parent_id.exists' => 'La vista padre seleccionada no existe',
        ];
    }
}
