<?php

declare(strict_types=1);

namespace Tests;

use App\CommandContext;
use App\CommandRouter;
use App\Commands\CommandInterface;
use App\Commands\EchoCommand;
use App\Commands\HelpCommand;
use App\Commands\PingCommand;
use App\Commands\SettingsCommand;
use App\Commands\TimeCommand;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class CommandRouterTest extends TestCase
{
    public function testNonPrefixedMessagesAreIgnored(): void
    {
        $router = $this->router();

        self::assertNull($router->routeContent('hello !bot ping', '!bot', $this->config()));
    }

    public function testPrefixAdjacentTextIsIgnored(): void
    {
        $router = $this->router();

        self::assertNull($router->routeContent('!botping', '!bot', $this->config()));
    }

    public function testLeadingAndTrailingWhitespaceAroundValidCommandIsAllowed(): void
    {
        $router = $this->router();

        self::assertSame('Pong!', $router->routeContent("  \t !bot ping  \n", '!bot', $this->config()));
    }

    public function testMultipleSpacesAndTabsBetweenCommandAndArgumentsAreCollapsed(): void
    {
        $capture = new CaptureCommand();
        $router = new CommandRouter([
            'capture' => $capture,
        ]);

        self::assertSame('captured', $router->routeContent("!bot   capture\tone   two\tthree", '!bot', $this->config()));
        self::assertSame(['one', 'two', 'three'], $capture->arguments);
        self::assertSame('capture', $capture->commandName);
    }

    public function testEmptyPrefixIgnoresContent(): void
    {
        $router = $this->router();

        self::assertNull($router->routeContent('ping', '', $this->config('')));
        self::assertNull($router->routeContent('', '', $this->config('')));
    }

    public function testBarePrefixWithSurroundingWhitespaceRoutesToHelp(): void
    {
        $router = $this->router();

        $reply = $router->routeContent(" \n !bot \t ", '!bot', $this->config());

        self::assertIsString($reply);
        self::assertStringContainsString('Available commands:', $reply);
        self::assertStringContainsString('`!bot help`', $reply);
    }

    public function testBarePrefixRoutesToHelp(): void
    {
        $router = $this->router();

        $reply = $router->routeContent('!bot', '!bot', $this->config());

        self::assertIsString($reply);
        self::assertStringContainsString('Available commands:', $reply);
        self::assertStringContainsString('`!bot help`', $reply);
    }

    public function testUnknownMixedCaseCommandsReturnFriendlyMessageWithNormalizedName(): void
    {
        $router = $this->router();

        self::assertSame(
            'Unknown command `missing`. Try `!bot help` for a list of commands.',
            $router->routeContent('!bot MiSsInG', '!bot', $this->config()),
        );
    }

    public function testCommandNamesAreCaseInsensitive(): void
    {
        $router = $this->router();

        self::assertSame('Pong!', $router->routeContent('!bot PiNg', '!bot', $this->config()));
    }

    public function testArgumentsAreSplitAndPassedToCommandContext(): void
    {
        $capture = new CaptureCommand();
        $router = new CommandRouter([
            'capture' => $capture,
        ]);

        self::assertSame('captured', $router->routeContent('!bot capture one two three', '!bot', $this->config()));
        self::assertSame(['one', 'two', 'three'], $capture->arguments);
        self::assertSame('capture', $capture->commandName);
    }

    public function testCommandExceptionsAreLoggedAndReturnSafeReply(): void
    {
        $messages = [];
        $router = new CommandRouter([
            'explode' => new ExplodingCommand(),
        ], static function (string $message) use (&$messages): void {
            $messages[] = $message;
        });

        $reply = $router->routeContent('!bot explode', '!bot', $this->config());

        self::assertSame('Sorry, something went wrong while running that command.', $reply);
        self::assertCount(1, $messages);
        self::assertStringContainsString('Command "explode" failed: sensitive implementation detail', $messages[0]);
        self::assertStringNotContainsString('sensitive implementation detail', $reply);
    }

    public function testHelpThroughRouterIncludesAllRegisteredCommandUsageStrings(): void
    {
        $router = new CommandRouter(require __DIR__ . '/../config/commands.php');

        $reply = $router->routeContent('!bot help', '!bot', $this->config());

        self::assertIsString($reply);
        self::assertStringContainsString('`!bot ping`', $reply);
        self::assertStringContainsString('`!bot time`', $reply);
        self::assertStringContainsString('`!bot settings`', $reply);
        self::assertStringContainsString('`!bot echo <message>`', $reply);
        self::assertStringContainsString('`!bot help`', $reply);
    }

    public function testAliasRoutesToSameCommandWithoutDuplicateHelpEntry(): void
    {
        $router = new CommandRouter([
            'ping' => PingCommand::class,
            'help' => [
                'class' => HelpCommand::class,
                'aliases' => ['commands'],
            ],
        ]);

        self::assertStringContainsString('Available commands:', (string) $router->routeContent('!bot commands', '!bot', $this->config()));

        $metadata = $router->metadata('!bot');
        self::assertArrayHasKey('help', $metadata);
        self::assertArrayNotHasKey('commands', $metadata);
        self::assertSame(['commands'], $metadata['help']['aliases']);
    }

    public function testConfigRegistryCanRegisterAliasForBuiltInCommand(): void
    {
        $router = new CommandRouter(require __DIR__ . '/../config/commands.php');

        self::assertStringContainsString('Available commands:', (string) $router->routeContent('!bot commands', '!bot', $this->config()));
    }

    public function testDirectMessageCommandsUseUnprefixedContent(): void
    {
        $router = $this->router();

        self::assertSame('Pong!', $router->routeDirectMessageContent('ping', $this->config('')));
        self::assertSame('hello privately', $router->routeDirectMessageContent('echo hello privately', $this->config('')));
        self::assertNull($router->routeDirectMessageContent('', $this->config('')));
    }

    public function testDirectMessageHelpUsesUnprefixedUsage(): void
    {
        $router = $this->router();

        $reply = $router->routeDirectMessageContent('help', $this->config(''));

        self::assertIsString($reply);
        self::assertStringContainsString('`ping`', $reply);
        self::assertStringContainsString('`echo <message>`', $reply);
    }

    public function testMentionCommandsRequireBotMentionAtStart(): void
    {
        $router = $this->router();

        self::assertSame('Pong!', $router->routeMentionContent('<@123456> ping', '123456', $this->config('<@123456>')));
        self::assertSame('Pong!', $router->routeMentionContent('<@!123456> ping', '123456', $this->config('<@!123456>')));
        self::assertNull($router->routeMentionContent('hello <@123456> ping', '123456', $this->config('<@123456>')));
        self::assertNull($router->routeMentionContent('<@123456>ping', '123456', $this->config('<@123456>')));
    }

    public function testBareMentionRoutesToHelp(): void
    {
        $router = $this->router();

        $reply = $router->routeMentionContent('<@123456>', '123456', $this->config('<@123456>'));

        self::assertIsString($reply);
        self::assertStringContainsString('Available commands:', $reply);
        self::assertStringContainsString('`<@123456> help`', $reply);
    }

    public function testSlashCommandDispatchUsesSlashUsageAndAliases(): void
    {
        $router = $this->router();

        self::assertSame('Pong!', $router->routeCommand('PING', [], '/', $this->config('/')));
        self::assertStringContainsString('Available commands:', (string) $router->routeCommand('commands', [], '/', $this->config('/')));

        $reply = $router->routeCommand('help', [], '/', $this->config('/'));

        self::assertIsString($reply);
        self::assertStringContainsString('`/ping`', $reply);
        self::assertStringContainsString('`/echo <message>`', $reply);
    }

    public function testUnknownDirectAndSlashCommandsUseMatchingHelpHints(): void
    {
        $router = $this->router();

        self::assertSame(
            'Unknown command `missing`. Try `help` for a list of commands.',
            $router->routeDirectMessageContent('missing', $this->config('')),
        );
        self::assertSame(
            'Unknown command `missing`. Try `/help` for a list of commands.',
            $router->routeCommand('missing', [], '/', $this->config('/')),
        );
    }

    public function testSlashCommandDefinitionsIncludeRegisteredCommandsAndAliases(): void
    {
        $router = $this->router();

        self::assertSame([
            ['name' => 'ping', 'description' => 'Check that the bot is online.', 'options' => []],
            ['name' => 'time', 'description' => 'Show the current configured bot time.', 'options' => []],
            ['name' => 'settings', 'description' => 'Show the current bot settings summary.', 'options' => []],
            ['name' => 'echo', 'description' => 'Echo command arguments back to Discord.', 'options' => [
                ['name' => 'arguments', 'description' => 'Optional command arguments as text.', 'type' => 3, 'required' => false],
            ]],
            ['name' => 'help', 'description' => 'List available commands.', 'options' => []],
            ['name' => 'commands', 'description' => 'Alias for help. List available commands.', 'options' => []],
        ], $router->slashCommandDefinitions());
    }

    public function testSlashOptionMetadataCanBeConfiguredForAdditionalCommands(): void
    {
        $router = new CommandRouter([
            'capture' => [
                'class' => CaptureCommand::class,
                'aliases' => ['grab'],
                'slash_options' => [
                    [
                        'name' => 'Text',
                        'description' => 'Text to capture.',
                        'type' => 3,
                        'required' => true,
                    ],
                ],
            ],
        ]);

        self::assertSame([
            [
                'name' => 'capture',
                'description' => 'Capture arguments for tests.',
                'options' => [
                    ['name' => 'text', 'description' => 'Text to capture.', 'type' => 3, 'required' => true],
                ],
            ],
            [
                'name' => 'grab',
                'description' => 'Alias for capture. Capture arguments for tests.',
                'options' => [
                    ['name' => 'text', 'description' => 'Text to capture.', 'type' => 3, 'required' => true],
                ],
            ],
        ], $router->slashCommandDefinitions());
    }

    private function router(): CommandRouter
    {
        return new CommandRouter([
            'ping' => PingCommand::class,
            'time' => TimeCommand::class,
            'settings' => SettingsCommand::class,
            'echo' => [
                'class' => EchoCommand::class,
                'slash_options' => [
                    [
                        'name' => 'arguments',
                        'description' => 'Optional command arguments as text.',
                        'type' => 3,
                        'required' => false,
                    ],
                ],
            ],
            'help' => [
                'class' => HelpCommand::class,
                'aliases' => ['commands'],
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function config(string $prefix = '!bot'): array
    {
        return [
            'bot' => [
                'prefix' => $prefix,
                'timezone' => 'UTC',
                'env' => 'testing',
            ],
            'commands' => [],
        ];
    }
}

final class CaptureCommand implements CommandInterface
{
    /** @var list<string> */
    public array $arguments = [];

    public string $commandName = '';

    public function execute(CommandContext $context): string
    {
        $this->arguments = $context->arguments();
        $this->commandName = $context->commandName();

        return 'captured';
    }

    public function description(): string
    {
        return 'Capture arguments for tests.';
    }

    public function usage(string $prefix): string
    {
        return sprintf('%s capture <arguments>', $prefix);
    }
}

final class ExplodingCommand implements CommandInterface
{
    public function execute(CommandContext $context): string
    {
        throw new RuntimeException('sensitive implementation detail');
    }

    public function description(): string
    {
        return 'Throw an exception for tests.';
    }

    public function usage(string $prefix): string
    {
        return sprintf('%s explode', $prefix);
    }
}
