<?php

declare(strict_types=1);

namespace Tests;

use App\ConfigValidator;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ConfigValidatorTest extends TestCase
{
    public function testMissingTokenFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing DISCORD_BOT_TOKEN');

        ConfigValidator::validateBotConfig([
            'token' => '',
            'timezone' => 'UTC',
        ]);
    }

    public function testBlankPrefixFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid BOT_PREFIX');

        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '',
            'timezone' => 'UTC',
            'log_level' => 'debug',
        ]);
    }

    public function testPrefixWithWhitespaceFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid BOT_PREFIX "! bot"');

        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '! bot',
            'timezone' => 'UTC',
            'log_level' => 'debug',
        ]);
    }

    public function testInvalidTimezoneFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid BOT_TIMEZONE "Mars/Olympus"');

        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '!bot',
            'timezone' => 'Mars/Olympus',
            'log_level' => 'debug',
        ]);
    }

    public function testInvalidLogLevelFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid LOG_LEVEL "verbose"');

        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '!bot',
            'timezone' => 'UTC',
            'log_level' => 'verbose',
        ]);
    }


    public function testInvalidInteractionToggleFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid BOT_ENABLE_SLASH_COMMANDS "sometimes"');

        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '!bot',
            'timezone' => 'UTC',
            'log_level' => 'debug',
            'interactions' => [
                'prefix_commands' => 'true',
                'slash_commands' => 'sometimes',
                'mention_commands' => 'false',
                'dm_commands' => 'false',
            ],
        ]);
    }

    public function testBooleanInteractionTogglesPassValidation(): void
    {
        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '!bot',
            'timezone' => 'UTC',
            'log_level' => 'debug',
            'interactions' => [
                'prefix_commands' => true,
                'slash_commands' => false,
                'mention_commands' => true,
                'dm_commands' => false,
            ],
        ]);

        self::assertTrue(ConfigValidator::booleanValue('yes', 'TEST_TOGGLE'));
        self::assertFalse(ConfigValidator::booleanValue('off', 'TEST_TOGGLE'));
    }


    public function testInvalidEnvironmentFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid APP_ENV "demo"');

        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '!bot',
            'timezone' => 'UTC',
            'env' => 'demo',
            'log_level' => 'debug',
        ]);
    }

    public function testAllInteractionPathsDisabledFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Enable at least one interaction path');

        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '!bot',
            'timezone' => 'UTC',
            'env' => 'testing',
            'log_level' => 'debug',
            'interactions' => [
                'prefix_commands' => 'false',
                'slash_commands' => 'false',
                'mention_commands' => 'false',
                'dm_commands' => 'false',
            ],
        ]);
    }

    public function testNonNumericRateLimitMaxAttemptsFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid BOT_RATE_LIMIT_MAX_ATTEMPTS "abc"');

        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '!bot',
            'timezone' => 'UTC',
            'env' => 'testing',
            'log_level' => 'debug',
            'rate_limit' => [
                'max_attempts' => 'abc',
                'window_seconds' => '10',
            ],
        ]);
    }

    public function testNonNumericRateLimitWindowFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid BOT_RATE_LIMIT_WINDOW_SECONDS "abc"');

        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '!bot',
            'timezone' => 'UTC',
            'env' => 'testing',
            'log_level' => 'debug',
            'rate_limit' => [
                'max_attempts' => '5',
                'window_seconds' => 'abc',
            ],
        ]);
    }

    public function testInvalidCommandRegistryFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Duplicate alias or command name "ping"');

        ConfigValidator::validateCommandRegistry([
            'ping' => \App\Commands\PingCommand::class,
            'help' => [
                'class' => \App\Commands\HelpCommand::class,
                'aliases' => ['ping'],
            ],
        ]);
    }

    public function testInvalidSlashOptionFailsValidation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Duplicate option "text"');

        ConfigValidator::validateCommandRegistry([
            'echo' => [
                'class' => \App\Commands\EchoCommand::class,
                'slash_options' => [
                    ['name' => 'text', 'description' => 'Text to echo.', 'type' => 3],
                    ['name' => 'TEXT', 'description' => 'Duplicate text option.', 'type' => 3],
                ],
            ],
        ]);
    }

    public function testValidConfigPassesValidation(): void
    {
        ConfigValidator::validateBotConfig([
            'token' => 'not-a-real-token-for-tests',
            'prefix' => '!bot',
            'timezone' => 'UTC',
            'log_level' => 'debug',
        ]);

        self::assertTrue(true);
    }
}
