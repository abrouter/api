<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\Experiments;

use Illuminate\Http\Request;
use Modules\AbRouter\Services\Events\DTO\StatsQueryDTO;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\Core\EntityId\EntityEncoder;

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
        $userId = $this->authDecorator->get()->getId()
            ?? (new EntityEncoder())->decode($request->get('userId'), 'users');

        return new StatsQueryDTO(
            $userId,
            $request->input('filter.tag'),
            $request->input('filter.date_from'),
            $request->input('filter.date_to'),
            $request->input('filter.experimentId')
        );
    }
}
