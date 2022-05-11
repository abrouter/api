<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Experiments;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\Experiment\DTO\RunExperimentDTO;
use Modules\Auth\Exposable\AuthDecorator;

class RunExperimentTransformer
{
    /**
     * @var AuthDecorator $authDecorator
     */
    private $authDecorator;

    public function __construct(AuthDecorator $authDecorator)
    {
        $this->authDecorator = $authDecorator;
    }

    public function transform(Request $request): RunExperimentDTO
    {
        return new RunExperimentDTO(
            $this->authDecorator->get()->getId(),
            $request->input('data.attributes.userSignature'),
            $request->input('data.relationships.experiment.data.id')
        );
    }
}
