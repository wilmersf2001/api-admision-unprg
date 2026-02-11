<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMailRequest;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Jobs\SendEmailJob;
use App\Models\Postulant;
use App\Models\Process;
use Symfony\Component\HttpFoundation\Response;

class SendMailController extends Controller
{
    use ApiResponse, HandlesValidation;

    public function sendMail(SendMailRequest $request)
    {
        try {
            $idsPostulantes = $request->idsPostulantes;
            $postulanteState = $request->postulanteState;
            $isValidFiles = $request->isValidFiles;
            $processNumber = Process::getProcessNumber();
            $postulantes = Postulant::whereIn('id', $idsPostulantes)->get();

            foreach ($postulantes as $postulante) {
                $email = $postulante->correo;
                $destinatario = implode(' ', [
                    $postulante->nombres,
                    $postulante->ap_paterno,
                    $postulante->ap_materno
                ]);
                $sexo = $postulante->sexo_id;

                SendEmailJob::dispatch($email, $destinatario, $sexo, $isValidFiles, $processNumber);
            }

            Postulant::bulkUpdateStatus($idsPostulantes, $postulanteState);

            return $this->successResponse('Correos enviado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse('Error al enviar los correos', Response::HTTP_BAD_REQUEST);
        }
    }
}
