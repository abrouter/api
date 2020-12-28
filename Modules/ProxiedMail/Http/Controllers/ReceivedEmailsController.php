<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\ProxiedMail\Http\Requests\ReceivedEmailCreateRequest;
use Modules\ProxiedMail\Http\Resources\ReceivedEmail\ReceivedEmailResource;
use Modules\ProxiedMail\Http\Transformers\ReceivedEmail\ReceivedEmailTransformer;
use Modules\ProxiedMail\Services\ReceivedEmail\CreatorService;

class ReceivedEmailsController extends Controller
{
    public function create(
        ReceivedEmailCreateRequest $request,
        ReceivedEmailTransformer $receivedEmailTransformer,
        CreatorService $creatorService
    ): ReceivedEmailResource {
        $dto = $receivedEmailTransformer->transform($request);
        $receivedEmail = $creatorService->create($dto);

        return new ReceivedEmailResource($receivedEmail);
    }
}
