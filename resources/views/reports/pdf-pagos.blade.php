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
                <td class="encabezado" colspan="{{ count($importes) + 1 }}">
                    <h3 align="center" style="margin-top: 35px">PROCESO DE ADMISIÓN 2024-I</h3>
                    <h2 align="center">REPORTE DE PAGOS - BANCO DE LA NACIÓN</h2>
                    <div align="center" style="margin-top: 10px;">
                        <p>Transacción 9135</p>
                        <p>Colegios Nacionales (00346), Particulares (00345) y otros pagos</p>
                    </div>
                    <div class="fecha-reporte">
                        <p>{{ $today }} {{ date('H:i:s A') }}</p>
                    </div>
                </td>
            </tr>
            <thead>
                <tr>
                    <td colspan="{{ count($importes) + 2 }}" align="center">
                        <p style="font-size: 14px; margin-top: 20px;">Pagos relizados del
                            <strong>{{ $fechaDesde }}</strong> al
                            <strong>{{ $fechaHasta }}</strong>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="{{ count($importes) + 2 }}" align="center" style="background-color: #2876b4;">
                        <p style="text-align: center"></p><strong style="color: #fff">TOTAL PAGOS POR COLEGIOS
                            NACIONALES Y
                            PARTICULARES</strong>
                    </td>
                </tr>
                <tr>
                    <th style="background-color: #747474; color: #fff">Fecha</th>
                    @foreach ($importes as $importe)
                        <th style="background-color: #747474; color: #fff">S/. {{ $importe }}</th>
                    @endforeach
                    <th style="background-color: #747474; color: #fff">SubTotal por fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $fecha => $pagosPorImporte)
                    <tr>
                        <td>{{ $fecha }}</td>
                        @foreach ($importes as $importe)
                            <td>{{ $pagosPorImporte[$importe] ?? 0 }}</td>
                        @endforeach
                        <td><strong>S/. {{ $pagosPorImporte['subTotal'] }}</strong></td>
                    </tr>
                @endforeach
                <tr>
                    <td style="background-color: rgb(230, 230, 230); color: #000"><strong>TOTAL</strong></td>
                    @foreach ($cantidadImportes as $importe)
                        <td style="background-color: rgb(230, 230, 230); color: #000"><strong>{{ $importe }}</strong></td>
                    @endforeach
                    <td style="background-color: rgb(230, 230, 230); color: #000"><strong>S/. {{ $totalPagos }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
