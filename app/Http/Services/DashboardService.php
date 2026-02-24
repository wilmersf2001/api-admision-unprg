<?php

namespace App\Http\Services;

use App\Models\School;
use App\Models\Postulant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Construye la query base aplicando los filtros comunes del dashboard.
     * Filtros soportados: modalidad_id, programa_academico_id, departamento_id.
     */
    private function baseQuery(Request $request)
    {
        $query = Postulant::query();

        if ($request->filled('modalidad_id')) {
            $query->where('tb_postulante.modalidad_id', $request->integer('modalidad_id'));
        }

        if ($request->filled('programa_academico_id')) {
            $query->where('tb_postulante.programa_academico_id', $request->integer('programa_academico_id'));
        }

        // Filtra por departamento de procedencia (nacimiento)
        if ($request->filled('departamento_id')) {
            $query->join('tb_distrito as d_nac', 'd_nac.id', '=', 'tb_postulante.distrito_nac_id')
                  ->join('tb_provincia as p_nac', 'p_nac.id', '=', 'd_nac.provincia_id')
                  ->where('p_nac.departamento_id', $request->integer('departamento_id'))
                  ->whereNull('d_nac.deleted_at')
                  ->whereNull('p_nac.deleted_at');
        }

        return $query;
    }

    /**
     * Resumen general: KPIs principales del dashboard.
     *
     * Incluye:
     *   - Total de inscritos
     *   - Desglose por género con porcentajes
     *   - Desglose por tipo de colegio (nacional/particular) con porcentajes
     *   - Postulantes que ingresaron vs no ingresaron
     */
    public function getSummary(Request $request): array
    {
        $base = $this->baseQuery($request);
        $total = (clone $base)->count();

        // Desglose por género
        $byGender = (clone $base)
            ->join('tb_sexo', function ($join) {
                $join->on('tb_sexo.id', '=', 'tb_postulante.sexo_id')
                     ->whereNull('tb_sexo.deleted_at');
            })
            ->select(
                'tb_sexo.id',
                'tb_sexo.descripcion as genero',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tb_sexo.id', 'tb_sexo.descripcion')
            ->orderBy('tb_sexo.id')
            ->get()
            ->map(fn($item) => [
                'id'          => $item->id,
                'genero'      => $item->genero,
                'total'       => $item->total,
                'porcentaje'  => $total > 0 ? round(($item->total / $total) * 100, 2) : 0,
            ])
            ->values();

        // Desglose por tipo de colegio
        $bySchoolType = (clone $base)
            ->join('tb_colegio', function ($join) {
                $join->on('tb_colegio.id', '=', 'tb_postulante.colegio_id')
                     ->whereNull('tb_colegio.deleted_at');
            })
            ->select(
                'tb_colegio.tipo',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tb_colegio.tipo')
            ->orderBy('tb_colegio.tipo')
            ->get()
            ->map(fn($item) => [
                'tipo_codigo' => $item->tipo,
                'tipo'        => $item->tipo == School::TYPE_NATIONAL ? 'Nacional' : 'Particular',
                'total'       => $item->total,
                'porcentaje'  => $total > 0 ? round(($item->total / $total) * 100, 2) : 0,
            ])
            ->values();

        // Postulantes que ingresaron
        $ingresaron = (clone $base)->where('tb_postulante.ingreso', true)->count();

        return [
            'total_inscritos'   => $total,
            'ingresaron'        => [
                'total'      => $ingresaron,
                'porcentaje' => $total > 0 ? round(($ingresaron / $total) * 100, 2) : 0,
            ],
            'por_genero'        => $byGender,
            'por_tipo_colegio'  => $bySchoolType,
        ];
    }

    /**
     * Ranking de programas académicos por cantidad de postulantes.
     *
     * Query param: limit (default 10, max 50)
     */
    public function getAcademicPrograms(Request $request): array
    {
        $base  = $this->baseQuery($request);
        $total = (clone $base)->count();
        $limit = min((int) $request->get('limit', 10), 50);

        $programs = (clone $base)
            ->join('tb_programa_academico', function ($join) {
                $join->on('tb_programa_academico.id', '=', 'tb_postulante.programa_academico_id')
                     ->whereNull('tb_programa_academico.deleted_at');
            })
            ->select(
                'tb_programa_academico.id',
                'tb_programa_academico.nombre',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tb_programa_academico.id', 'tb_programa_academico.nombre')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'id'         => $item->id,
                'nombre'     => $item->nombre,
                'total'      => $item->total,
                'porcentaje' => $total > 0 ? round(($item->total / $total) * 100, 2) : 0,
            ])
            ->values();

        return [
            'total_inscritos' => $total,
            'programas'       => $programs,
        ];
    }

    /**
     * Ranking de departamentos de procedencia con mayor cantidad de postulantes.
     *
     * Procedencia = departamento del distrito de nacimiento del postulante.
     * Query param: limit (default 10, max 30)
     */
    public function getRegions(Request $request): array
    {
        $base  = $this->baseQuery($request);
        $total = (clone $base)->count();
        $limit = min((int) $request->get('limit', 10), 30);

        // Si ya se aplicó el join del filtro departamento_id, evitamos duplicar alias
        $alreadyJoined = $request->filled('departamento_id');

        $query = (clone $base);

        if (!$alreadyJoined) {
            $query->join('tb_distrito as d_nac', function ($join) {
                    $join->on('d_nac.id', '=', 'tb_postulante.distrito_nac_id')
                         ->whereNull('d_nac.deleted_at');
                })
                ->join('tb_provincia as p_nac', function ($join) {
                    $join->on('p_nac.id', '=', 'd_nac.provincia_id')
                         ->whereNull('p_nac.deleted_at');
                });
        }

        $regions = $query
            ->join('tb_departamento', function ($join) {
                $join->on('tb_departamento.id', '=', 'p_nac.departamento_id')
                     ->whereNull('tb_departamento.deleted_at');
            })
            ->select(
                'tb_departamento.id',
                'tb_departamento.nombre',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tb_departamento.id', 'tb_departamento.nombre')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'id'          => $item->id,
                'departamento' => $item->nombre,
                'total'       => $item->total,
                'porcentaje'  => $total > 0 ? round(($item->total / $total) * 100, 2) : 0,
            ])
            ->values();

        return [
            'total_inscritos' => $total,
            'departamentos'   => $regions,
        ];
    }

    /**
     * Ranking de colegios con mayor cantidad de postulantes.
     *
     * Query params: limit (default 10, max 50), tipo_colegio (1=Nacional, 2=Particular)
     */
    public function getTopSchools(Request $request): array
    {
        $base  = $this->baseQuery($request);
        $total = (clone $base)->count();
        $limit = min((int) $request->get('limit', 10), 50);

        $query = (clone $base)
            ->join('tb_colegio', function ($join) {
                $join->on('tb_colegio.id', '=', 'tb_postulante.colegio_id')
                     ->whereNull('tb_colegio.deleted_at');
            });

        // Filtro adicional por tipo de colegio dentro de este endpoint
        if ($request->filled('tipo_colegio')) {
            $query->where('tb_colegio.tipo', $request->input('tipo_colegio'));
        }

        $schools = $query
            ->select(
                'tb_colegio.id',
                'tb_colegio.nombre',
                'tb_colegio.tipo',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tb_colegio.id', 'tb_colegio.nombre', 'tb_colegio.tipo')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'id'          => $item->id,
                'nombre'      => $item->nombre,
                'tipo_codigo' => $item->tipo,
                'tipo'        => $item->tipo == School::TYPE_NATIONAL ? 'Nacional' : 'Particular',
                'total'       => $item->total,
                'porcentaje'  => $total > 0 ? round(($item->total / $total) * 100, 2) : 0,
            ])
            ->values();

        return [
            'total_inscritos' => $total,
            'colegios'        => $schools,
        ];
    }

    /**
     * Tendencia de inscripciones agrupadas por fecha.
     *
     * Útil para gráficos de línea/área en el dashboard.
     * Query param: group_by (day|week|month, default: day)
     */
    public function getInscriptionTrend(Request $request): array
    {
        $base     = $this->baseQuery($request);
        $groupBy  = in_array($request->get('group_by'), ['day', 'week', 'month'])
                        ? $request->get('group_by')
                        : 'day';

        $dateFormat = match ($groupBy) {
            'month' => '%Y-%m',
            'week'  => '%x-W%v',
            default => '%Y-%m-%d',
        };

        $trend = (clone $base)
            ->whereNotNull('tb_postulante.fecha_inscripcion')
            ->select(
                DB::raw("DATE_FORMAT(fecha_inscripcion, '{$dateFormat}') as periodo"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get()
            ->map(fn($item) => [
                'periodo' => $item->periodo,
                'total'   => $item->total,
            ])
            ->values();

        return [
            'group_by' => $groupBy,
            'tendencia' => $trend,
        ];
    }
}
