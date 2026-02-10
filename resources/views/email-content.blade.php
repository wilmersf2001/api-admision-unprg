<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Postulante</title>
</head>
<style>
    .container {
        border-right: 1px solid black;
        padding: 1rem;
    }
</style>

<body>
    <div class="container">
        <p>El {{ $today }}. Confirmacion de inscripción al Examen de Admisión {{ $processNumber }}
            admision@unprg.edu.pe escribió:</p>
        <div style="text-align: center;">
            <h1>UNIVERSIDAD NACIONAL PEDRO RUIZ GALLO</h1>
            <h1>DIRECCIÓN DE ADMISIÓN</h1>
            <h1>PROCESO DE ADMISIÓN {{ $processNumber }}</h1>
        </div>
        <br>
        <section>
            @if ($sexo == 1)
                <p>Sr. Postulante: <b>{{ $applicantName }}</b></p>
            @else
                <p>Srta. Postulante: <b>{{ $applicantName }}</b></p>
            @endif
        </section>
        @if ($isValid)
            <section>
                <div style="text-align: center;">
                    <strong>!FELICITACIONES, SU INSCRIPCIÓN HA SIDO APROBADA!</strong>
                </div>
                <br>
                Ingrese al siguiente link <a href="https://inscripciones.unprg.edu.pe/ficha-inscripcion">CLIC
                    AQUÍ</a>
                donde usted deberá acceder e imprimir su constancia de inscripción y a partir del día siguiente podrá
                acercarse
                a
                la OFICINA DE TECNOLOGÍAS DE LA INFORMACIÓN, Cuidad Universitaria, donde canjeará la constancia por su
                carnet
                de
                postulante.
            </section>
            <br>
            <br>
            <section>
                <strong>IMPORTANTE: </strong>
                <ul>
                    <li>NO FIRME NI COLOQUE SU HUELLA EN LA CONSTANCIA DE INSCRIPCIÓN (Esto se realizará al momento de
                        entrega del
                        carnet).</li>
                    <li>Recuerde que su carnet de postulante y su DNI son los únicos documentos de identificación en el
                        día del
                        examen.</li>
                </ul>
            </section>
        @else
            <section>
                <div style="text-align: center;"><strong>!INSCRIPCIÓN OBSERVADA!</strong></div><br>
                Se le comunica que su fotografía ha sido observada, por favor ingrese al siguiente link <a
                    href="https://inscripciones.unprg.edu.pe/ficha-inscripcion">CLIC AQUÍ</a>
                para poder subsanar la observación y pueda continuar con el proceso de inscripción.
            </section>
            <br>
            <br>
            <section>
                <strong>INDICACIONES: </strong>
                <ul>
                    <li>
                        La foto debe presentar fondo blanco y el ambiente debe estar iluminado para destacar el rostro.
                    </li>
                    <li>Enmarca tu cabeza completa y parte superior de los hombros.
                    </li>
                    <li>
                        Mantén una expresión facial natural y neutra. Evita sonrisas exageradas o gestos que puedan
                        distorsionar tu
                        apariencia.
                    </li>
                    <li>
                        Asegúrate de que tus ojos estén abiertos y visibles. No utilizar gafas o anteojos o algún otro
                        tipo de accesorio en la cabeza o rostro.
                    </li>
                    <li>
                        Recuerda: Tu fotografía sera impresa en tu carnet de postulante.
                    </li>
                </ul>
            </section>
        @endif
    </div>
</body>

</html>
