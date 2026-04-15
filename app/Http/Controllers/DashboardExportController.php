<?php

namespace App\Http\Controllers;

use App\Http\Services\DashboardService;
use Exception;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title as ChartTitle;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardExportController extends Controller
{
    protected DashboardService $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    public function export(Request $request): StreamedResponse
    {
        try {
            // ── 1. Obtener todos los datos ──────────────────────────────────
            $summary  = $this->service->getSummary($request);
            $programs = $this->service->getAcademicPrograms($request);
            $regions  = $this->service->getRegions($request);
            $schools  = $this->service->getTopSchools($request);
            $trend    = $this->service->getInscriptionTrend($request);

            // ── 2. Crear el spreadsheet ─────────────────────────────────────
            $spreadsheet = new Spreadsheet();
            $sheet       = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Dashboard');

            // Anchos de columna: A=etiqueta larga, B-D=valores, E-M=relleno para gráficas
            $sheet->getColumnDimension('A')->setWidth(42);
            $sheet->getColumnDimension('B')->setWidth(16);
            $sheet->getColumnDimension('C')->setWidth(16);
            $sheet->getColumnDimension('D')->setWidth(16);
            foreach (['E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'] as $col) {
                $sheet->getColumnDimension($col)->setWidth(13);
            }

            $row = 1;

            // ── ENCABEZADO PRINCIPAL ───────────────────────────────────────
            $sheet->mergeCells("A{$row}:M{$row}");
            $sheet->setCellValue("A{$row}", 'REPORTE EJECUTIVO DE ADMISIÓN — UNPRG');
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(44);
            $row++;

            // Sub-encabezado con filtros y fecha
            $sheet->mergeCells("A{$row}:M{$row}");
            $filterText = 'Exportado el: ' . now()->format('d/m/Y H:i');
            if ($request->filled('modalidad_id')) {
                $filterText .= '   |   Modalidad ID: ' . $request->input('modalidad_id');
            }
            if ($request->filled('programa_academico_id')) {
                $filterText .= '   |   Programa ID: ' . $request->input('programa_academico_id');
            }
            if ($request->filled('departamento_id')) {
                $filterText .= '   |   Departamento ID: ' . $request->input('departamento_id');
            }
            $sheet->setCellValue("A{$row}", $filterText);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '374151']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e0e7ef']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(18);
            $row += 2; // fila filtros + 1 fila en blanco

            // ═══════════════════════════════════════════════════════════════
            // SECCIÓN 1 — INDICADORES GENERALES (KPIs)
            // ═══════════════════════════════════════════════════════════════
            $this->sectionHeader($sheet, $row, 'INDICADORES GENERALES');
            $row++;

            $sheet->setCellValue("A{$row}", 'Indicador');
            $sheet->setCellValue("B{$row}", 'Total');
            $sheet->setCellValue("C{$row}", 'Porcentaje');
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->headerRowStyle());
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;

            $kpiRows = [
                ['Total Inscritos', $summary['total_inscritos'], null],
                ['Ingresaron',      $summary['ingresaron']['total'], $summary['ingresaron']['porcentaje']],
            ];
            foreach ($summary['por_tipo_colegio'] as $tipo) {
                $kpiRows[] = ['Colegio ' . $tipo['tipo'], $tipo['total'], $tipo['porcentaje']];
            }

            $kpiStart = $row;
            foreach ($kpiRows as $i => $kpi) {
                $sheet->setCellValue("A{$row}", $kpi[0]);
                $sheet->setCellValue("B{$row}", $kpi[1]);
                if ($kpi[2] !== null) {
                    $sheet->setCellValue("C{$row}", $kpi[2] / 100);
                    $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('0.00%');
                }
                $style = $this->dataRowStyle($i);
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($style);
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
            $this->applyTableOuterBorder($sheet, "A{$kpiStart}:C" . ($row - 1));
            $row += 3; // espacio entre secciones

            // ═══════════════════════════════════════════════════════════════
            // SECCIÓN 2 — DISTRIBUCIÓN POR GÉNERO  [gráfica Pie debajo]
            // ═══════════════════════════════════════════════════════════════
            $this->sectionHeader($sheet, $row, 'DISTRIBUCIÓN POR GÉNERO');
            $row++;

            $sheet->setCellValue("A{$row}", 'Género');
            $sheet->setCellValue("B{$row}", 'Total');
            $sheet->setCellValue("C{$row}", 'Porcentaje');
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->headerRowStyle());
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;

            $generoStart = $row;
            foreach ($summary['por_genero'] as $i => $g) {
                $sheet->setCellValue("A{$row}", ucfirst(strtolower($g['genero'])));
                $sheet->setCellValue("B{$row}", $g['total']);
                $sheet->setCellValue("C{$row}", $g['porcentaje'] / 100);
                $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('0.00%');
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->dataRowStyle($i));
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
            $generoEnd = $row - 1;
            $this->applyTableOuterBorder($sheet, "A{$generoStart}:C{$generoEnd}");
            $row++;

            // Gráfica Pie — ancho completo A:M, debajo de la tabla
            $chartRow = $row;
            $chartHeight = 20;
            if ($generoEnd >= $generoStart) {
                $chart = $this->pieChart('Dashboard', 'Género', $generoStart, $generoEnd);
                $chart->setTopLeftPosition('A' . $chartRow);
                $chart->setBottomRightPosition('M' . ($chartRow + $chartHeight));
                $sheet->addChart($chart);
                $row = $chartRow + $chartHeight + 1;
            }
            $row += 3;

            // ═══════════════════════════════════════════════════════════════
            // SECCIÓN 3 — TENDENCIA DE INSCRIPCIONES  [gráfica Línea debajo]
            // ═══════════════════════════════════════════════════════════════
            $groupByLabel = match ($trend['group_by']) {
                'month' => 'por mes',
                'week'  => 'por semana',
                default => 'por día',
            };

            $this->sectionHeader($sheet, $row, "TENDENCIA DE INSCRIPCIONES ({$groupByLabel})");
            $row++;

            $sheet->setCellValue("A{$row}", 'Período');
            $sheet->setCellValue("B{$row}", 'Inscripciones');
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->headerRowStyle());
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;

            $tendenciaStart = $row;
            foreach ($trend['tendencia'] as $i => $t) {
                $sheet->setCellValue("A{$row}", $t['periodo']);
                $sheet->setCellValue("B{$row}", $t['total']);
                $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($this->dataRowStyle($i));
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
            $tendenciaEnd = $row - 1;
            $this->applyTableOuterBorder($sheet, "A{$tendenciaStart}:B{$tendenciaEnd}");
            $row++;

            // Gráfica Línea — ancho completo A:M, debajo de la tabla
            $chartRow    = $row;
            $chartHeight = max(22, (int)(count($trend['tendencia']) * 0.6) + 10);
            if ($tendenciaEnd >= $tendenciaStart) {
                $chart = $this->lineChart('Dashboard', 'Inscripciones', $tendenciaStart, $tendenciaEnd);
                $chart->setTopLeftPosition('A' . $chartRow);
                $chart->setBottomRightPosition('M' . ($chartRow + $chartHeight));
                $sheet->addChart($chart);
                $row = $chartRow + $chartHeight + 1;
            }
            $row += 3;

            // ═══════════════════════════════════════════════════════════════
            // SECCIÓN 4 — TOP PROGRAMAS ACADÉMICOS  [gráfica Barras debajo]
            // ═══════════════════════════════════════════════════════════════
            $this->sectionHeader($sheet, $row, 'TOP PROGRAMAS ACADÉMICOS');
            $row++;

            $sheet->setCellValue("A{$row}", 'Programa');
            $sheet->setCellValue("B{$row}", 'Total');
            $sheet->setCellValue("C{$row}", 'Porcentaje');
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->headerRowStyle());
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;

            $programasStart = $row;
            foreach ($programs['programas'] as $i => $p) {
                $sheet->setCellValue("A{$row}", $p['nombre']);
                $sheet->setCellValue("B{$row}", $p['total']);
                $sheet->setCellValue("C{$row}", $p['porcentaje'] / 100);
                $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('0.00%');
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->dataRowStyle($i));
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
            $programasEnd = $row - 1;
            $this->applyTableOuterBorder($sheet, "A{$programasStart}:C{$programasEnd}");
            $row++;

            // Gráfica Barras — ancho completo A:M, debajo de la tabla
            $chartRow    = $row;
            $chartHeight = max(20, count($programs['programas']) + 8);
            if ($programasEnd >= $programasStart) {
                $chart = $this->barChart('Dashboard', 'Postulantes', $programasStart, $programasEnd, 'B');
                $chart->setTopLeftPosition('A' . $chartRow);
                $chart->setBottomRightPosition('M' . ($chartRow + $chartHeight));
                $sheet->addChart($chart);
                $row = $chartRow + $chartHeight + 1;
            }
            $row += 3;

            // ═══════════════════════════════════════════════════════════════
            // SECCIÓN 5 — TOP DEPARTAMENTOS  [gráfica Barras debajo]
            // ═══════════════════════════════════════════════════════════════
            $this->sectionHeader($sheet, $row, 'TOP DEPARTAMENTOS DE PROCEDENCIA');
            $row++;

            $sheet->setCellValue("A{$row}", 'Departamento');
            $sheet->setCellValue("B{$row}", 'Total');
            $sheet->setCellValue("C{$row}", 'Porcentaje');
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->headerRowStyle());
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;

            $regionesStart = $row;
            foreach ($regions['departamentos'] as $i => $d) {
                $sheet->setCellValue("A{$row}", $d['departamento']);
                $sheet->setCellValue("B{$row}", $d['total']);
                $sheet->setCellValue("C{$row}", $d['porcentaje'] / 100);
                $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('0.00%');
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($this->dataRowStyle($i));
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
            $regionesEnd = $row - 1;
            $this->applyTableOuterBorder($sheet, "A{$regionesStart}:C{$regionesEnd}");
            $row++;

            // Gráfica Barras — ancho completo A:M, debajo de la tabla
            $chartRow    = $row;
            $chartHeight = max(20, count($regions['departamentos']) + 8);
            if ($regionesEnd >= $regionesStart) {
                $chart = $this->barChart('Dashboard', 'Postulantes', $regionesStart, $regionesEnd, 'B');
                $chart->setTopLeftPosition('A' . $chartRow);
                $chart->setBottomRightPosition('M' . ($chartRow + $chartHeight));
                $sheet->addChart($chart);
                $row = $chartRow + $chartHeight + 1;
            }
            $row += 3;

            // ═══════════════════════════════════════════════════════════════
            // SECCIÓN 6 — TOP COLEGIOS  [gráfica Barras debajo]
            // ═══════════════════════════════════════════════════════════════
            $this->sectionHeader($sheet, $row, 'TOP COLEGIOS DE PROCEDENCIA');
            $row++;

            $sheet->setCellValue("A{$row}", 'Colegio');
            $sheet->setCellValue("B{$row}", 'Tipo');
            $sheet->setCellValue("C{$row}", 'Total');
            $sheet->setCellValue("D{$row}", 'Porcentaje');
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray($this->headerRowStyle());
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;

            $colegiosStart = $row;
            foreach ($schools['colegios'] as $i => $c) {
                $sheet->setCellValue("A{$row}", $c['nombre']);
                $sheet->setCellValue("B{$row}", $c['tipo']);
                $sheet->setCellValue("C{$row}", $c['total']);
                $sheet->setCellValue("D{$row}", $c['porcentaje'] / 100);
                $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('0.00%');
                $sheet->getStyle("A{$row}:D{$row}")->applyFromArray($this->dataRowStyle($i));
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
            $colegiosEnd = $row - 1;
            $this->applyTableOuterBorder($sheet, "A{$colegiosStart}:D{$colegiosEnd}");
            $row++;

            // Gráfica Barras — ancho completo A:M, debajo de la tabla
            $chartRow    = $row;
            $chartHeight = max(20, count($schools['colegios']) + 8);
            if ($colegiosEnd >= $colegiosStart) {
                $chart = $this->barChart('Dashboard', 'Postulantes', $colegiosStart, $colegiosEnd, 'C');
                $chart->setTopLeftPosition('A' . $chartRow);
                $chart->setBottomRightPosition('M' . ($chartRow + $chartHeight));
                $sheet->addChart($chart);
                $row = $chartRow + $chartHeight + 1;
            }

            // ── 3. Generar y transmitir el archivo ──────────────────────────
            $writer = new Xlsx($spreadsheet);
            $writer->setIncludeCharts(true);

            $filename = 'dashboard_admision_' . now()->format('Ymd_His') . '.xlsx';

            return response()->streamDownload(
                function () use ($writer) {
                    $writer->save('php://output');
                },
                $filename,
                [
                    'Content-Type'                  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition'           => 'attachment; filename="' . $filename . '"',
                    'Cache-Control'                 => 'max-age=0',
                    'Access-Control-Expose-Headers' => 'Content-Disposition',
                ]
            );
        } catch (Exception $e) {
            return response()->streamDownload(function () use ($e) {
                echo json_encode(['error' => $e->getMessage()]);
            }, 'error.json', ['Content-Type' => 'application/json']);
        }
    }

    // ─── Helpers de layout ────────────────────────────────────────────────────

    /** Encabezado de sección — siempre abarca las 13 columnas (A:M). */
    private function sectionHeader($sheet, int $row, string $title): void
    {
        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", '  ' . $title);
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1d4ed8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(24);
    }

    private function headerRowStyle(): array
    {
        return [
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1e3a5f']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'bfdbfe']],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '93c5fd']],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
    }

    /** Filas de datos con color alterno (zebra striping). */
    private function dataRowStyle(int $index = 0): array
    {
        $bg = ($index % 2 === 0) ? 'f0f9ff' : 'FFFFFF';
        return [
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'cbd5e1']],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
    }

    /** Borde exterior grueso sobre el rango de la tabla completa. */
    private function applyTableOuterBorder($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1d4ed8']],
            ],
        ]);
    }

    // ─── Builders de gráficas ─────────────────────────────────────────────────

    private function pieChart(string $sheetName, string $seriesLabel, int $dataStart, int $dataEnd): Chart
    {
        $count = $dataEnd - $dataStart + 1;

        $label      = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, [$seriesLabel]);
        $categories = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING,  "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, $count);
        $values     = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER,  "'{$sheetName}'!\$B\${$dataStart}:\$B\${$dataEnd}", null, $count);

        $series   = new DataSeries(DataSeries::TYPE_PIECHART, DataSeries::GROUPING_STANDARD, [0], [$label], [$categories], [$values]);
        $plotArea = new PlotArea(null, [$series]);
        $legend   = new Legend(Legend::POSITION_RIGHT, null, false);

        return new Chart('pie_genero', new ChartTitle('Distribución por Género'), $legend, $plotArea, true, 0, null, null);
    }

    private function lineChart(string $sheetName, string $seriesLabel, int $dataStart, int $dataEnd): Chart
    {
        $count = $dataEnd - $dataStart + 1;

        $label      = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, [$seriesLabel]);
        $categories = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING,  "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, $count);
        $values     = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER,  "'{$sheetName}'!\$B\${$dataStart}:\$B\${$dataEnd}", null, $count);

        $series   = new DataSeries(DataSeries::TYPE_LINECHART, DataSeries::GROUPING_STANDARD, [0], [$label], [$categories], [$values]);
        $plotArea = new PlotArea(null, [$series]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);

        return new Chart('line_tendencia', new ChartTitle('Tendencia de Inscripciones'), $legend, $plotArea, true, 0, null, null);
    }

    private function barChart(string $sheetName, string $seriesLabel, int $dataStart, int $dataEnd, string $valueCol = 'B'): Chart
    {
        $count = $dataEnd - $dataStart + 1;

        $label      = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, [$seriesLabel]);
        $categories = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING,  "'{$sheetName}'!\$A\${$dataStart}:\$A\${$dataEnd}", null, $count);
        $values     = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER,  "'{$sheetName}'!\${$valueCol}\${$dataStart}:\${$valueCol}\${$dataEnd}", null, $count);

        $series   = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED, [0], [$label], [$categories], [$values]);
        $plotArea = new PlotArea(null, [$series]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);

        return new Chart('bar_chart_' . $dataStart, new ChartTitle($seriesLabel), $legend, $plotArea, true, 0, null, null);
    }
}