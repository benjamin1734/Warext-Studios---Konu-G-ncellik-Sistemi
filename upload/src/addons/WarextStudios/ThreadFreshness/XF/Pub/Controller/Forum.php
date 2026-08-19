<?php

namespace WarextStudios\ThreadFreshness\XF\Pub\Controller;

use XF\Entity\Forum as ForumEntity;
use XF\Finder\Thread as ThreadFinder;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\View;

class Forum extends XFCP_Forum
{
    public function actionForum(ParameterBag $params)
    {
        $reply = parent::actionForum($params);

        if ($reply instanceof View)
        {
            $forum = $reply->getParam('forum');
            if ($forum && $forum->wrxt_freshness_enabled)
            {
                $this->repository('WarextStudios\ThreadFreshness:ThreadFreshness')->preloadStatesForThreads(
                    $reply->getParam('threads'),
                    $reply->getParam('stickyThreads')
                );
            }
        }

        return $reply;
    }

    protected function getForumFilterInput(ForumEntity $forum)
    {
        $filters = parent::getForumFilterInput($forum);

        if ($forum->wrxt_freshness_enabled)
        {
            $status = trim($this->filter('freshness_status', 'str'));
            if (in_array($status, [
                'current',
                'likely_current',
                'mixed',
                'questionable',
                'not_working',
                'revalidating',
                'unverified'
            ], true))
            {
                $filters['freshness_status'] = $status;
            }
        }

        return $filters;
    }

    protected function applyForumFilters(ForumEntity $forum, ThreadFinder $threadFinder, array $filters)
    {
        parent::applyForumFilters($forum, $threadFinder, $filters);

        if (!$forum->wrxt_freshness_enabled || empty($filters['freshness_status']))
        {
            return;
        }

        $status = $filters['freshness_status'];
        $threadFinder->with('WrxtFreshnessState', false);

        if ($status === 'unverified')
        {
            $threadFinder->whereOr(
                ['WrxtFreshnessState.status', '=', 'unverified'],
                ['WrxtFreshnessState.thread_id', '=', null]
            );
            return;
        }

        $threadFinder->where('WrxtFreshnessState.status', $status);
    }
}
