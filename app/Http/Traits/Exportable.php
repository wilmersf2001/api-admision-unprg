<?php

namespace App\Http\Traits;

use App\Exports\GenericExport;
use Maatwebsite\Excel\Facades\Excel;

trait Exportable
{
    /**
     * Define las columnas que se exportarán.
     *
     * @return array<string, string|array> Formato: ['campo' => 'Nombre Columna'] o ['campo' => ['label' => 'Nombre', 'format' => callable]]
     *
     * Ejemplos:
     * - Campo simple: 'nombres' => 'Nombres'
     * - Relación: 'districtBirth.nombre' => 'Distrito de Nacimiento'
     * - Con formato: 'created_at' => ['label' => 'Fecha Creación', 'format' => fn($value) => $value->format('d/m/Y')]
     * - Con callback complejo: 'gender' => ['label' => 'Sexo', 'value' => fn($model) => $model->gender?->nombre ?? 'N/A']
     */
    abstract protected function getExportColumns(): array;

    /**
     * Obtiene el query base para la exportación.
     * Override este método para personalizar las relaciones eager load.
     */
    protected function getExportQuery()
    {
        $relations = $this->extractRelationsFromColumns();
        return static::with($relations);
    }

    /**
     * Exporta los datos a Excel.
     */
    public static function export(string $filename = null, $query = null)
    {
        $instance = new static();
        $query = $query ?? $instance->getExportQuery();

        $export = new GenericExport(
            $query,
            $instance->getExportColumns(),
            $instance->getExportTitle()
        );

        $filename = $filename ?? $instance->getDefaultExportFilename();

        return Excel::download($export, $filename);
    }

    /**
     * Exporta y guarda el archivo.
     */
    public static function exportAndStore(string $path, string $disk = 'local', $query = null)
    {
        $instance = new static();
        $query = $query ?? $instance->getExportQuery();

        $export = new GenericExport(
            $query,
            $instance->getExportColumns(),
            $instance->getExportTitle()
        );

        return Excel::store($export, $path, $disk);
    }

    /**
     * Exporta a un array (útil para testing o procesamiento).
     */
    public static function exportToArray($query = null): array
    {
        $instance = new static();
        $query = $query ?? $instance->getExportQuery();

        $export = new GenericExport(
            $query,
            $instance->getExportColumns(),
            $instance->getExportTitle()
        );

        return $export->toArray();
    }

    /**
     * Extrae las relaciones necesarias de las columnas configuradas.
     */
    protected function extractRelationsFromColumns(): array
    {
        $relations = [];
        $columns = $this->getExportColumns();

        foreach ($columns as $field => $config) {
            if (is_string($field) && str_contains($field, '.')) {
                $relation = explode('.', $field)[0];
                if (!in_array($relation, $relations)) {
                    $relations[] = $relation;
                }
            }
        }

        return $relations;
    }

    /**
     * Obtiene el título del export (opcional).
     */
    protected function getExportTitle(): ?string
    {
        return null;
    }

    /**
     * Obtiene el nombre de archivo por defecto.
     */
    protected function getDefaultExportFilename(): string
    {
        return strtolower(class_basename(static::class)) . '_' . now()->format('Y-m-d_His') . '.xlsx';
    }
}
