<?php

declare(strict_types=1);

namespace Tests;

use App\ConsoleLogger;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ConsoleLoggerTest extends TestCase
{
    public function testFiltersMessagesBelowConfiguredLogLevel(): void
    {
        $stream = fopen('php://temp', 'w+');
        self::assertIsResource($stream);

        $logger = new ConsoleLogger('warning', $stream);
        $logger('Hidden debug message.', 'debug');
        $logger('Visible warning message.', 'warning');

        rewind($stream);
        $output = stream_get_contents($stream);
        fclose($stream);

        self::assertIsString($output);
        self::assertStringNotContainsString('Hidden debug message.', $output);
        self::assertStringContainsString('WARNING: Visible warning message.', $output);
    }


    public function testWritesStructuredDailyJsonLogWhenDirectoryIsConfigured(): void
    {
        $stream = fopen('php://temp', 'w+');
        self::assertIsResource($stream);
        $directory = sys_get_temp_dir() . '/discbotskel-json-logs-' . bin2hex(random_bytes(4));

        $logger = new ConsoleLogger('debug', $stream, $directory, static fn (): int => 1717245296);
        $logger('Structured log message.', 'info');

        $path = $directory . DIRECTORY_SEPARATOR . 'bot-2024-06-01.json';
        self::assertFileExists($path);
        $line = trim((string) file_get_contents($path));
        $record = json_decode($line, true);

        self::assertSame([
            'timestamp' => '2024-06-01 12:34:56',
            'level' => 'info',
            'message' => 'Structured log message.',
        ], $record);

        fclose($stream);
        unlink($path);
        rmdir($directory);
    }

    public function testRejectsInvalidLogLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid LOG_LEVEL "verbose"');

        new ConsoleLogger('verbose');
    }
}
