<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Solicitud de Actualización - UNPRG</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        padding: 20px;
        line-height: 1.6;
    }

    .email-wrapper {
        max-width: 700px;
        margin: 0 auto;
        background-color: #ffffff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .header {
        background: linear-gradient(135deg, #003d82 0%, #005eb8 100%);
        color: #ffffff;
        padding: 40px 20px;
        text-align: center;
    }

    .header h1 {
        font-size: 22px;
        font-weight: 600;
        margin: 8px 0;
        letter-spacing: 0.5px;
    }

    .header h2 {
        font-size: 18px;
        font-weight: 500;
        margin: 5px 0;
    }

    .content {
        padding: 35px 30px;
    }

    .date-info {
        color: #666;
        font-size: 13px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e0e0e0;
    }

    .greeting {
        font-size: 16px;
        color: #333;
        margin-bottom: 25px;
    }

    .greeting .name {
        color: #003d82;
        font-weight: 600;
    }

    .message-text {
        font-size: 15px;
        color: #444;
        line-height: 1.8;
        margin: 20px 0;
        text-align: justify;
    }

    /* Aprobado */
    .code-box {
        text-align: center;
        background: linear-gradient(135deg, #f0f4ff 0%, #e6eeff 100%);
        border: 2px dashed #003d82;
        border-radius: 10px;
        padding: 30px 20px;
        margin: 30px 0;
    }

    .code-box .code-label {
        font-size: 13px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }

    .code-box .code-value {
        font-size: 26px;
        font-weight: 700;
        color: #003d82;
        letter-spacing: 4px;
        font-family: 'Courier New', Courier, monospace;
        word-break: break-all;
    }

    .code-box .code-expiry {
        font-size: 13px;
        color: #d97706;
        margin-top: 12px;
        font-weight: 500;
    }

    /* Rechazado */
    .rejected-box {
        text-align: center;
        background: linear-gradient(135deg, #fff0f0 0%, #ffe6e6 100%);
        border: 2px dashed #dc2626;
        border-radius: 10px;
        padding: 30px 20px;
        margin: 30px 0;
    }

    .rejected-box .rejected-label {
        font-size: 14px;
        color: #dc2626;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .rejected-box .rejected-reason {
        font-size: 15px;
        color: #555;
        line-height: 1.7;
        font-style: italic;
    }

    /* Info boxes */
    .info-box {
        background-color: #f8f9fa;
        border-left: 4px solid #003d82;
        padding: 20px;
        margin: 25px 0;
        border-radius: 5px;
    }

    .info-box.warning {
        border-left-color: #f59e0b;
        background-color: #fffbeb;
    }

    .info-box.danger {
        border-left-color: #dc2626;
        background-color: #fff5f5;
    }

    .info-box strong {
        color: #003d82;
        font-size: 15px;
        display: block;
        margin-bottom: 12px;
    }

    .info-box.warning strong {
        color: #d97706;
    }

    .info-box.danger strong {
        color: #dc2626;
    }

    .info-box ul {
        margin: 10px 0;
        padding-left: 20px;
    }

    .info-box li {
        margin: 8px 0;
        color: #555;
        line-height: 1.6;
    }

    .footer {
        background-color: #f8f9fa;
        padding: 25px 30px;
        text-align: center;
        color: #666;
        font-size: 13px;
        border-top: 1px solid #e0e0e0;
    }

    .footer p {
        margin: 8px 0;
    }

    .footer a {
        color: #003d82;
        text-decoration: none;
        font-weight: 600;
    }

    @media only screen and (max-width: 600px) {
        body { padding: 10px; }
        .content { padding: 25px 20px; }
        .header h1 { font-size: 18px; }
        .header h2 { font-size: 15px; }
        .code-box .code-value { font-size: 18px; letter-spacing: 2px; }
    }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            <h1>UNIVERSIDAD NACIONAL PEDRO RUIZ GALLO</h1>
            <h2>DIRECCIÓN DE ADMISIÓN</h2>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="date-info">
                <strong>Fecha:</strong> {{ $today }}<br>
                <strong>Asunto:</strong>
                @if ($status === 'approved')
                    Solicitud de actualización aprobada - Código de acceso único<br>
                @else
                    Solicitud de actualización rechazada<br>
                @endif
                <strong>De:</strong> admision@unprg.edu.pe
            </div>

            <div class="greeting">
                @if ($sexo == 1)
                    Estimado Sr. Postulante: <span class="name">{{ $applicantName }}</span>
                @else
                    Estimada Srta. Postulante: <span class="name">{{ $applicantName }}</span>
                @endif
            </div>

            @if ($status === 'approved')
                {{-- SECCIÓN APROBADO --}}
                <div class="message-text">
                    Su solicitud de actualización de datos ha sido <strong>revisada y aprobada</strong> por la
                    Dirección de Admisión. A continuación encontrará su <strong>código único de acceso</strong>,
                    el cual le permitirá ingresar al portal y actualizar su información personal.
                </div>

                <div class="code-box">
                    <div class="code-label">Su código de acceso único</div>
                    <div class="code-value">{{ $uniqueCode }}</div>
                    <div class="code-expiry">⏳ Válido hasta: {{ $expiresAt }}</div>
                </div>

                <div class="info-box">
                    <strong>📋 PASOS PARA ACTUALIZAR SUS DATOS:</strong>
                    <ul>
                        <li>Ingrese al portal de admisión y seleccione la opción <strong>"Actualizar datos"</strong>.</li>
                        <li>Ingrese su <strong>número de documento</strong>, <strong>número de voucher</strong> y el <strong>código único</strong> recibido en este correo.</li>
                        <li>Realice los cambios necesarios en su información y confirme la actualización.</li>
                        <li>Sus cambios serán aplicados de forma inmediata al usar el código.</li>
                    </ul>
                </div>

                <div class="info-box warning">
                    <strong>⚠ IMPORTANTE:</strong>
                    <ul>
                        <li>Este código es de <strong>uso único</strong>. Una vez utilizado quedará invalidado.</li>
                        <li>El código expira el <strong>{{ $expiresAt }}</strong>. Pasada esta fecha deberá realizar una nueva solicitud.</li>
                        <li>Si usted no realizó esta solicitud, comuníquese de inmediato con la Dirección de Admisión.</li>
                    </ul>
                </div>

            @else
                {{-- SECCIÓN RECHAZADO --}}
                <div class="message-text">
                    Lamentamos informarle que su solicitud de actualización de datos ha sido
                    <strong>revisada y no ha podido ser aprobada</strong> por la Dirección de Admisión.
                </div>

                <div class="rejected-box">
                    <div class="rejected-label">✗ Solicitud Rechazada</div>
                    <div class="rejected-reason">
                        <strong>Motivo:</strong> {{ $note }}
                    </div>
                </div>

                <div class="info-box danger">
                    <strong>ℹ ¿QUÉ PUEDE HACER AHORA?</strong>
                    <ul>
                        <li>Revise el motivo del rechazo detallado anteriormente.</li>
                        <li>Si considera que hubo un error, comuníquese con la Dirección de Admisión adjuntando su documentación.</li>
                        <li>Puede realizar una nueva solicitud subsanando las observaciones indicadas.</li>
                    </ul>
                </div>

                <div class="info-box warning">
                    <strong>⚠ IMPORTANTE:</strong>
                    <ul>
                        <li>Este rechazo no afecta su inscripción como postulante.</li>
                        <li>Para consultas, comuníquese directamente con la Dirección de Admisión.</li>
                    </ul>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Universidad Nacional Pedro Ruiz Gallo</strong></p>
            <p>Dirección de Admisión | Ciudad Universitaria</p>
            <p>📧 <a href="mailto:admision@unprg.edu.pe">admision@unprg.edu.pe</a></p>
            <p>🌐 <a href="https://www.unprg.edu.pe" target="_blank">www.unprg.edu.pe</a></p>
        </div>
    </div>
</body>

</html>