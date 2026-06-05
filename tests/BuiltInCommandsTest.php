<?php

declare(strict_types=1);

namespace Tests;

use App\CommandContext;
use App\Commands\EchoCommand;
use App\Commands\HelpCommand;
use App\Commands\PingCommand;
use App\Commands\SettingsCommand;
use App\Commands\TimeCommand;
use PHPUnit\Framework\TestCase;

final class BuiltInCommandsTest extends TestCase
{
    public function testPingCommandRepliesWithPong(): void
    {
        self::assertSame('Pong!', (new PingCommand())->execute($this->context('ping')));
    }

    public function testSettingsCommandShowsExpectedOutputShape(): void
    {
        $reply = (new SettingsCommand())->execute($this->context('settings'));

        self::assertStringContainsString('Bot settings:', $reply);
        self::assertStringContainsString('- Prefix: `!bot`', $reply);
        self::assertStringContainsString('- Timezone: `UTC`', $reply);
        self::assertStringContainsString('- Environment: `testing`', $reply);
    }

    public function testHelpCommandIncludesDescriptionsAndUsage(): void
    {
        $reply = (new HelpCommand())->execute($this->context('help'));

        self::assertStringContainsString('Available commands:', $reply);
        self::assertStringContainsString('`!bot ping` — Check that the bot is online.', $reply);
        self::assertStringContainsString('`!bot echo <message>` — Echo command arguments back to Discord.', $reply);
    }

    public function testTimeCommandShowsConfiguredTimezone(): void
    {
        $reply = (new TimeCommand())->execute($this->context('time'));

        self::assertMatchesRegularExpression('/^Current time in UTC: \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} UTC$/', $reply);
    }

    public function testEchoCommandHandlesEmptyArguments(): void
    {
        $reply = (new EchoCommand())->execute($this->context('echo'));

        self::assertSame('Nothing to echo. Try `!bot echo hello world`.', $reply);
    }

    public function testEchoCommandHandlesMultiWordArguments(): void
    {
        $reply = (new EchoCommand())->execute($this->context('echo', ['hello', 'friendly', 'world']));

        self::assertSame('hello friendly world', $reply);
    }


    public function testUsageFormattingSupportsSlashAndDirectMessagePaths(): void
    {
        self::assertSame('/ping', (new PingCommand())->usage('/'));
        self::assertSame('ping', (new PingCommand())->usage(''));
        self::assertSame('/echo <message>', (new EchoCommand())->usage('/'));
        self::assertSame('echo <message>', (new EchoCommand())->usage(''));
    }

    public function testEchoEmptyArgumentsUseCurrentInteractionPathUsage(): void
    {
        $reply = (new EchoCommand())->execute($this->context('echo', [], '/'));

        self::assertSame('Nothing to echo. Try `/echo hello world`.', $reply);
    }

    /** @param list<string> $arguments */
    private function context(string $commandName, array $arguments = [], string $prefix = '!bot'): CommandContext
    {
        return new CommandContext(
            discord: null,
            message: null,
            commandName: $commandName,
            arguments: $arguments,
            prefix: $prefix,
            config: [
                'bot' => [
                    'prefix' => $prefix,
                    'timezone' => 'UTC',
                    'env' => 'testing',
                ],
                'commands' => [
                    'metadata' => [
                        'ping' => [
                            'description' => 'Check that the bot is online.',
                            'usage' => '!bot ping',
                        ],
                        'echo' => [
                            'description' => 'Echo command arguments back to Discord.',
                            'usage' => '!bot echo <message>',
                        ],
                        'help' => [
                            'description' => 'List available commands.',
                            'usage' => '!bot help',
                        ],
                    ],
                ],
            ],
        );
    }
}
