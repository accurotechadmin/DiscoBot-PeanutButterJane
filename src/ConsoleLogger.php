<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

final class ConsoleLogger
{
    /** @var array<string, int> */
    private const LEVELS = [
        'debug' => 0,
        'info' => 1,
        'warning' => 2,
        'error' => 3,
    ];

    private int $minimumLevel;

    /** @var resource */
    private $stream;

    /**
     * @param resource|null $stream
     */
    public function __construct(
        string $minimumLevel = 'debug',
        mixed $stream = null,
        private readonly ?string $fileDirectory = null,
        private readonly mixed $clock = null,
    ) {
        $normalizedLevel = strtolower(trim($minimumLevel));

        if (!isset(self::LEVELS[$normalizedLevel])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid LOG_LEVEL "%s". Use one of: %s.',
                $minimumLevel,
                implode(', ', array_keys(self::LEVELS)),
            ));
        }

        if ($stream !== null && !is_resource($stream)) {
            throw new InvalidArgumentException('Console logger stream must be a valid resource.');
        }

        if ($fileDirectory !== null && trim($fileDirectory) === '') {
            throw new InvalidArgumentException('Structured log directory must be a non-empty path.');
        }

        $this->minimumLevel = self::LEVELS[$normalizedLevel];
        $this->stream = $stream ?? STDOUT;
    }

    public function __invoke(string $message, string $level = 'info'): void
    {
        $normalizedLevel = strtolower(trim($level));
        $levelValue = self::LEVELS[$normalizedLevel] ?? self::LEVELS['info'];

        if ($levelValue < $this->minimumLevel) {
            return;
        }

        $timestamp = $this->timestamp();

        fwrite($this->stream, sprintf(
            '[%s] %s: %s%s',
            $timestamp,
            strtoupper($normalizedLevel),
            $message,
            PHP_EOL,
        ));

        if ($this->fileDirectory !== null) {
            $this->writeStructuredLog($timestamp, $normalizedLevel, $message);
        }
    }

    private function timestamp(): string
    {
        if (is_callable($this->clock)) {
            return date('Y-m-d H:i:s', (int) ($this->clock)());
        }

        return date('Y-m-d H:i:s');
    }

    private function writeStructuredLog(string $timestamp, string $level, string $message): void
    {
        $directory = rtrim((string) $this->fileDirectory, DIRECTORY_SEPARATOR);

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            fwrite($this->stream, sprintf('[%s] WARNING: Unable to create structured log directory "%s".%s', $timestamp, $directory, PHP_EOL));
            return;
        }

        $date = substr($timestamp, 0, 10);
        $path = sprintf('%s%sbot-%s.json', $directory, DIRECTORY_SEPARATOR, $date);
        $record = [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => $message,
        ];
        $encoded = json_encode($record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($encoded === false) {
            $encoded = '{"timestamp":"' . $timestamp . '","level":"error","message":"Unable to encode log record."}';
        }

        if (file_put_contents($path, $encoded . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
            fwrite($this->stream, sprintf('[%s] WARNING: Unable to write structured log file "%s".%s', $timestamp, $path, PHP_EOL));
        }
    }
}
