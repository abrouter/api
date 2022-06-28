<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Modules\AbRouter\Emails\AskFormEmail;
use Modules\AbRouter\Http\Requests\AskFormRequest;
use Symfony\Component\HttpFoundation\Response;

class AskFormController
{
    public function askForm(AskFormRequest $request): JsonResponse
    {
        $name = $request['name'];
        $email = $request['email'];
        $message = $request['message'];

        Mail::to('abrouter@proxiedmail.com')->send(new AskFormEmail($name, $email, $message));

        return new JsonResponse(['status'=>'success'], Response::HTTP_OK);
    }
}
