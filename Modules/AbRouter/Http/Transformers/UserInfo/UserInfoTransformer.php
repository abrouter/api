<?php
declare(strict_types=1);

namespace Modules\AbRouter\Http\Transformers\UserInfo;

use Illuminate\Support\Collection;
use Modules\AbRouter\Entities\UserInfoEntity;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\Experiments\ExperimentBranchUser;
use Modules\Core\EntityId\EntityEncoder;

class UserInfoTransformer
{
    public function transform(?Event $userInfo,  Collection $experimentsIds): UserInfoEntity
    {
        $meta = !empty($userInfo->meta) ? (array) json_decode($userInfo->meta) : null;
        $encodeExperimentsIds = $experimentsIds
            ->reduce(function (array $acc, ExperimentBranchUser $experimentBranchUser) {
                $acc[] = (new EntityEncoder())
                    ->encode(
                        $experimentBranchUser->experiment_id, 'experiments'
                    );

                return $acc;
            }, []);

        return new UserInfoEntity(
            $encodeExperimentsIds,
            $userInfo->created_at ?? null,
            $meta['browser'] ?? null,
            $meta['platform'] ?? null,
            $meta['country_name'] ?? null
        );
    }
}
