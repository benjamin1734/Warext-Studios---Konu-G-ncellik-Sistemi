<?php

namespace WarextStudios\ThreadFreshness\Service\ThreadFreshness;

use WarextStudios\ThreadFreshness\Util\VersionSummary;
use XF\Entity\Thread;
use XF\Entity\User;
use XF\Service\AbstractService;

class Vote extends AbstractService
{
    protected Thread $thread;
    protected User $user;
    protected int $vote = 0;
    protected string $reason = '';
    protected string $version = '';
    protected string $message = '';

    public function __construct(\XF\App $app, Thread $thread, User $user)
    {
        parent::__construct($app);
        $this->thread = $thread;
        $this->user = $user;
    }

    public function setVote(int $vote): void
    {
        if (!in_array($vote, [-1, 1], true))
        {
            throw new \InvalidArgumentException('Invalid vote');
        }

        $this->vote = $vote;
    }

    public function setReason(string $reason): void
    {
        $reason = trim($reason);
        $allowed = [
            '',
            'outdated_version',
            'dead_links',
            'method_invalid',
            'incomplete',
            'did_not_work',
            'other'
        ];

        if (!in_array($reason, $allowed, true))
        {
            throw new \InvalidArgumentException('Invalid reason');
        }

        $this->reason = $reason;
    }

    public function setVersion(string $version): void
    {
        $this->version = VersionSummary::clean($version);
    }

    public function setMessage(string $message): void
    {
        $this->message = mb_substr(trim($message), 0, 500);
    }

    public function save()
    {
        if (!$this->thread->thread_id || !$this->user->user_id)
        {
            throw new \LogicException('Thread and user must exist');
        }

        if (!in_array($this->vote, [-1, 1], true))
        {
            throw new \LogicException('Vote is required');
        }

        $configuredVersions = $this->thread->wrxtFreshnessGetConfiguredVersions();
        if ($configuredVersions && !in_array($this->version, $configuredVersions, true))
        {
            throw new \LogicException('Invalid version');
        }

        $em = $this->app->em();
        $entity = $this->app->finder('WarextStudios\ThreadFreshness:Vote')
            ->where('thread_id', $this->thread->thread_id)
            ->where('user_id', $this->user->user_id)
            ->fetchOne();

        if (!$entity)
        {
            $entity = $em->create('WarextStudios\ThreadFreshness:Vote');
            $entity->thread_id = $this->thread->thread_id;
            $entity->user_id = $this->user->user_id;
        }

        $entity->vote = $this->vote;
        $entity->reason = $this->reason;
        $entity->version = $this->version;
        $entity->message = $this->message;
        $entity->save();

        $this->repository()->recalculateThread($this->thread->thread_id, 'vote', $this->user->user_id);

        return $entity;
    }

    protected function repository(): \WarextStudios\ThreadFreshness\Repository\ThreadFreshness
    {
        return $this->app->repository('WarextStudios\ThreadFreshness:ThreadFreshness');
    }
}
