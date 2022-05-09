<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Experiments;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\Experiment\DTO\SimpleRunDTO;

class SimpleRunTransformer
{
    public function transform(Request $request): SimpleRunDTO
    {
        $experimentId = $request->input('experimentId');
        $userId = $request->input('userId');
        $token = $request->input('token');

        return new SimpleRunDTO($experimentId, $token, $userId);
    }
}

