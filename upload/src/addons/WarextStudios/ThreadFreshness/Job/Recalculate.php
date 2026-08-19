<?php

namespace WarextStudios\ThreadFreshness\Job;

use XF\Job\AbstractRebuildJob;

class Recalculate extends AbstractRebuildJob
{
    protected function getNextIds($start, $batch): array
    {
        $db = $this->app->db();

        return $db->fetchAllColumn(
            $db->limit(
                'SELECT thread_id FROM xf_wrxt_thread_freshness_state WHERE thread_id > ? ORDER BY thread_id',
                max(1, (int)$batch)
            ),
            (int)$start
        );
    }

    protected function rebuildById($id): void
    {
        $this->app->repository('WarextStudios\ThreadFreshness:ThreadFreshness')
            ->recalculateThreadSafely((int)$id);
    }

    protected function getStatusType(): \XF\Phrase
    {
        return \XF::phrase('threads');
    }
}
