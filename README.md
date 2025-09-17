# Monolog Synology Handler

A Monolog handler for sending log messages to Synology Chat via incoming webhooks.

## Features

- Send logs from your PHP application directly to Synology Chat
- Custom formatter for structured JSON messages
- Built on top of [Monolog](https://github.com/Seldaek/monolog)

## Installation

Install via Composer:

```bash
composer require minhyung/monolog-synology
```

## Usage

```php
use Minhyung\Monolog\SynologyChatHandler;
use Monolog\Logger;

$webhookUrl = 'https://your-synology-chat-webhook-url';
$logger = new Logger('synology');
$handler = new SynologyChatHandler($webhookUrl);
$logger->pushHandler($handler);

$logger->info('This is a test message sent to Synology Chat!');
```

### Handler Options

- **$url** (string): Synology Chat incoming webhook URL (required)
- **$ignoreFailure** (bool): If true, exceptions during sending will be ignored (default: false)
- **$level**: Minimum logging level (default: `Logger::DEBUG`)
- **$bubble**: Whether messages bubble up the stack (default: true)

## Formatter

The handler uses a custom formatter (`SynologyChatFormatter`) that encodes log records as JSON strings for Synology Chat.

## License

MIT License. See [LICENSE](LICENSE) for details.
