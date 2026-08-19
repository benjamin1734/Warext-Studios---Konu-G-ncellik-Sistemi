<?php

namespace WarextStudios\ThreadFreshness\XF\Pub\Controller;

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
}
