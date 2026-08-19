<?php

namespace WarextStudios\ThreadFreshness\XF\Pub\Controller;

use WarextStudios\ThreadFreshness\Util\ModeratorStatus;
use XF\Mvc\ParameterBag;

class Thread extends XFCP_Thread
{
    public function actionFreshnessVote(ParameterBag $params)
    {
        $this->assertPostOnly();

        $thread = $this->assertViewableThread($params->thread_id);

        if (!$thread->wrxtFreshnessCanVote())
        {
            return $this->noPermission();
        }

        $input = $this->filter([
            'vote' => 'int',
            'reason' => 'str',
            'version' => 'str',
            'message' => 'str'
        ]);

        if (!in_array($input['vote'], [-1, 1], true))
        {
            return $this->error('Geçersiz oy değeri.');
        }

        if ($input['vote'] === 1)
        {
            $input['reason'] = '';
        }

        $configuredVersions = $thread->wrxtFreshnessGetConfiguredVersions();
        if ($configuredVersions && !in_array(trim($input['version']), $configuredVersions, true))
        {
            return $this->error('Geçerli bir sürüm seçmelisiniz.');
        }

        $service = $this->service(
            'WarextStudios\ThreadFreshness:ThreadFreshness\Vote',
            $thread,
            \XF::visitor()
        );

        $service->setVote($input['vote']);
        $service->setReason($input['reason']);
        $service->setVersion($input['version']);
        $service->setMessage($input['message']);
        $service->save();

        return $this->redirect(
            $this->buildLink('threads', $thread) . '#wrxt-thread-freshness'
        );
    }

    public function actionFreshnessModerate(ParameterBag $params)
    {
        $this->assertPostOnly();

        $thread = $this->assertViewableThread($params->thread_id);
        if (!$thread->wrxtFreshnessCanModerate())
        {
            return $this->noPermission();
        }

        $status = trim($this->filter('moderator_status', 'str'));
        if (ModeratorStatus::normalize($status) !== $status)
        {
            return $this->error('Geçersiz moderatör durumu.');
        }

        $service = $this->service(
            'WarextStudios\ThreadFreshness:ThreadFreshness\Moderate',
            $thread,
            \XF::visitor()
        );
        $service->setStatus($status);
        $service->save();

        return $this->redirect(
            $this->buildLink('threads', $thread) . '#wrxt-thread-freshness'
        );
    }
}
