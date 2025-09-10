<?php

namespace Minhyung\Monolog\Tests;

use Minhyung\Monolog\SynologyChatFormatter;
use Minhyung\Monolog\SynologyChatHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class SynologyChatTest extends TestCase
{
    public function testFormatter(): void
    {
        $handler = new TestHandler();
        $handler->setFormatter(new SynologyChatFormatter());

        $log = new Logger('test');
        $log->pushHandler($handler);
        $log->info('test message');

        $records = $handler->getRecords();
        $this->assertCount(1, $records);
        $this->assertStringContainsString('"message":"test message"', $records[0]->formatted);
    }

    public function testHandler(): void
    {
        $url = $_ENV['WEBHOOK_URL'];
        if (! $url) {
            $this->markTestSkipped('WEBHOOK_URL environment variable is not set.');
        }
        $handler = new SynologyChatHandler(urldecode($url));

        $log = new Logger('test');
        $log->pushHandler($handler);
        $log->info('test message');
    }
}
