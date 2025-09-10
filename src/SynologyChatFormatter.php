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

        return Utils::jsonEncode($normalized, true)."\n";
    }
}
