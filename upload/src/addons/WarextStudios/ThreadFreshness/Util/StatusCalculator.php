<?php

namespace WarextStudios\ThreadFreshness\Util;

class StatusCalculator
{
    public static function calculate(array $votes, int $now): array
    {
        $positiveWeight = 0.0;
        $negativeWeight = 0.0;
        $positiveCount = 0;
        $negativeCount = 0;
        $lastVoteDate = 0;

        foreach ($votes as $vote)
        {
            $value = (int)$vote['vote'];
            if ($value !== 1 && $value !== -1)
            {
                continue;
            }

            $date = (int)$vote['vote_date'];
            $weight = VoteWeight::forAge($date, $now);
            $lastVoteDate = max($lastVoteDate, $date);

            if ($value === 1)
            {
                $positiveCount++;
                $positiveWeight += $weight;
            }
            else
            {
                $negativeCount++;
                $negativeWeight += $weight;
            }
        }

        $voteCount = $positiveCount + $negativeCount;
        $totalWeight = $positiveWeight + $negativeWeight;
        $score = $totalWeight > 0 ? $positiveWeight / $totalWeight : 0.0;
        $status = 'unverified';

        if ($voteCount >= 8 && $score <= 0.20)
        {
            $status = 'not_working';
        }
        elseif ($voteCount >= 5 && $score <= 0.30)
        {
            $status = 'questionable';
        }
        elseif ($voteCount >= 5 && $score >= 0.80)
        {
            $status = 'current';
        }
        elseif ($voteCount >= 3 && $score >= 0.75)
        {
            $status = 'likely_current';
        }
        elseif ($voteCount >= 3 && $score >= 0.40 && $score <= 0.60)
        {
            $status = 'mixed';
        }

        return [
            'status' => $status,
            'score' => $score,
            'positive_weight' => $positiveWeight,
            'negative_weight' => $negativeWeight,
            'vote_count' => $voteCount,
            'positive_count' => $positiveCount,
            'negative_count' => $negativeCount,
            'last_vote_date' => $lastVoteDate
        ];
    }
}
