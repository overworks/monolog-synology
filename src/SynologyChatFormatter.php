<?php

namespace Minhyung\Monolog;

use Monolog\Formatter\NormalizerFormatter;
use Monolog\LogRecord;
use Monolog\Utils;

class SynologyChatFormatter extends NormalizerFormatter
{
    /**
     * @inheritDoc
     */
    public function format(LogRecord $record): string
    {
        $normalized = $this->normalizeRecord($record);

        $levelEmojis = [
            'DEBUG' => '🐛',
            'INFO' => 'ℹ️',
            'NOTICE' => '🔔',
            'WARNING' => '⚠️',
            'ERROR' => '❌',
            'CRITICAL' => '🚨',
            'ALERT' => '🚨',
            'EMERGENCY' => '🚨',
        ];
        $levelName = strtoupper($normalized['level_name']);
        $levelEmoji = $levelEmojis[$levelName] ?? 'ℹ️';

        $text = "{$levelEmoji} *{$normalized['message']}*";
        if ($normalized['context']) {
            $text .= "\n```\n";
            foreach ($normalized['context'] as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = Utils::jsonEncode($value, true);
                }
                $text .= "- {$key}: {$value}\n";
            }
            $text .= "```\n";
        }

        if ($normalized['extra']) {
            $text .= "\n```\n";
            foreach ($normalized['extra'] as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = Utils::jsonEncode($value, true);
                }
                $text .= "- {$key}: {$value}\n";
            }
            $text .= "```\n";
        }

        return $text;
    }
}
