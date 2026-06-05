<?php

declare(strict_types=1);

namespace App;

use App\Commands\CommandInterface;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use InvalidArgumentException;
use Throwable;

final class CommandRouter
{
    private const COMMAND_NAME_PATTERN = '/^[a-z0-9_-]{1,32}$/';
    private const SLASH_OPTION_NAME_PATTERN = '/^[a-z0-9_-]{1,32}$/';
    /** @var list<int> */
    private const ALLOWED_SLASH_OPTION_TYPES = [3, 4, 5, 6, 7, 8, 9, 10, 11];

    /** @var array<string, CommandInterface> */
    private array $commands = [];

    /** @var array<string, string> */
    private array $aliases = [];

    /** @var array<string, list<array{name: string, description: string, type?: int, required?: bool}>> */
    private array $slashOptions = [];

    /** @var callable(string, string=): void */
    private $logger;

    /**
     * @param array<string, class-string<CommandInterface>|CommandInterface|array{class: class-string<CommandInterface>|CommandInterface, aliases?: list<string>, slash_options?: list<array{name: string, description: string, type?: int, required?: bool}>}> $commandRegistry
     * @param callable(string, string=): void|null $logger
     */
    public function __construct(array $commandRegistry, ?callable $logger = null)
    {
        $this->logger = $logger ?? static fn (string $message, string $level = 'info'): null => null;

        foreach ($commandRegistry as $name => $entry) {
            if (is_array($entry)) {
                $this->register((string) $name, $entry['class'], $entry['aliases'] ?? [], $entry['slash_options'] ?? []);
                continue;
            }

            $this->register((string) $name, $entry);
        }
    }

    /**
     * @param class-string<CommandInterface>|CommandInterface $command
     * @param list<string> $aliases
     * @param list<array{name: string, description: string, type?: int, required?: bool}> $slashOptions
     */
    public function register(string $name, string|CommandInterface $command, array $aliases = [], array $slashOptions = []): void
    {
        $normalizedName = $this->normalizeCommandName($name);
        $this->assertValidCommandName($normalizedName, sprintf('command "%s"', $name));
        $instance = is_string($command) ? new $command() : $command;

        if (!$instance instanceof CommandInterface) {
            throw new InvalidArgumentException(sprintf('Command "%s" must implement %s.', $name, CommandInterface::class));
        }

        if (isset($this->commands[$normalizedName]) || isset($this->aliases[$normalizedName])) {
            throw new InvalidArgumentException(sprintf('Duplicate command or alias "%s".', $normalizedName));
        }

        $this->commands[$normalizedName] = $instance;
        $this->slashOptions[$normalizedName] = $this->normalizeSlashOptions($slashOptions);

        foreach ($aliases as $alias) {
            $this->registerAlias((string) $alias, $normalizedName);
        }
    }

    /** @param array<string, mixed> $config */
    public function route(Discord $discord, Message $message, string $prefix, array $config): ?string
    {
        return $this->routeContent((string) $message->content, $prefix, $config, $discord, $message);
    }

    /**
     * Routes raw message content without requiring DiscordPHP objects.
     *
     * The nullable Discord and Message values are intentional for unit tests and other offline checks.
     * Production code should call route(), which always supplies the live Discord client and message.
     *
     * @param array<string, mixed> $config
     */
    public function routeContent(
        string $content,
        string $prefix,
        array $config,
        ?Discord $discord = null,
        ?Message $message = null,
    ): ?string {
        $parsed = $this->parse($content, $prefix);

        if ($parsed === null) {
            return null;
        }

        return $this->routeCommand(
            $parsed->name(),
            $parsed->arguments(),
            $prefix,
            $config,
            $discord,
            $message,
        );
    }

    /**
     * @param list<string> $arguments
     * @param array<string, mixed> $config
     */
    public function routeCommand(
        string $name,
        array $arguments,
        string $usagePrefix,
        array $config,
        ?Discord $discord = null,
        ?Message $message = null,
    ): ?string {
        $typedCommandName = $this->normalizeCommandName($name);
        $commandName = $this->aliases[$typedCommandName] ?? $typedCommandName;

        if (!isset($this->commands[$commandName])) {
            return sprintf(
                'Unknown command `%s`. Try `%s` for a list of commands.',
                $typedCommandName,
                $this->formatInvocation($usagePrefix, 'help'),
            );
        }

        $context = new CommandContext(
            discord: $discord,
            message: $message,
            commandName: $commandName,
            arguments: $arguments,
            prefix: $usagePrefix,
            config: $this->withCommandMetadata($config, $usagePrefix),
        );

        try {
            return $this->commands[$commandName]->execute($context);
        } catch (Throwable $exception) {
            ($this->logger)(sprintf(
                'Command "%s" failed: %s in %s:%d',
                $commandName,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
            ), 'warning');

            return 'Sorry, something went wrong while running that command.';
        }
    }

    /** @param array<string, mixed> $config */
    public function routeDirectMessageContent(
        string $content,
        array $config,
        ?Discord $discord = null,
        ?Message $message = null,
    ): ?string {
        $parsed = $this->parseUnprefixed($content);

        if ($parsed === null) {
            return null;
        }

        return $this->routeCommand($parsed->name(), $parsed->arguments(), '', $config, $discord, $message);
    }

    /** @param array<string, mixed> $config */
    public function routeMentionContent(
        string $content,
        string $botUserId,
        array $config,
        ?Discord $discord = null,
        ?Message $message = null,
    ): ?string {
        $mentionPrefix = $this->extractMentionPrefix($content, $botUserId);

        if ($mentionPrefix === null) {
            return null;
        }

        $withoutMention = trim(substr(trim($content), strlen($mentionPrefix)));
        $parsed = $withoutMention === '' ? new ParsedCommand('help') : $this->parseUnprefixed($withoutMention);

        if ($parsed === null) {
            return null;
        }

        return $this->routeCommand($parsed->name(), $parsed->arguments(), $mentionPrefix, $config, $discord, $message);
    }

    /** @return array<string, string> */
    public function descriptions(): array
    {
        $descriptions = [];

        foreach ($this->commands as $name => $command) {
            $descriptions[$name] = $command->description();
        }

        return $descriptions;
    }

    /** @return array<string, array{description: string, usage: string, aliases: list<string>}> */
    public function metadata(string $prefix): array
    {
        $metadata = [];

        foreach ($this->commands as $name => $command) {
            $metadata[$name] = [
                'description' => $command->description(),
                'usage' => $command->usage($prefix),
                'aliases' => $this->aliasesFor($name),
            ];
        }

        return $metadata;
    }

    /** @return list<array{name: string, description: string, options: list<array{name: string, description: string, type?: int, required?: bool}>}> */
    public function slashCommandDefinitions(): array
    {
        $definitions = [];

        foreach ($this->commands as $name => $command) {
            $definitions[] = [
                'name' => $name,
                'description' => $this->slashDescription($command->description()),
                'options' => $this->slashOptions[$name] ?? [],
            ];

            foreach ($this->aliasesFor($name) as $alias) {
                $definitions[] = [
                    'name' => $alias,
                    'description' => $this->slashDescription(sprintf('Alias for %s. %s', $name, $command->description())),
                    'options' => $this->slashOptions[$name] ?? [],
                ];
            }
        }

        return $definitions;
    }

    public function parse(string $content, string $prefix): ?ParsedCommand
    {
        $trimmedContent = trim($content);

        if ($prefix === '' || !str_starts_with($trimmedContent, $prefix)) {
            return null;
        }

        $afterPrefix = substr($trimmedContent, strlen($prefix), 1);

        if ($afterPrefix !== '' && !ctype_space($afterPrefix)) {
            return null;
        }

        $withoutPrefix = trim(substr($trimmedContent, strlen($prefix)));

        if ($withoutPrefix === '') {
            return new ParsedCommand('help');
        }

        /** @var list<string> $parts */
        $parts = preg_split('/\s+/', $withoutPrefix) ?: [];
        $commandName = $this->normalizeCommandName(array_shift($parts) ?? 'help');

        return new ParsedCommand($commandName, $parts);
    }

    private function parseUnprefixed(string $content): ?ParsedCommand
    {
        $trimmedContent = trim($content);

        if ($trimmedContent === '') {
            return null;
        }

        /** @var list<string> $parts */
        $parts = preg_split('/\s+/', $trimmedContent) ?: [];
        $commandName = $this->normalizeCommandName(array_shift($parts) ?? '');

        if ($commandName === '') {
            return null;
        }

        return new ParsedCommand($commandName, $parts);
    }

    private function extractMentionPrefix(string $content, string $botUserId): ?string
    {
        if ($botUserId === '') {
            return null;
        }

        $trimmedContent = trim($content);
        $plainMention = sprintf('<@%s>', $botUserId);
        $nicknameMention = sprintf('<@!%s>', $botUserId);

        foreach ([$plainMention, $nicknameMention] as $mention) {
            if (!str_starts_with($trimmedContent, $mention)) {
                continue;
            }

            $afterMention = substr($trimmedContent, strlen($mention), 1);

            if ($afterMention === '' || ctype_space($afterMention)) {
                return $mention;
            }
        }

        return null;
    }

    private function formatInvocation(string $prefix, string $command): string
    {
        if ($prefix === '/') {
            return sprintf('/%s', $command);
        }

        if ($prefix === '') {
            return $command;
        }

        return sprintf('%s %s', $prefix, $command);
    }

    /**
     * @param list<array{name: string, description: string, type?: int, required?: bool}> $options
     * @return list<array{name: string, description: string, type?: int, required?: bool}>
     */
    private function normalizeSlashOptions(array $options): array
    {
        $normalizedOptions = [];
        $optionNames = [];

        foreach ($options as $option) {
            $name = isset($option['name']) ? strtolower(trim((string) $option['name'])) : '';
            $description = isset($option['description']) ? $this->slashDescription((string) $option['description']) : '';

            if ($name === '' || preg_match(self::SLASH_OPTION_NAME_PATTERN, $name) !== 1) {
                throw new InvalidArgumentException(sprintf('Invalid slash option name "%s".', (string) ($option['name'] ?? '')));
            }

            if (isset($optionNames[$name])) {
                throw new InvalidArgumentException(sprintf('Duplicate slash option "%s".', $name));
            }

            if ($description === '' || strlen($description) > 100 || preg_match('/[[:cntrl:]]/', $description) === 1) {
                throw new InvalidArgumentException(sprintf('Invalid slash option description for option "%s".', $name));
            }

            $optionNames[$name] = true;

            $normalizedOption = [
                'name' => $name,
                'description' => $description,
            ];

            if (isset($option['type'])) {
                $type = (int) $option['type'];

                if (!in_array($type, self::ALLOWED_SLASH_OPTION_TYPES, true)) {
                    throw new InvalidArgumentException(sprintf('Invalid slash option type "%d" for option "%s".', $type, $name));
                }

                $normalizedOption['type'] = $type;
            }

            if (isset($option['required'])) {
                $normalizedOption['required'] = (bool) $option['required'];
            }

            $normalizedOptions[] = $normalizedOption;
        }

        return $normalizedOptions;
    }

    private function slashDescription(string $description): string
    {
        $description = trim($description);

        if ($description === '') {
            return 'Run this bot command.';
        }

        return substr($description, 0, 100);
    }

    private function registerAlias(string $alias, string $commandName): void
    {
        $normalizedAlias = $this->normalizeCommandName($alias);
        $this->assertValidCommandName($normalizedAlias, sprintf('alias "%s"', $alias));

        if ($normalizedAlias === $commandName) {
            throw new InvalidArgumentException(sprintf('Alias "%s" duplicates its command name.', $normalizedAlias));
        }

        if (isset($this->commands[$normalizedAlias]) || isset($this->aliases[$normalizedAlias])) {
            throw new InvalidArgumentException(sprintf('Duplicate alias or command name "%s".', $normalizedAlias));
        }

        $this->aliases[$normalizedAlias] = $commandName;
    }

    private function normalizeCommandName(string $name): string
    {
        return strtolower(trim($name));
    }

    private function assertValidCommandName(string $name, string $label): void
    {
        if (preg_match(self::COMMAND_NAME_PATTERN, $name) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid %s. Use 1-32 lowercase letters, numbers, underscores, or hyphens.', $label));
        }
    }

    /** @return list<string> */
    private function aliasesFor(string $commandName): array
    {
        $aliases = [];

        foreach ($this->aliases as $alias => $target) {
            if ($target === $commandName) {
                $aliases[] = $alias;
            }
        }

        sort($aliases);

        return array_values($aliases);
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function withCommandMetadata(array $config, string $prefix): array
    {
        $config['commands']['descriptions'] = $this->descriptions();
        $config['commands']['metadata'] = $this->metadata($prefix);

        return $config;
    }
}
