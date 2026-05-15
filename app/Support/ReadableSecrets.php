<?php

namespace App\Support;

class ReadableSecrets
{
    public static function password(): string
    {
        $adjectives = [
            'brisk', 'clear', 'mellow', 'steady', 'bright', 'silent', 'nimble', 'gentle',
            'happy', 'fierce', 'swift', 'calm', 'shiny', 'cozy', 'sharp', 'noble',
        ];

        $nouns = [
            'river', 'forest', 'comet', 'canyon', 'coffee', 'anchor', 'summit', 'ocean',
            'ember', 'harbor', 'signal', 'falcon', 'planet', 'studio', 'voyage', 'thunder',
        ];

        return sprintf(
            '%s-%s-%d',
            $adjectives[array_rand($adjectives)],
            $nouns[array_rand($nouns)],
            random_int(10, 99)
        );
    }
}
