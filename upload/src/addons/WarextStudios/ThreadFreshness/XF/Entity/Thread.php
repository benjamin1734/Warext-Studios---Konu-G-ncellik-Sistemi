<?php

namespace WarextStudios\ThreadFreshness\XF\Entity;

use WarextStudios\ThreadFreshness\Entity\ThreadState;
use WarextStudios\ThreadFreshness\Util\Eligibility;
use XF\Mvc\Entity\Structure;

class Thread extends XFCP_Thread
{
    public static function getStructure(Structure $structure): Structure
    {
        $structure = parent::getStructure($structure);

        $structure->relations['WrxtFreshnessState'] = [
            'entity' => 'WarextStudios\ThreadFreshness:ThreadState',
            'type' => self::TO_ONE,
            'conditions' => 'thread_id',
            'primary' => true
        ];

        return $structure;
    }

    public function wrxtFreshnessIsEnabled(): bool
    {
        $general = (array)(\XF::options()->wrxtFreshnessGeneral ?? []);
        $enabled = !empty($general['enabled']);
        $forumIds = Eligibility::parseForumIds((string)(\XF::options()->wrxtFreshnessForumIds ?? ''));

        return $enabled && in_array((int)$this->node_id, $forumIds, true);
    }

    public function wrxtFreshnessIsEligible(): bool
    {
        $general = (array)(\XF::options()->wrxtFreshnessGeneral ?? []);
        $forumIds = Eligibility::parseForumIds((string)(\XF::options()->wrxtFreshnessForumIds ?? ''));

        return Eligibility::isThreadEligible(
            !empty($general['enabled']),
            (int)$this->node_id,
            (int)$this->last_post_date,
            $forumIds,
            (int)($general['days'] ?? 90),
            \XF::$time
        );
    }

    public function wrxtFreshnessCanVote(): bool
    {
        if (!$this->wrxtFreshnessIsEligible())
        {
            return false;
        }

        $visitor = \XF::visitor();

        return Eligibility::canVisitorVote(
            (int)$visitor->user_id,
            (int)$this->user_id,
            (int)$visitor->register_date,
            (int)$visitor->message_count,
            $visitor->hasPermission('wrxtFreshness', 'vote'),
            (bool)(\XF::options()->wrxtFreshnessAllowOwnThread ?? false),
            (int)(\XF::options()->wrxtFreshnessMinAccountDays ?? 7),
            (int)(\XF::options()->wrxtFreshnessMinMessages ?? 3),
            \XF::$time
        );
    }

    public function wrxtFreshnessGetState(): ?ThreadState
    {
        return $this->WrxtFreshnessState;
    }

    public function wrxtFreshnessGetVisitorVote(): int
    {
        $visitorId = (int)\XF::visitor()->user_id;
        if ($visitorId <= 0)
        {
            return 0;
        }

        $vote = \XF::finder('WarextStudios\ThreadFreshness:Vote')
            ->where('thread_id', $this->thread_id)
            ->where('user_id', $visitorId)
            ->fetchOne();

        return $vote ? (int)$vote->vote : 0;
    }
}
