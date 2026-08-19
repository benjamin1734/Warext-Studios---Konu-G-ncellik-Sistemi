<?php

require __DIR__ . '/../upload/src/addons/WarextStudios/ThreadFreshness/Util/VoteWeight.php';
require __DIR__ . '/../upload/src/addons/WarextStudios/ThreadFreshness/Util/StatusCalculator.php';

use WarextStudios\ThreadFreshness\Util\StatusCalculator;
use WarextStudios\ThreadFreshness\Util\VoteWeight;

function assertSameValue($expected, $actual, string $name): void
{
    if ($expected !== $actual)
    {
        throw new RuntimeException($name . ': ' . var_export($actual, true));
    }
}

function makeVotes(int $positive, int $negative, int $date): array
{
    $votes = [];
    for ($i = 0; $i < $positive; $i++)
    {
        $votes[] = ['vote' => 1, 'vote_date' => $date];
    }
    for ($i = 0; $i < $negative; $i++)
    {
        $votes[] = ['vote' => -1, 'vote_date' => $date];
    }
    return $votes;
}

$now = 2000000000;
$day = 86400;

assertSameValue(1.0, VoteWeight::forAge($now - 90 * $day, $now), 'weight_90');
assertSameValue(0.75, VoteWeight::forAge($now - 90 * $day - 1, $now), 'weight_90_plus');
assertSameValue(0.5, VoteWeight::forAge($now - 180 * $day - 1, $now), 'weight_180_plus');
assertSameValue(0.25, VoteWeight::forAge($now - 365 * $day - 1, $now), 'weight_365_plus');
assertSameValue('unverified', StatusCalculator::calculate([], $now)['status'], 'empty');
assertSameValue('likely_current', StatusCalculator::calculate(makeVotes(3, 0, $now), $now)['status'], 'likely_current');
assertSameValue('current', StatusCalculator::calculate(makeVotes(4, 1, $now), $now)['status'], 'current');
assertSameValue('mixed', StatusCalculator::calculate(makeVotes(2, 2, $now), $now)['status'], 'mixed');
assertSameValue('questionable', StatusCalculator::calculate(makeVotes(0, 5, $now), $now)['status'], 'questionable');
assertSameValue('not_working', StatusCalculator::calculate(makeVotes(0, 8, $now), $now)['status'], 'not_working');
assertSameValue('unverified', StatusCalculator::calculate(makeVotes(8, 0, $now - 366 * $day), $now)['status'], 'aged_votes');

echo "OK\n";
