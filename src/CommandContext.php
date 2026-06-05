<?php

declare(strict_types=1);

namespace App;

use Discord\Discord;
use Discord\Parts\Channel\Message;

final class CommandContext
{
    /**
     * @param list<string> $arguments
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly ?Discord $discord,
        private readonly ?Message $message,
        private readonly string $commandName,
        private readonly array $arguments,
        private readonly string $prefix,
        private readonly array $config,
    ) {
    }

    /**
     * Returns the live DiscordPHP client during normal bot execution.
     *
     * This may be null when commands are exercised through CommandRouter::routeContent()
     * or direct unit tests, allowing command logic to be tested without a Discord network connection.
     */
    public function discord(): ?Discord
    {
        return $this->discord;
    }

    /**
     * Returns the original DiscordPHP message during normal bot execution.
     *
     * This may be null when commands are exercised through CommandRouter::routeContent()
     * or direct unit tests, allowing command logic to be tested without DiscordPHP message fakes.
     */
    public function message(): ?Message
    {
        return $this->message;
    }

    public function hasDiscord(): bool
    {
        return $this->discord !== null;
    }

    public function hasMessage(): bool
    {
        return $this->message !== null;
    }

    public function commandName(): string
    {
        return $this->commandName;
    }

    /** @return list<string> */
    public function arguments(): array
    {
        return $this->arguments;
    }

    public function prefix(): string
    {
        return $this->prefix;
    }

    /** @return array<string, mixed> */
    public function config(): array
    {
        return $this->config;
    }
}
