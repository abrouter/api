<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\AbRouter\Emails\AskFormEmail;
use Symfony\Component\HttpFoundation\Response;

class AskFormController
{
//    public function askForm()
//    {
//        return 'Hello!';
//    }
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function askForm(Request $request): JsonResponse
    {
        $name = $request['name'];
        $email = $request['email'];
        $message = $request['message'];

        Mail::to('kondratyuk.alinaa@gmail.com')->send(new AskFormEmail($name, $email, $message));

        return new JsonResponse(['status'=>'success'], Response::HTTP_OK);
    }
}
