<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Experiments;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Core\EntityId\EntityEncoder;

class ExperimentBranchStatsTransformer
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
            $request->input('filter.date_from'),
            $request->input('filter.date_to'),
            (new EntityEncoder())->decode($request->input('filter.experimentBranchId'), 'experiment_branches')
        );
    }
}
