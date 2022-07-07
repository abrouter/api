<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Experiments;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\Experiment\DTO\UserExperimentsDTO;
use Modules\Auth\Exposable\AuthDecorator;

class UserExperimentsTransformer
{
    /**
     * @var AuthDecorator $authDecorator
     */
    private $authDecorator;

    public function __construct(AuthDecorator $authDecorator)
    {
        $this->authDecorator = $authDecorator;
    }

    public function transform(Request $request): UserExperimentsDTO
    {
        return new UserExperimentsDTO(
            $this->authDecorator->get()->getId(),
            $request->getAttribute('user_signature'),
            $request->input('data.relationships.experiments.data.id'),
            $request->input('data.relationships.branches.data.id')
        );
    }
}
