<?php

// 1. TRAIT PARA CONSULTAS FLEXIBLES
namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait FlexibleQueries
{
    /**
     * Aplicar filtros dinámicos basados en la configuración del modelo
     */
    public function scopeApplyFilters(Builder $query, Request $request)
    {
        $filters = $this->getFilterConfig();

        foreach ($filters as $field => $config) {
            $this->applyFilter($query, $request, $field, $config);
        }

        return $query;
    }

    /**
     * Aplicar ordenamiento dinámico
     * no aplica para formato /workers?sort_by=status,name&sort_order=desc,asc
     * solo para formato /workers?sort_by=status&sort_order=desc
     */
    public function scopeApplySort(Builder $query, Request $request)
    {
        $sortConfig = $this->getSortConfig();
        $sortBy = $request->get('sort_by', $sortConfig['default'] ?? 'id');
        $sortOrder = $request->get('sort_order', 'asc');

        if (in_array($sortBy, $sortConfig['allowed'] ?? [])) {
            if (str_contains($sortBy, '.')) {
                // Ordenamiento por relación
                [$relation, $field] = explode('.', $sortBy);
                $query->with($relation)->orderBy(
                    $this->getRelationTable($relation) . '.' . $field,
                    $sortOrder
                );
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        return $query;
    }

    /**
     * Aplicar paginación o retornar todos los registros
     */
    public function scopeApplyPagination(Builder $query, Request $request)
    {
        $all = filter_var($request->get('all', false), FILTER_VALIDATE_BOOLEAN);

        if ($all) {
            return $query->get();
        }

        $perPage = $request->get('per_page', 15);
        return $query->paginate($perPage);
    }

    /**
     * Aplicar un filtro individual
     */
    private function applyFilter(Builder $query, Request $request, string $field, array $config)
    {
        $value = $request->get($field);

        // Para filtros especiales como exists, no necesitamos valor
        if ($value === null || $value === '') {
            if (!in_array($config['type'] ?? 'simple', ['exists', 'date_range', 'number_range'])) {
                return;
            }
        }

        $operator = $this->getOperator($request, $field, $config);
        $column = $config['column'] ?? $field;
        $type = $config['type'] ?? 'simple';

        // Manejar diferentes tipos de filtros
        switch ($type) {
            case 'date_range':
                $this->applyDateRangeFilter($query, $request, $field, $config);
                break;
            case 'global_search':
                $this->applyGlobalSearchFilter($query, $value, $config);
                break;
            case 'number_range':
                $this->applyNumberRangeFilter($query, $request, $field, $config);
                break;
            case 'exists':
                $this->applyExistsFilter($query, $request, $field, $config);
                break;
            case 'relation':
                $this->applyRelationFilter($query, $config, $value, $operator);
                break;
            default:
                $this->applyDirectFilter($query, $column, $value, $operator);
        }
    }

    /**
     * Aplicar filtro en relación
     */
    private function applyRelationFilter(Builder $query, array $config, $value, string $operator)
    {
        $relation = $config['relation'];
        $column = $config['column'];

        $query->whereHas($relation, function (Builder $q) use ($column, $value, $operator) {
            if ($operator === 'LIKE') {
                $q->where($column, $operator, "%{$value}%");
            } else {
                $q->where($column, $operator, $value);
            }
        });
    }

    /**
     * Aplicar filtro directo
     */
    private function applyDirectFilter(Builder $query, string $column, $value, string $operator)
    {
        if ($operator === 'LIKE') {
            $query->where($column, $operator, "%{$value}%");
        } elseif ($operator === 'IN') {
            // Manejar diferentes formatos de entrada
            if (is_array($value)) {
                $values = $value;
            } elseif (is_string($value)) {
                // Si es string, puede ser separado por comas
                $values = explode(',', $value);
            } else {
                // Si es un valor único, convertirlo en array
                $values = [$value];
            }

            // Limpiar valores vacíos y espacios
            $values = array_filter(array_map('trim', $values));

            if (!empty($values)) {
                $query->whereIn($column, $values);
            }
        } else {
            $query->where($column, $operator, $value);
        }
    }

    /**
     * Obtener el operador para el filtro
     */
    private function getOperator(Request $request, string $field, array $config): string
    {
        // Si se especifica el operador en la request
        $requestOperator = $request->get($field . '_operator');
        if ($requestOperator) {
            return strtoupper($requestOperator);
        }

        // Operador por defecto del config
        return strtoupper($config['operator'] ?? '=');
    }

    /**
     * Aplicar filtro de rango de fechas
     */
    private function applyDateRangeFilter(Builder $query, Request $request, string $field, array $config)
    {
        $column = $config['column'] ?? $field;
        $startDate = $request->get($field . '_start');
        $endDate = $request->get($field . '_end');

        if ($startDate) {
            $query->where($column, '>=', $startDate);
        }

        if ($endDate) {
            $query->where($column, '<=', $endDate);
        }
    }

    /**
     * Aplicar búsqueda global
     */
    private function applyGlobalSearchFilter(Builder $query, $value, array $config)
    {
        $searchableColumns = $config['columns'];
        $relations = $config['relations'] ?? [];

        $query->where(function (Builder $q) use ($searchableColumns, $relations, $value) {
            // Buscar en columnas locales
            foreach ($searchableColumns as $column) {
                $q->orWhere($column, 'LIKE', "%{$value}%");
            }

            // Buscar en relaciones
            foreach ($relations as $relation => $columns) {
                $q->orWhereHas($relation, function (Builder $subQuery) use ($columns, $value) {
                    foreach ($columns as $column) {
                        $subQuery->orWhere($column, 'LIKE', "%{$value}%");
                    }
                });
            }
        });
    }

    /**
     * Aplicar filtro de rango numérico (salarios, edades, etc.)
     */
    private function applyNumberRangeFilter(Builder $query, Request $request, string $field, array $config)
    {
        $column = $config['column'] ?? $field;
        $minValue = $request->get($field . '_min');
        $maxValue = $request->get($field . '_max');

        if ($minValue !== null && $minValue !== '') {
            $query->where($column, '>=', $minValue);
        }

        if ($maxValue !== null && $maxValue !== '') {
            $query->where($column, '<=', $maxValue);
        }
    }

    /**
     * Filtro de existencia de relación (tiene o no tiene relación)
     */
    private function applyExistsFilter(Builder $query, Request $request, string $field, array $config)
    {
        $relation = $config['relation'];
        $value = $request->get($field);
        $exists = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($exists) {
            $query->whereHas($relation);
        } else {
            $query->whereDoesntHave($relation);
        }
    }

    /**
     * Obtener tabla de relación (debe implementarse en cada modelo)
     */
    protected function getRelationTable(string $relation): string
    {
        return $this->$relation()->getRelated()->getTable();
    }
}
