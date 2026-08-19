<?php

namespace WarextStudios\ThreadFreshness\Admin\Controller;

use XF\Admin\Controller\AbstractController;
use XF\Mvc\ParameterBag;

class Dashboard extends AbstractController
{
    protected function preDispatchController($action, ParameterBag $params)
    {
        $this->setSectionContext('wrxtThreadFreshness');
    }

    public function actionIndex()
    {
        $repository = $this->repository('WarextStudios\ThreadFreshness:ThreadFreshness');

        return $this->view(
            'WarextStudios\ThreadFreshness:Dashboard',
            'wrxt_thread_freshness_dashboard',
            [
                'stats' => $repository->getDashboardStats(),
                'criticalThreads' => $repository->getCriticalThreads(50)
            ]
        );
    }
}
