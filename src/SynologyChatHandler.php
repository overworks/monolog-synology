<?php

namespace Minhyung\Monolog;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
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
        try {
            $payload = [
                'text' => $message,
            ];

            $client = new Client();
            $response = $client->post($this->url, [
                RequestOptions::FORM_PARAMS => [
                    'payload' => Utils::jsonEncode($payload),
                ],
            ]);
            $responseBody = (string) $response->getBody();
            $result = json_decode($responseBody, true);
            if (! ($result['success'] ?? false)) {
                $error = $result['error'] ?? [];
                $code = $error['code'] ?? 0;
                $message = $error['errors'][0] ?? 'Unknown error';
                throw new \RuntimeException($message, $code);
            }
        } catch (\Throwable $e) {
            if (! $this->ignoreFailure) {
                throw $e;
            }
        }
    }
}
