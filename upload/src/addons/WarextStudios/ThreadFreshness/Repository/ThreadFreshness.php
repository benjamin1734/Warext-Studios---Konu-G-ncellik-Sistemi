<?php

namespace WarextStudios\ThreadFreshness\Repository;

use WarextStudios\ThreadFreshness\Entity\ThreadState;
use WarextStudios\ThreadFreshness\Util\Revalidation;
use WarextStudios\ThreadFreshness\Util\StatusCalculator;
use WarextStudios\ThreadFreshness\Util\VersionSummary;
use XF\Mvc\Entity\Repository;

class ThreadFreshness extends Repository
{
    public function findVotesForThread(int $threadId)
    {
        return $this->finder('WarextStudios\ThreadFreshness:Vote')
            ->where('thread_id', $threadId)
            ->order('vote_date', 'DESC');
    }

    public function findStateForThread(int $threadId): ?ThreadState
    {
        return $this->em->find('WarextStudios\ThreadFreshness:ThreadState', $threadId);
    }

    public function getOrCreateState(int $threadId): ThreadState
    {
        $state = $this->findStateForThread($threadId);
        if ($state)
        {
            return $state;
        }

        $state = $this->em->create('WarextStudios\ThreadFreshness:ThreadState');
        $state->thread_id = $threadId;
        return $state;
    }

    public function getVersionSummaryForThread(int $threadId, int $limit = 8): array
    {
        $votes = [];

        foreach ($this->findVotesForThread($threadId)->fetch() as $vote)
        {
            $votes[] = [
                'vote' => (int)$vote->vote,
                'vote_date' => max((int)$vote->vote_date, (int)$vote->updated_date),
                'version' => (string)$vote->version
            ];
        }

        return VersionSummary::summarize($votes, \XF::$time, $limit);
    }

    public function recalculateThread(int $threadId, string $triggerType = 'system', int $userId = 0): ThreadState
    {
        $votes = [];
        foreach ($this->findVotesForThread($threadId)->fetch() as $vote)
        {
            $votes[] = [
                'vote' => $vote->vote,
                'vote_date' => max((int)$vote->vote_date, (int)$vote->updated_date)
            ];
        }

        $result = StatusCalculator::calculate($votes, \XF::$time);
        $state = $this->getOrCreateState($threadId);
        $oldStatus = $state->exists() ? $state->status : '';
        $lastVerifiedDate = (int)$state->last_verified_date;
        $revalidateDays = max(1, (int)(\XF::options()->wrxtFreshnessRevalidateDays ?? 180));

        if (Revalidation::shouldRevalidate(
            $result['status'],
            $lastVerifiedDate,
            (int)$result['last_vote_date'],
            $revalidateDays,
            \XF::$time
        ))
        {
            $result['status'] = 'revalidating';
        }

        $state->bulkSet($result);
        $state->last_calculated_date = \XF::$time;

        if (
            in_array($result['status'], ['current', 'likely_current'], true)
            && $result['last_vote_date'] > $lastVerifiedDate
        )
        {
            $state->last_verified_date = $result['last_vote_date'];
        }

        $state->save();

        if ($oldStatus !== $state->status)
        {
            $log = $this->em->create('WarextStudios\ThreadFreshness:StatusLog');
            $log->thread_id = $threadId;
            $log->old_status = $oldStatus;
            $log->new_status = $state->status;
            $log->trigger_type = $triggerType;
            $log->user_id = $userId;
            $log->save();
        }

        return $state;
    }
}
