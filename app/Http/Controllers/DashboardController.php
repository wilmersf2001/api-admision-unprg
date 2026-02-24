<?php

namespace App\Http\Controllers;

use App\Http\Services\DashboardService;
use App\Http\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    use ApiResponse;

    protected DashboardService $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/dashboard/summary
     *
     * KPIs principales: total inscritos, desglose por género,
     * por tipo de colegio y tasa de ingreso.
     *
     * Filtros: modalidad_id, programa_academico_id, departamento_id
     */
    public function summary(Request $request)
    {
        try {
            return $this->successResponse(
                $this->service->getSummary($request),
                'Resumen del dashboard obtenido exitosamente'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/dashboard/academic-programs
     *
     * Ranking de programas académicos con mayor cantidad de postulantes.
     *
     * Filtros: modalidad_id, programa_academico_id, departamento_id
     * Query param: limit (default 10, max 50)
     */
    public function academicPrograms(Request $request)
    {
        try {
            return $this->successResponse(
                $this->service->getAcademicPrograms($request),
                'Programas académicos del dashboard obtenidos exitosamente'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/dashboard/regions
     *
     * Ranking de departamentos de procedencia con mayor cantidad de postulantes.
     *
     * Filtros: modalidad_id, programa_academico_id, departamento_id
     * Query param: limit (default 10, max 30)
     */
    public function regions(Request $request)
    {
        try {
            return $this->successResponse(
                $this->service->getRegions($request),
                'Regiones del dashboard obtenidas exitosamente'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/dashboard/top-schools
     *
     * Ranking de colegios con mayor cantidad de postulantes.
     *
     * Filtros: modalidad_id, programa_academico_id, departamento_id, tipo_colegio (1|2)
     * Query param: limit (default 10, max 50)
     */
    public function topSchools(Request $request)
    {
        try {
            return $this->successResponse(
                $this->service->getTopSchools($request),
                'Top colegios del dashboard obtenidos exitosamente'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/dashboard/inscription-trend
     *
     * Tendencia de inscripciones por período (día, semana o mes).
     * Ideal para gráficos de línea en el dashboard.
     *
     * Filtros: modalidad_id, programa_academico_id, departamento_id
     * Query param: group_by (day|week|month, default: day)
     */
    public function inscriptionTrend(Request $request)
    {
        try {
            return $this->successResponse(
                $this->service->getInscriptionTrend($request),
                'Tendencia de inscripciones obtenida exitosamente'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
