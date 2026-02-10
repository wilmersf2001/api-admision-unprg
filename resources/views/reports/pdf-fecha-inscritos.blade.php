<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            height: 100%;
        }

        .container table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
            padding: 10px 14px;
        }

        .container table .encabezado {
            position: relative;
        }

        .container table .encabezado .fecha-reporte {
            position: absolute;
            top: 2px;
            right: 4px;
            font-size: 12px
        }

        .container table .logo_unprg {
            width: 10%;
            padding-top: 20px;
        }

        .container th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 14px;
            text-align: center
        }

        .container th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container">
        <table>
            <tr>
                <td class="logo_unprg" colspan="1" align="center">
                    <img src={{ $base64ImageLogoUnprg }} alt="logo_unprg" width="80" height="auto">
                </td>
                <td class="encabezado" colspan="3">
                    <h3 align="center" style="margin-top: 35px">PROCESO DE ADMISIÓN 2024-I</h3>
                    <h2 align="center">REPORTE DE POSTULANTES INSCRITOS</h2>
                    <h2 align="center">POR FECHAS DE INSCRIPCIÓN</h2>
                    <div class="fecha-reporte">
                        <p>{{ $today }} {{ date('H:i:s A') }}</p>
                    </div>
                </td>
            </tr>
            <thead>
                <tr>
                    <td colspan="4" align="center">
                        <p style="font-size: 14px; margin-top: 20px;">Postulantes inscritos del
                            <strong>{{ $fechaDesde }}</strong> al
                            <strong>{{ $fechaHasta }}</strong>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" style="background-color: #2876b4;">
                        <p style="text-align: center"><strong style="color: #fff">TOTAL DE INSCRITOS POR FECHAS DE
                                INSCRIPCIÓN</strong></p>
                    </td>
                </tr>
                <tr>
                    <th colspan="2" style="background-color: #747474; color: #fff">FECHAS DE INSCRIPCIÓN</th>
                    <th colspan="2" style="background-color: #747474; color: #fff">TOTAL DE INSCRITOS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resultadoInscritos as $inscritos)
                    <tr>
                        <td colspan="2">{{ $inscritos->fecha_inscripcion }}</td>
                        <td colspan="2"><strong>{{ $inscritos->conteo }}</strong></td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="background-color: #7474742c;color: #000"><strong>TOTAL</strong></td>
                    <td colspan="2" style="background-color: #7474742c; color: #000">
                        <strong>{{ $resultadoInscritos->sum('conteo') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
