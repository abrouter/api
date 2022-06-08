<?php

namespace Modules\AbRouter\Services\Events\Stats;

use Illuminate\Support\Collection;
use Modules\AbRouter\Models\Events\Event;
use Modules\AbRouter\Models\RelatedUsers\RelatedUser;
use Modules\Core\Interfaces\Stats\Stats;

class ReferrerStats implements Stats
{
    /**
     * @param Collection $eventsList
     * @param array $uniqUsers
     * @param array $allDisplayEvents
     * @param bool $enableDateCounter
     * @return array
     */
    public function getCounters(
        Collection $eventsList,
        array $uniqUsers,
        array $allDisplayEvents,
        bool $enableDateCounter = false
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

            if ($enableDateCounter) {
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

    /**
     * @param array $referrers
     * @param array $referrerCounters
     * @param int $uniqUsersCount
     * @return array
     */
    public function getPercentages(
        array $referrers,
        array $referrerCounters,
        int $uniqUsersCount
    ): array {
        $referrerPercentage = [];

        foreach ($referrers as $referrer) {
            if (!isset($referrerCounters[$referrer])) {
                $referrerPercentage[$referrer] = 0;
                continue;
            }

            $counter = $referrerCounters[$referrer];

            if ($uniqUsersCount === 0) {
                $referrerPercentage[$referrer] = 0;
                continue;
            }

            $referrerPercentage[$referrer] = intval(($counter / $uniqUsersCount) * 100);
        }

        return $referrerPercentage;
    }
}
