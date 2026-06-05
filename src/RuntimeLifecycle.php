<?php

declare(strict_types=1);

namespace App;

final class RuntimeLifecycle
{
    private bool $shutdownRequested = false;
    private bool $signalsInstalled = false;
    private bool $shutdownLoggerRegistered = false;

    /** @var callable(string, string=): void */
    private $logger;

    public function __construct(?callable $logger = null)
    {
        $this->logger = $logger ?? static fn (string $message, string $level = 'info'): null => null;
    }

    public function installSignalHandlers(callable $shutdown): bool
    {
        if (!function_exists('pcntl_signal') || !function_exists('pcntl_async_signals')) {
            ($this->logger)('Signal handling is unavailable because the pcntl extension is not loaded.', 'warning');

            return false;
        }

        pcntl_async_signals(true);

        foreach ([SIGINT, SIGTERM] as $signal) {
            pcntl_signal($signal, function (int $receivedSignal) use ($shutdown): void {
                $this->requestShutdown($receivedSignal, $shutdown);
            });
        }

        $this->signalsInstalled = true;
        ($this->logger)('Signal handlers installed for SIGINT and SIGTERM.', 'debug');

        return true;
    }

    public function requestShutdown(int $signal, callable $shutdown): void
    {
        if ($this->shutdownRequested) {
            return;
        }

        $this->shutdownRequested = true;
        ($this->logger)(sprintf('Received signal %d. Requesting Discord runtime shutdown.', $signal), 'info');
        $shutdown($signal);
    }

    public function registerShutdownLogger(): void
    {
        if ($this->shutdownLoggerRegistered) {
            return;
        }

        register_shutdown_function(function (): void {
            ($this->logger)('PHP process shutdown complete.', 'debug');
        });

        $this->shutdownLoggerRegistered = true;
    }

    public function shutdownRequested(): bool
    {
        return $this->shutdownRequested;
    }

    public function signalsInstalled(): bool
    {
        return $this->signalsInstalled;
    }

    public function shutdownLoggerRegistered(): bool
    {
        return $this->shutdownLoggerRegistered;
    }
}
