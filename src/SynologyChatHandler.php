<?php

namespace Minhyung\Monolog;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Utils;

class SynologyChatHandler extends AbstractProcessingHandler
{
    /**
     * @param string                       $url   The Synology Chat incoming webhook URL
     * @param int|string|Level|LogLevel::* $level  The minimum logging level at which this handler will be triggered
     * @param bool                         $bubble Whether the messages that are handled can bubble up the stack or not
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    public function __construct(
        protected string $url,
        protected bool $ignoreFailure = false,
        int|string|Level $level = Level::Debug,
        bool $bubble = true,
    ) {
        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $this->sendMessage($record->formatted);
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new SynologyChatFormatter();
    }

    protected function sendMessage(string $message): void
    {
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(['payload' => Utils::jsonEncode(['text' => $message])]),
                'timeout' => 5,
            ],
        ];

        $resource = stream_context_create($options);

        if (file_get_contents($this->url, false, $resource) === false && ! $this->ignoreFailure) {
            throw new \RuntimeException('Failed to send message to Synology Chat');
        }
    }
}
