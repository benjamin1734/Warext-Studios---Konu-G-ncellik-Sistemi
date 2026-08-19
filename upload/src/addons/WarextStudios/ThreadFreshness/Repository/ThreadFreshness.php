<?php

namespace WarextStudios\ThreadFreshness\Repository;

use WarextStudios\ThreadFreshness\Entity\ThreadState;
use WarextStudios\ThreadFreshness\Util\StatusCalculator;
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

    public function recalculateThread(int $threadId, string $triggerType = 'system', int $userId = 0): ThreadState
    {
        $votes = [];
        foreach ($this->findVotesForThread($threadId)->fetch() as $vote)
        {
            $votes[] = [
                'vote' => $vote->vote,
                'vote_date' => $vote->vote_date
            ];
        }

        $result = StatusCalculator::calculate($votes, \XF::$time);
        $state = $this->getOrCreateState($threadId);
        $oldStatus = $state->exists() ? $state->status : '';

        $state->bulkSet($result);
        $state->last_calculated_date = \XF::$time;
        if (in_array($result['status'], ['current', 'likely_current'], true))
        {
            $state->last_verified_date = \XF::$time;
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
