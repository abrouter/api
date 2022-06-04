<?php
declare(strict_types=1);

namespace Modules\AbRouter\Repositories\Experiments;

use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Modules\AbRouter\Models\Experiments\Experiment;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\EntityId\EntityEncoder;

class ExperimentsRepository extends BaseRepository
{
    public function getExperimentsById(string $experimentId, int $owner): ?Experiment
    {
        try {
            $expInternalId = (new EntityEncoder())->decode($experimentId, 'experiments');
        } catch (\Throwable $e) {
            throw new NotFoundHttpException('Failed to find an experiment');
        }

        /**
         * @var Experiment $model
         */
        $model = $this->query()
            ->where('id', $expInternalId)
            ->where('owner_id', $owner)
            ->first();
        
        return $model;
    }

    public function getExperimentsByAlias(string $experimentAlias, int $owner): ?Experiment
    {
        /**
         * @var Experiment $model
         */
        $model = $this->query()
            ->where('alias', $experimentAlias)
            ->where('owner_id', $owner)
            ->first();

        if (empty($model)) {
            throw new NotFoundHttpException('Failed to find experiment with given uid');
        }
        
        return $model;
    }

    protected function getModel()
    {
        return new Experiment();
    }
}
