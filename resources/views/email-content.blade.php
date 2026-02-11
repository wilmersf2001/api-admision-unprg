<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Postulante - UNPRG</title>
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

    .header .process-number {
        font-size: 16px;
        margin-top: 10px;
        padding: 8px 15px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 5px;
        display: inline-block;
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

    .status-badge {
        text-align: center;
        padding: 20px;
        margin: 25px 0;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 0.5px;
    }

    .status-badge.approved {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: 3px solid #059669;
    }

    .status-badge.observed {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border: 3px solid #d97706;
    }

    .message-text {
        font-size: 15px;
        color: #444;
        line-height: 1.8;
        margin: 20px 0;
        text-align: justify;
    }

    .cta-button {
        display: inline-block;
        padding: 12px 30px;
        background: linear-gradient(135deg, #003d82 0%, #005eb8 100%);
        color: white !important;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        margin: 15px 0;
        transition: transform 0.2s;
        box-shadow: 0 3px 10px rgba(0, 61, 130, 0.3);
    }

    .cta-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 61, 130, 0.4);
    }

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

    .info-box strong {
        color: #003d82;
        font-size: 16px;
        display: block;
        margin-bottom: 12px;
    }

    .info-box.warning strong {
        color: #d97706;
    }

    .info-box ul {
        margin: 10px 0;
        padding-left: 20px;
    }

    .info-box li {
        margin: 10px 0;
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
        body {
            padding: 10px;
        }

        .content {
            padding: 25px 20px;
        }

        .header h1 {
            font-size: 18px;
        }

        .header h2 {
            font-size: 15px;
        }

        .status-badge {
            font-size: 16px;
            padding: 15px;
        }
    }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            <h1>UNIVERSIDAD NACIONAL PEDRO RUIZ GALLO</h1>
            <h2>DIRECCIÓN DE ADMISIÓN</h2>
            <div class="process-number">PROCESO DE ADMISIÓN {{ $processNumber }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="date-info">
                <strong>Fecha:</strong> {{ $today }}<br>
                <strong>Asunto:</strong> Confirmación de inscripción al Examen de Admisión {{ $processNumber }}<br>
                <strong>De:</strong> admision@unprg.edu.pe
            </div>

            <div class="greeting">
                @if ($sexo == 1)
                    Estimado Sr. Postulante: <span class="name">{{ $applicantName }}</span>
                @else
                    Estimada Srta. Postulante: <span class="name">{{ $applicantName }}</span>
                @endif
            </div>

            @if ($isValid)
                <!-- Aprobado -->
                <div class="status-badge approved">
                    ✓ ¡FELICITACIONES, SU INSCRIPCIÓN HA SIDO APROBADA!
                </div>

                <div class="message-text">
                    Nos complace informarle que su inscripción al proceso de admisión ha sido aprobada exitosamente.
                    Para continuar con el proceso, por favor acceda al siguiente enlace donde podrá imprimir su
                    constancia de inscripción:
                </div>

                <div style="text-align: center; margin: 25px 0;">
                    <a href="https://inscripciones.unprg.edu.pe/ficha-inscripcion" class="cta-button">
                        IMPRIMIR CONSTANCIA DE INSCRIPCIÓN
                    </a>
                </div>

                <div class="message-text">
                    A partir del día siguiente, podrá acercarse a la <strong>OFICINA DE TECNOLOGÍAS DE LA INFORMACIÓN</strong>,
                    ubicada en Ciudad Universitaria, donde canjeará la constancia por su carnet de postulante.
                </div>

                <div class="info-box">
                    <strong>📋 IMPORTANTE:</strong>
                    <ul>
                        <li><strong>NO FIRME NI COLOQUE SU HUELLA</strong> en la constancia de inscripción.
                            Esto se realizará al momento de la entrega del carnet.</li>
                        <li>Recuerde que su <strong>carnet de postulante y su DNI</strong> son los únicos documentos
                            de identificación válidos en el día del examen.</li>
                    </ul>
                </div>
            @else
                <!-- Observado -->
                <div class="status-badge observed">
                    ⚠ INSCRIPCIÓN OBSERVADA
                </div>

                <div class="message-text">
                    Se le comunica que su fotografía ha sido observada. Por favor, ingrese al siguiente enlace
                    para subsanar la observación y continuar con el proceso de inscripción:
                </div>

                <div style="text-align: center; margin: 25px 0;">
                    <a href="https://inscripciones.unprg.edu.pe/ficha-inscripcion" class="cta-button">
                        SUBSANAR OBSERVACIÓN
                    </a>
                </div>

                <div class="info-box warning">
                    <strong>📸 INDICACIONES PARA LA FOTOGRAFÍA:</strong>
                    <ul>
                        <li>La foto debe presentar <strong>fondo blanco</strong> y el ambiente debe estar iluminado
                            para destacar el rostro.</li>
                        <li>Enmarca tu cabeza completa y parte superior de los hombros.</li>
                        <li>Mantén una expresión facial natural y neutra. Evita sonrisas exageradas o gestos que
                            puedan distorsionar tu apariencia.</li>
                        <li>Asegúrate de que tus ojos estén abiertos y visibles. <strong>No utilizar gafas, anteojos
                            o algún otro tipo de accesorio</strong> en la cabeza o rostro.</li>
                        <li><strong>Recuerda:</strong> Tu fotografía será impresa en tu carnet de postulante.</li>
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
