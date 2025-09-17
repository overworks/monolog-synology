<?php

namespace Minhyung\Monolog\Tests;

use Minhyung\Monolog\SynologyChatFormatter;
use Minhyung\Monolog\SynologyChatHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class SynologyChatTest extends TestCase
{
    protected $faker;

    protected function faker()
    {
        return $this->faker ??= \Faker\Factory::create();
    }

    public function testFormatter(): void
    {
        $handler = new TestHandler();
        $handler->setFormatter(new SynologyChatFormatter());

        $message = $this->faker()->sentence();
        $method = $this->faker()->randomElement(['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency']);

        $log = new Logger('test');
        $log->pushHandler($handler);
        $log->$method($message);

        $records = $handler->getRecords();
        $this->assertCount(1, $records);
        $this->assertEquals(strtoupper($method), $records[0]['level_name']);
        $this->assertStringContainsString(addslashes($message), $records[0]->formatted);
    }

    public function testHandler(): void
    {
        $url = $_ENV['WEBHOOK_URL'];
        if (! $url) {
            $this->markTestSkipped('WEBHOOK_URL environment variable is not set.');
        }
        $handler = new SynologyChatHandler(urldecode($url));

        $message = $this->faker()->sentence();
        $method = $this->faker()->randomElement(['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency']);

        $log = new Logger('test');
        $log->pushHandler($handler);
        $log->$method($message, ['foo' => 'bar', 'baz' => ['key' => 'value']]);
        $this->assertTrue(true);
    }
}
