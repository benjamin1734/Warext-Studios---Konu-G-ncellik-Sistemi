<?php

namespace WarextStudios\ThreadFreshness\Repository;

use WarextStudios\ThreadFreshness\Entity\ThreadState;
use WarextStudios\ThreadFreshness\Util\ModeratorStatus;
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

    public function withThreadLock(int $threadId, callable $callback)
    {
        $db = $this->db();
        $db->beginTransaction();

        try
        {
            $db->query(
                'INSERT IGNORE INTO xf_wrxt_thread_freshness_state (thread_id) VALUES (?)',
                $threadId
            );
            $db->query(
                'SELECT thread_id FROM xf_wrxt_thread_freshness_state WHERE thread_id = ? FOR UPDATE',
                $threadId
            );

            $result = $callback();
            $db->commit();

            return $result;
        }
        catch (\Throwable $e)
        {
            $db->rollback();
            throw $e;
        }
    }

    public function preloadStatesForThreads($threads, $stickyThreads = null): void
    {
        $ids = [];

        foreach ([$threads, $stickyThreads] as $collection)
        {
            if (!$collection)
            {
                continue;
            }

            foreach ($collection as $thread)
            {
                $threadId = (int)$thread->thread_id;
                if ($threadId > 0)
                {
                    $ids[$threadId] = $threadId;
                }
            }
        }

        if ($ids)
        {
            $this->finder('WarextStudios\ThreadFreshness:ThreadState')
                ->where('thread_id', array_values($ids))
                ->fetch();
        }
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

    public function getEffectiveStatus(?ThreadState $state): string
    {
        if (!$state)
        {
            return 'unverified';
        }

        return ModeratorStatus::effective((string)$state->status, (string)$state->moderator_status);
    }

    public function logStatusChange(
        int $threadId,
        string $oldStatus,
        string $newStatus,
        string $triggerType,
        int $userId = 0
    ): void
    {
        if ($oldStatus === $newStatus)
        {
            return;
        }

        $log = $this->em->create('WarextStudios\ThreadFreshness:StatusLog');
        $log->thread_id = $threadId;
        $log->old_status = $oldStatus;
        $log->new_status = $newStatus;
        $log->trigger_type = $triggerType;
        $log->user_id = $userId;
        $log->save();
    }

    public function notifyStatusChange(
        int $threadId,
        string $oldStatus,
        string $newStatus,
        string $triggerType,
        int $userId = 0
    ): void
    {
        $this->app->service(
            'WarextStudios\ThreadFreshness:ThreadFreshness\Notifier'
        )->notify($threadId, $oldStatus, $newStatus, $triggerType, $userId);
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
        $oldStatus = $this->getEffectiveStatus($state);
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

        $newStatus = $this->getEffectiveStatus($state);
        $this->logStatusChange($threadId, $oldStatus, $newStatus, $triggerType, $userId);
        $this->notifyStatusChange($threadId, $oldStatus, $newStatus, $triggerType, $userId);

        return $state;
    }

    public function recalculateThreadSafely(
        int $threadId,
        string $triggerType = 'system',
        int $userId = 0
    ): ThreadState
    {
        return $this->withThreadLock(
            $threadId,
            fn() => $this->recalculateThread($threadId, $triggerType, $userId)
        );
    }

    public function cleanupOrphans(int $limit = 1000): void
    {
        $limit = max(1, min(5000, $limit));
        $db = $this->db();

        $voteIds = $db->fetchAllColumn(
            $db->limit(
                'SELECT v.vote_id
                FROM xf_wrxt_thread_freshness_vote v
                LEFT JOIN xf_thread t ON t.thread_id = v.thread_id
                LEFT JOIN xf_user u ON u.user_id = v.user_id
                WHERE t.thread_id IS NULL OR u.user_id IS NULL
                ORDER BY v.vote_id',
                $limit
            )
        );

        if ($voteIds)
        {
            $db->delete(
                'xf_wrxt_thread_freshness_vote',
                'vote_id IN (' . $db->quote($voteIds) . ')'
            );
        }

        $logIds = $db->fetchAllColumn(
            $db->limit(
                'SELECT l.log_id
                FROM xf_wrxt_thread_freshness_log l
                LEFT JOIN xf_thread t ON t.thread_id = l.thread_id
                WHERE t.thread_id IS NULL
                ORDER BY l.log_id',
                $limit
            )
        );

        if ($logIds)
        {
            $db->delete(
                'xf_wrxt_thread_freshness_log',
                'log_id IN (' . $db->quote($logIds) . ')'
            );
        }

        $threadIds = $db->fetchAllColumn(
            $db->limit(
                'SELECT s.thread_id
                FROM xf_wrxt_thread_freshness_state s
                LEFT JOIN xf_thread t ON t.thread_id = s.thread_id
                WHERE t.thread_id IS NULL
                ORDER BY s.thread_id',
                $limit
            )
        );

        if ($threadIds)
        {
            $db->delete(
                'xf_wrxt_thread_freshness_state',
                'thread_id IN (' . $db->quote($threadIds) . ')'
            );
        }
    }
}
