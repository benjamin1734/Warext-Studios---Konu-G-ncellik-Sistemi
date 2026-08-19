<?php

namespace WarextStudios\ThreadFreshness\Cron;

class Recalculate
{
    public static function run(): void
    {
        \XF::app()->jobManager()->enqueue(
            'WarextStudios\ThreadFreshness:Recalculate',
            []
        );
    }
}
