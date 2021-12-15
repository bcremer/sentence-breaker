<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker;

class SentenceBuilder
{
    /**
     * @param iterable<TokenProbability> $tokenProbabilities
     * @param int                $threshold
     *
     * @return array
     */
    public function build(iterable $tokenProbabilities, int $threshold = 50)
    {
        $currentSentence = '';

        foreach ($tokenProbabilities as $tokenProbability) {
            $token = $tokenProbability->getToken();
            $currentSentence .= $token->getPrintableValue();
            $meetsThreshold = $tokenProbability->getProbability() >= $threshold;
            $currentSentenceIsEmpty = empty(trim($currentSentence));

            if ($meetsThreshold && !$currentSentenceIsEmpty) {
                yield ltrim($currentSentence);
                $currentSentence = '';
            }
        }
    }
}
