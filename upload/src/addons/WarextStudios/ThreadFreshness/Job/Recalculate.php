<?php

namespace WarextStudios\ThreadFreshness\Job;

use XF\Job\AbstractRebuildJob;

class Recalculate extends AbstractRebuildJob
{
    protected function getNextIds($start, $batch): array
    {
        return $this->app->db()->fetchAllColumn(
            'SELECT thread_id FROM xf_wrxt_thread_freshness_state WHERE thread_id > ? ORDER BY thread_id LIMIT ' . (int)$batch,
            $start
        );
    }

    protected function rebuildById($id): void
    {
        $this->app->repository('WarextStudios\ThreadFreshness:ThreadFreshness')->recalculateThread((int)$id);
    }

    protected function getStatusType(): string
    {
        return 'wrxt_thread_freshness';
    }
}
