<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Postulante {{ $postulante->num_documento }}</title>
    <style>
        * {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
            padding: 10px 14px;
        }

        td {
            height: 5px;
            padding: 0 10px;
            vertical-align: top;
        }

        .azul {
            background-color: #2974B5;
        }

        .celeste {
            background-color: #7CD3E7;
        }

        .amarillo {
            background-color: #FEC400;
        }

        .plomo {
            background-color: #747474;
        }

        li {
            font-size: 12px;
        }

        p {
            font-size: 14px;
        }

        .container .foto_perfil {
            width: 32%;
            height: 160px;
            padding: 10px 0;
            background-color: #f4f4f4;
        }

        .container .foto_perfil img {
            border: 2px solid #ccc;
        }

        .container .encabezado {
            padding-top: 15px;
        }

        .container .encabezado h3 {
            margin-bottom: 10px;
            font-size: 19px;
        }

        .container .logo_unprg {
            width: 8%;
            padding-top: 20px;
        }

        .left-column .info-contacto {
            margin-top: 10px;
        }

        .left-column .info-contacto ul {
            list-style: none;
            padding: 10px;
            margin-top: 5px;
            font-size: 14px;
        }

        .left-column .info-contacto ul li {
            margin-bottom: 12px;
        }

        .right-column {
            background-color: #f4f4f4;
        }

        .right-column .info-contacto {
            margin-top: 10px;
        }

        .right-column .info-contacto ul {
            list-style: none;
            padding: 10px;
            margin-top: 10px;
            font-size: 14px;
            background-color: #fff;
            border-radius: 10px;
        }

        .right-column .info-contacto ul li {
            margin: 10px 0;
        }

        .items {
            border: 1px solid #ccc;
            padding: 5px 10px;
            text-align: center;
            color: #003D5E;
            border-radius: 5px;
        }

        .declaracion {
            border-radius: 10px;
        }

        .declaracion li {
            padding: 5px 10px;
            background-color: #ECF8F9;
            border-radius: 10px;
        }

        .huella {
            position: relative;
            width: 105px;
            height: 130px;
            font-size: 10px;
            margin: 25px auto;
            border: 1px solid black;
        }

        .huella p {
            border-top: 1px solid black;
            position: absolute;
            font-size: 10px;
            bottom: 0;
            left: 0;
            margin: 0;
            padding: 0;
            padding: 5px 8px;
        }

        .footer {
            padding-top: 40px;
            height: 50px;
            vertical-align: bottom;
            padding-bottom: 10px;
        }

        .footer p {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <table class="container">
        <tr>
            <td class="azul"></td>
            <td class="celeste"></td>
            <td class="amarillo"></td>
            <td class="plomo"></td>
        </tr>
        <tr>
            <td class="logo_unprg" colspan="1" align="center">
                <img src="{{ $base64ImageLogoUnprg }}" alt="LOGO_UNPRG" width="80" height="auto">
            </td>
            <td class="encabezado" colspan="2" align="center">
                <h2>Universidad Nacional Pedro Ruiz Gallo</h2>
                <div style="line-height: 1.5; margin-top: 12px;">
                    <p>FICHA DE INSCRIPCIÓN DEL
                        POSTULANTE</p>
                    <p>EXAMEN DE ADMISIÓN {{ $process }}</p>
                </div>
            </td>
            <td class="foto_perfil" colspan="1" align="center">
                <img src="{{ $base64ImagePostulante }}" alt="FOTO_POSTULANTE" width="118" height="154">
            </td>
        </tr>
        <tr>
            <td class="left-column" colspan="3">
                <div class="info-contacto">
                    <h5 class="items">DATOS PERSONALES</h5>
                    <ul>
                        <li style="display: inline-block; margin-right:160px"><b>DNI:</b>
                            {{ $postulante->num_documento }}
                        </li>
                        <li style="display: inline-block;"><b>Fecha de nacimiento:</b>
                            {{ $postulante->fecha_nacimiento }}</li>
                        <li><b>Nombres y Apellidos:</b> {{ $postulante->nombres }} {{ $postulante->ap_paterno }}
                            {{ $postulante->ap_materno }}</li>
                        <li><b>{{ $laberBirth }}:</b> {{ $lugarNacimiento }}</li>
                        <li><b>Lugar de residencia:</b> {{ $lugarResidencia }}</li>
                        <li><b>Dirección:</b> {{ $postulante->direccion }}</li>
                    </ul>
                </div>
                <div class="info-contacto">
                    <h5 class="items">INFORMACIÓN ACADÉMICA</h5>
                    <ul>
                        <li><b>Lugar de estudio:</b> {{ $lugarColegio }}</li>
                        <li><b>Colegio:</b> {{ $colegio }}</li>
                        <li style="display: inline-block; margin-right:100px"><b>Tipo de colegio:</b>
                            {{ $tipoColegio }}</li>
                        <li style="display: inline-block;"><b>Año de egreso:</b> {{ $postulante->anno_egreso }}</li>
                    </ul>
                </div>
                <div class="info-contacto">
                    <h5 class="items">DECLARACIÓN JURADA</h5>
                    <ul class="declaracion">
                        <li>Conozco, acepto y me someto a las bases, condiciones y procedimientos establecidos en el
                            Reglamento del
                            Concurso de Admisión , de la Universidad Nacional Pedro Ruiz Gallo.</li>
                        <li>La información y fotografia registrada es AUTÉNTICA y que las imágenes de mi DNI enviados
                            para
                            mi
                            inscripción como postulante al presente Concurso de Admisión, son copia fiel al original; en
                            caso de faltar
                            a la verdad, me someto a las sanciones correspondientes (Art.23 del Reglamento del presente
                            Concurso de
                            Admisión).</li>
                        <li>No tengo impedimento para participar en el Concurso de Admisión.</li>
                        <li>
                            De alcanzar una vacante, me comprometo a regularizar mi expediente en la fecha establecida
                            en el
                            cronograma
                            del presente Concurso de Admisión en caso contrario me someto a las sanciones
                            correspondientes
                            (Art.68 del
                            Reglamento del presente Concurso de Admisión).
                        </li>
                        <li>
                            He culminado el 5to año de Educación Secundaria antes del Concurso de Admisión
                            .
                        </li>
                    </ul>
                </div>
            </td>
            <td class="right-column">
                <div class="info-contacto">
                    <h5 class="items">INFORMACIÓN DE CONTACTO</h5>
                    <ul>
                        <li><b>Teléfono:</b> {{ $postulante->telefono }}</li>
                        <li><b>Teléfono de apoderado:</b> {{ $postulante->telefono_ap }}</li>
                        <li><b>Correo:</b> {{ $postulante->correo }}</li>
                    </ul>
                </div>
                <div class="info-contacto">
                    <h5 class="items">DATOS DE POSTULACIÓN</h5>
                    <ul>
                        <li><b>Sede:</b> {{ $sede }}</li>
                        <li><b>Modalidad:</b> {{ $modalidad }}</li>
                        <li><b>Programa Académico:</b> {{ $programaAcademico }}</li>
                        <li><b>N° Voucher:</b> {{ $postulante->num_voucher }}</li>
                    </ul>
                </div>
                <div class="info-contacto" align="center">
                    <h5 class="items" style="margin-bottom: 20px">IDENTIFICACIÓN</h5>
                    <img src="data:image/png;charset=utf-8;base64,{{ base64_encode(QrCode::encoding('UTF-8')->generate($resultadoQr)) }}"
                        alt="Código QR">
                    <div class="huella">
                        <p>INDICE DERECHO</p>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="footer" style="padding-left: 30px">
                <p>Lambayeque, {{ $today }}.</p>
            </td>
            <td colspan="1" class="footer" align="center" style="padding-right: 60px">
                <hr>
                <p>Firma de Postulante</p>
            </td>
        </tr>
    </table>
</body>

</html>
