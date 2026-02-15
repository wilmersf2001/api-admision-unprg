<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
{
    public function rules(): array
    {
        $allowedActions = array_keys(config('permissions.actions'));
        $allowedTypes = array_keys(config('permissions.types'));

        return [
            'view_id' => 'nullable|exists:views,id',
            'module' => 'required|string|max:100',
            'module_name' => 'required|string|max:255',
            'actions' => 'required|array|min:1',
            'actions.*' => [
                'required',
                'string',
                Rule::in($allowedActions),
            ],
            'type' => [
                'nullable',
                'string',
                Rule::in($allowedTypes),
            ],
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Mensajes personalizados de validación
     */
    public function messages(): array
    {
        return [
            'view_id.exists' => 'La vista seleccionada no existe',
            'module.required' => 'El módulo es requerido',
            'module.max' => 'El módulo no puede exceder 100 caracteres',
            'module_name.required' => 'El nombre del módulo es requerido',
            'actions.required' => 'Debe seleccionar al menos una acción',
            'actions.min' => 'Debe seleccionar al menos una acción',
            'actions.*.in' => 'La acción seleccionada no es válida',
        ];
    }

    /**
     * Atributos personalizados para los mensajes de error
     */
    public function attributes(): array
    {
        return [
            'view_id' => 'vista',
            'module' => 'módulo',
            'module_name' => 'nombre del módulo',
            'actions' => 'acciones',
            'type' => 'tipo',
            'is_active' => 'activo',
        ];
    }

    /**
     * Preparar datos para validación
     */
    protected function prepareForValidation(): void
    {
        // Establecer valores por defecto
        if (!$this->has('type')) {
            $this->merge(['type' => 'basic']);
        }

        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }

        // Remover duplicados del array de actions
        if ($this->has('actions') && is_array($this->actions)) {
            $this->merge(['actions' => array_values(array_unique($this->actions))]);
        }
    }
}
