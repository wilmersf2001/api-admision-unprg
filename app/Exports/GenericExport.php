<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class GenericExport implements FromQuery, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected Builder $query;
    protected array $columns;
    protected ?string $title;

    /**
     * @param Builder $query Query builder con los datos a exportar
     * @param array $columns Configuración de columnas ['campo' => 'Label'] o ['campo' => ['label' => 'Label', 'format' => callable]]
     * @param string|null $title Título de la hoja
     */
    public function __construct(Builder $query, array $columns, ?string $title = null)
    {
        $this->query = $query;
        $this->columns = $columns;
        $this->title = $title ?? 'Exportación';
    }

    /**
     * Query de datos.
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * Encabezados de las columnas.
     */
    public function headings(): array
    {
        return collect($this->columns)->map(function ($config) {
            if (is_array($config)) {
                return $config['label'] ?? 'Sin título';
            }
            return $config;
        })->values()->toArray();
    }

    /**
     * Mapea cada fila del modelo.
     */
    public function map($row): array
    {
        $data = [];

        foreach ($this->columns as $field => $config) {
            $value = null;

            if (is_array($config)) {
                if (isset($config['value'])) {
                    $value = $config['value']($row);
                } else {
                    $value = $this->getNestedValue($row, $field);
                    if (isset($config['format']) && is_callable($config['format'])) {
                        $value = $config['format']($value, $row);
                    }
                }
            } else {
                $value = $this->getNestedValue($row, $field);
            }

            $data[] = $value ?? '';
        }

        return $data;
    }

    /**
     * Obtiene valores anidados usando notación de punto.
     */
    protected function getNestedValue($object, string $key)
    {
        if (!str_contains($key, '.')) {
            return $object->{$key} ?? null;
        }

        $keys = explode('.', $key);
        $value = $object;

        foreach ($keys as $segment) {
            if (is_object($value)) {
                $value = $value->{$segment} ?? null;
            } elseif (is_array($value)) {
                $value = $value[$segment] ?? null;
            } else {
                return null;
            }

            if ($value === null) {
                return null;
            }
        }

        return $value;
    }

    /**
     * Título de la hoja.
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Estilos para la hoja.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ],
            ],
        ];
    }

    /**
     * Convierte la exportación a array (útil para testing).
     */
    public function toArray(): array
    {
        $results = $this->query->get();

        return [
            'headings' => $this->headings(),
            'data' => $results->map(fn($row) => $this->map($row))->toArray(),
        ];
    }
}