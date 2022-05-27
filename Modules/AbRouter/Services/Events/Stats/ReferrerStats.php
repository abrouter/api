<?php

namespace Modules\AbRouter\Services\Events\Stats;

use Illuminate\Support\Collection;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\Core\Interfaces\Stats\Stats;

class ReferrerStats implements Stats
{
    public function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        Collection $allDisplayEvents,
        bool $date = false
    ): array {
        $uniqUsers = array_flip($uniqUsers);
        $referrerCounters = [];
        $userEventAdded = [];

        foreach ($eventsList as $event) {
            /**
             * @var Event $event
             */

            preg_match(
                '/((http|https):\/\/([\w.]+\/?))|()/',
                $event->referrer,
                $matches
            );

            if (!$matches) {
                continue;
            }

            $referrer = $matches[0] === '' ? 'direct' : $matches[0];

            $relatedUsersIds = $event
                ->relatedUsers
                ->reduce(function (array $acc, RelatedUser $relatedUser) {
                    if (empty($relatedUser->related_user_id)) {
                        return $acc;
                    }

                    $acc[] = $relatedUser->related_user_id;
                    return $acc;
                }, []);

            $userEventKey = $referrer . '_' . $event->user_id;

            if (!empty($event->user_id) && isset($userEventAdded[$userEventKey])) {
                continue;
            }

            sort($relatedUsersIds);
            if (!empty($relatedUsersIds)) {
                $relatedUsersKey = $referrer . '_' . join('_', $relatedUsersIds);
            } else {
                $relatedUsersKey = '';
            }

            if (!empty($relatedUsersKey) && isset($userEventAdded[$relatedUsersKey])) {
                continue;
            }

            $hasUserInRelated = false;
            foreach ($relatedUsersIds as $relatedUsersId) {
                if (isset($uniqUsers[$relatedUsersId])) {
                    $hasUserInRelated = true;
                    break;
                }
            }

            if (!$hasUserInRelated && !isset($uniqUsers[$event->user_id])) {
                continue;
            }

            if ($hasUserInRelated) {
                $userEventAdded[$relatedUsersKey] = true;
            }
            if (isset($uniqUsers[$event->user_id])) {
                $userEventAdded[$userEventKey] = true;
            }

            if ($allDisplayEvents) {
                $convertDate = $event->created_at->format('Y-m-d');

                if (!isset($referrerCounters[$referrer][$convertDate])) {
                    $referrerCounters[$referrer][$convertDate] = 0;
                }

                $referrerCounters[$referrer][$convertDate] ++;

                continue;
            }

            if (!isset($referrerCounters[$referrer])) {
                $referrerCounters[$referrer] = 0;
            }

            $referrerCounters[$referrer] ++;
        }

        return $referrerCounters;
    }

    public function getPercentages(Collection $referrers, array $referrerCounters, int $uniqUsersCount): array
    {
        $eventPercentage = [];
        foreach ($referrers as $referrer) {
            if (!isset($referrerCounters[$referrer])) {
                $eventPercentage[$referrer] = 0;
                continue;
            }

            $counter = $referrerCounters[$referrer];

            if ($uniqUsersCount === 0) {
                $eventPercentage[$referrer] = 0;
                continue;
            }
            var_dump($referrerCounters);
            $eventPercentage[$referrer] = intval(($counter / $uniqUsersCount) * 100);
        }

        return $eventPercentage;
    }
}
