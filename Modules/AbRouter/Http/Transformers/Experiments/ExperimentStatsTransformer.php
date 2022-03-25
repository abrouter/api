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
            $request->input('filter.tag'),
            $request->input('filter.date_from'),
            $request->input('filter.date_to'),
            $request->input('filter.experimentId')
        );
    }
}
