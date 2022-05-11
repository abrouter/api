<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Experiments;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\Experiment\DTO\AddUserToExperimentDTO;
use Modules\Auth\Exposable\AuthDecorator;

class AddUserToExperimentTransformer
{
    /**
     * @var AuthDecorator $authDecorator
     */
    private $authDecorator;

    public function __construct(AuthDecorator $authDecorator)
    {
        $this->authDecorator = $authDecorator;
    }

    public function transform(Request $request): AddUserToExperimentDTO
    {
        return new AddUserToExperimentDTO(
            $this->authDecorator->get()->getId(),
            $request->getAttribute('user_signature'),
            $request->input('data.relationships.experiments.data.id'),
            $request->input('data.relationships.branches.data.id')
        );
    }
}
