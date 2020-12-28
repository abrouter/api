<?php
declare(strict_types=1);

namespace Modules\ProxiedMail\Http\Transformers\ReceivedEmail;

use Modules\Core\Http\Transformers\BaseTransformer;
use Modules\ProxiedMail\Http\Requests\ReceivedEmailCreateRequest;
use Modules\ProxiedMail\Services\ReceivedEmail\DTO\ReceivedEmailDTO;

class ReceivedEmailTransformer extends BaseTransformer
{
    /**
     * @param ReceivedEmailCreateRequest $request
     * @return ReceivedEmailDTO
     */
    public function transform($request)
    {
        $recipient = trim($request->post('recipient'));
        $all = json_encode($request->all());
        if (empty($recipient)) {
            $all = $request->json()->all();
            $recipient = $all['recipient'];
            $all = json_encode($all);
        }

        return new ReceivedEmailDTO(
            $all,
            $recipient
        );
    }
}
