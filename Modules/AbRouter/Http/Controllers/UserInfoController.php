<?php
declare(strict_types = 1);

namespace Modules\AbRouter\Http\Controllers;

use AbRouter\JsonApiFormatter\DataSource\DataProviders\SimpleDataProvider;
use Modules\AbRouter\Http\Transformers\UserInfo\UserInfoTransformer;
use Modules\Auth\Exposable\AuthDecorator;
use Modules\AbRouter\Repositories\Experiments\ExperimentBranchUserRepository;
use Modules\AbRouter\Repositories\Events\UserEventsRepository;
use Modules\AbRouter\Http\Resources2\UserInfo\UserInfoScheme;

class UserInfoController
{
    /**
     * @var AuthDecorator
     */
    private $authDecorator;

    public function __construct(
        AuthDecorator $authDecorator
    ) {
        $this->authDecorator = $authDecorator;
    }

    public function __invoke(
        ExperimentBranchUserRepository $branchUserRepository,
        UserEventsRepository $eventsRepository,
        UserInfoTransformer $userInfoTransformer,
        string $userId
    ) {
        $ownerId = $this->authDecorator->get()->getId();
        $experimentsIds = $branchUserRepository->getExperimentsIdByUserSignatureAndOwner($userId, $ownerId);
        $userInfo = $eventsRepository->getUserInfoWithUserIdByOwnerId($userId, $ownerId);
        $userInfoTransformer = $userInfoTransformer->transform($userInfo, $experimentsIds);

        return new UserInfoScheme(
            new SimpleDataProvider($userInfoTransformer)
        );
    }
}
