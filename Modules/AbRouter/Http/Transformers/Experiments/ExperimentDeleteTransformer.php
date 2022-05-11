<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Experiments;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\Experiment\DTO\ExperimentDeleteDTO;

class ExperimentDeleteTransformer
{
    public function __construct()
    {
    }

    public function transform(Request $request)
    {
        $experiment = new ExperimentDeleteDTO(
            $request->input('data.id')
        );

        return $experiment;
    }
}
