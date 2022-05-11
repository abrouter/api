<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\FeatureToggles;

use Illuminate\Http\Request;
use Modules\AbRouter\Models\Experiments\ExperimentBranches;
use Modules\AbRouter\Services\Experiment\DTO\BranchDTO;
use Modules\AbRouter\Services\Experiment\DTO\ExperimentDTO;

class FeatureToggleCreateTransformer
{
    public function __construct()
    {
    }

    public function transform(Request $request)
    {
        $branches = array_reduce($request->input('included', []), function (array $acc, array $branch) {
            if ($branch['type'] !== ExperimentBranches::getType()) {
                return $acc;
            }

            $acc[] = new BranchDTO(
                $branch['id'] ?? '',
                $branch['attributes']['uid'],
                (int) $branch['attributes']['percent'],
                $branch['relationships']['owner']['data']['id'],
                $branch['attributes']['config']
            );

            return $acc;
        }, []);

        $experiment = new ExperimentDTO(
            $request->input('data.id'),
            $request->input('data.attributes.name'),
            $request->input('data.attributes.is_enabled'),
            $request->input('data.attributes.is_feature_toggle'),
            $request->input('data.attributes.config', []),
            $request->input('data.relationships.owner.data.id', []),
            ...$branches
        );

        return $experiment;
    }
}
