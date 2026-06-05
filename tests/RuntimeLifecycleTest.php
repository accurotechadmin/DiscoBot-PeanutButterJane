<?php

declare(strict_types=1);

namespace Tests;

use App\RuntimeLifecycle;
use PHPUnit\Framework\TestCase;

final class RuntimeLifecycleTest extends TestCase
{
    public function testInitialStateHasNoShutdownRequest(): void
    {
        $lifecycle = new RuntimeLifecycle();

        self::assertFalse($lifecycle->shutdownRequested());
        self::assertFalse($lifecycle->signalsInstalled());
        self::assertFalse($lifecycle->shutdownLoggerRegistered());
    }

    public function testRequestShutdownInvokesCallbackOnceAndMarksState(): void
    {
        $messages = [];
        $signals = [];
        $lifecycle = new RuntimeLifecycle(static function (string $message, string $level = 'info') use (&$messages): void {
            $messages[] = [$level, $message];
        });

        $shutdown = static function (int $signal) use (&$signals): void {
            $signals[] = $signal;
        };

        $lifecycle->requestShutdown(15, $shutdown);
        $lifecycle->requestShutdown(2, $shutdown);

        self::assertTrue($lifecycle->shutdownRequested());
        self::assertSame([15], $signals);
        self::assertSame('info', $messages[0][0]);
        self::assertStringContainsString('Received signal 15', $messages[0][1]);
    }

    public function testRegisterShutdownLoggerIsIdempotent(): void
    {
        $lifecycle = new RuntimeLifecycle();

        $lifecycle->registerShutdownLogger();
        $lifecycle->registerShutdownLogger();

        self::assertTrue($lifecycle->shutdownLoggerRegistered());
    }

    public function testInstallSignalHandlersReturnsBoolean(): void
    {
        $messages = [];
        $lifecycle = new RuntimeLifecycle(static function (string $message) use (&$messages): void {
            $messages[] = $message;
        });

        $installed = $lifecycle->installSignalHandlers(static function (): void {
        });

        self::assertIsBool($installed);
    }
}
