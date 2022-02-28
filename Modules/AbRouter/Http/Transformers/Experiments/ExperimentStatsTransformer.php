<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Experiments;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\Auth\Exposable\AuthDecorator;

class ExperimentStatsTransformer
{
    /**
     * @var AuthDecorator $authDecorator
     */
    private $authDecorator;

    public function __construct(AuthDecorator $authDecorator)
    {
        $this->authDecorator = $authDecorator;
    }

    public function transform(Request $request): StatsQueryDTO
    {
        return new StatsQueryDTO(
            $this->authDecorator->get()->getId(),
            null,
            null,
            null,
            $request->input('filter.experimentId')
        );
    }
}
