<?php
declare(strict_types=1);

namespace Modules\AbRouter\Services\Experiment;

use Modules\AbRouter\Repositories\Experiments\ExperimentsRepository;

class ExperimentIdResolver
{
    /**
     * @var ExperimentsRepository
     */
    private $experimentsRepository;

    public function __construct(ExperimentsRepository $experimentsRepository)
    {
        $this->experimentsRepository = $experimentsRepository;
    }

    public function getExperimentsByResolvedId(string $experimentId, int $owner)
    {
        $isExperimentId = preg_match(
            '/^([A-Z0-9]{8})(-){1}([A-Z0-9]{4})(-){1}([A-Z0-9]{4})(-){1}([A-Z0-9]{8})$/',
            $experimentId
        );

        if ($isExperimentId) {
            $experiment = $this
                ->experimentsRepository
                ->getExperimentsById(
                    $experimentId,
                    $owner
                );
        } else {
            $experiment = $this
                ->experimentsRepository
                ->getExperimentsByAlias(
                    $experimentId,
                    $owner
                );
        }

        return $experiment;
    }
}
