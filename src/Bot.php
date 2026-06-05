<?php

declare(strict_types=1);

namespace App;

use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Throwable;

final class Bot
{
    private Discord $discord;

    /** @var callable(string, string=): void */
    private $logger;

    /** @param array<string, mixed> $config */
    public function __construct(
        private readonly array $config,
        private readonly CommandRouter $router,
        ?callable $logger = null,
        private readonly ?RuntimeLifecycle $lifecycle = null,
        private ?RateLimiter $rateLimiter = null,
    ) {
        $this->logger = $logger ?? static fn (string $message, string $level = 'info'): null => null;
        $this->rateLimiter ??= RateLimiter::fromConfig($config);
        $botConfig = $this->config['bot'] ?? [];
        $token = is_array($botConfig) && isset($botConfig['token']) ? (string) $botConfig['token'] : '';

        if ($token === '') {
            throw new \RuntimeException('Missing DISCORD_BOT_TOKEN. Copy .env.example to .env and set your bot token.');
        }

        $intents = Intents::getDefaultIntents();

        if ($this->usesMessageContent()) {
            $intents |= Intents::MESSAGE_CONTENT;
        }

        if ($this->interactionEnabled('dm_commands')) {
            $intents |= Intents::DIRECT_MESSAGES;
        }

        $this->discord = new Discord([
            'token' => $token,
            'intents' => $intents,
            'storeMessages' => false,
            'loadAllMembers' => false,
            'retrieveBans' => false,
        ]);
    }

    public function run(): void
    {
        $prefix = $this->prefix();

        ($this->logger)(sprintf('Connecting to Discord with prefix "%s"...', $prefix));
        ($this->logger)(sprintf('Enabled interaction paths: %s.', implode(', ', $this->enabledInteractionLabels())));

        if ($this->lifecycle !== null) {
            $this->lifecycle->registerShutdownLogger();
            $this->lifecycle->installSignalHandlers(function (int $_signal): void {
                $this->shutdownDiscord();
            });
        }

        $this->discord->on('ready', function (Discord $discord) use ($prefix): void {
            $username = $discord->user?->username ?? 'unknown';
            ($this->logger)(sprintf('Bot is ready as %s. Listening for enabled command interaction paths.', $username));

            if ($this->usesMessageContent()) {
                $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($prefix): void {
                    $this->handleMessage($discord, $message, $prefix);
                });
            }

            if ($this->interactionEnabled('slash_commands')) {
                ($this->logger)('Slash command listeners enabled. Run `composer sync-slash-commands` after command registry changes.', 'info');
                $this->listenForSlashCommands($discord);
            }
        });

        $this->discord->run();
    }

    private function handleMessage(Discord $discord, Message $message, string $prefix): void
    {
        if ($this->isBotMessage($discord, $message)) {
            return;
        }

        $reply = null;

        if ($this->messageCouldTriggerCommand($discord, $message, $prefix) && !$this->allowMessageCommand($message)) {
            $reply = 'Rate limit exceeded. Try again shortly.';
        } elseif ($this->isDirectMessage($message)) {
            if ($this->interactionEnabled('dm_commands')) {
                $reply = $this->router->routeDirectMessageContent((string) $message->content, $this->config, $discord, $message);
            }
        } else {
            $botId = isset($discord->user->id) ? (string) $discord->user->id : '';

            if ($this->interactionEnabled('mention_commands') && $botId !== '') {
                $reply = $this->router->routeMentionContent((string) $message->content, $botId, $this->config, $discord, $message);
            }

            if ($reply === null && $this->interactionEnabled('prefix_commands')) {
                $reply = $this->router->route($discord, $message, $prefix, $this->config);
            }
        }

        if ($reply === null || $reply === '') {
            return;
        }

        try {
            $message->channel->sendMessage($reply);
        } catch (Throwable $exception) {
            ($this->logger)(sprintf('Failed to send reply: %s', $exception->getMessage()), 'warning');
        }
    }

    private function handleSlashInteraction(Discord $discord, Interaction $interaction): void
    {
        $commandName = isset($interaction->data->name) ? (string) $interaction->data->name : '';

        if ($commandName === '') {
            return;
        }

        if (!$this->allowSlashCommand($interaction)) {
            $reply = 'Rate limit exceeded. Try again shortly.';
        } else {
            $reply = $this->router->routeCommand(
                $commandName,
                $this->interactionArguments($interaction),
                '/',
                $this->config,
                $discord,
            );
        }

        if ($reply === null || $reply === '') {
            return;
        }

        try {
            $interaction->respondWithMessage(MessageBuilder::new()->setContent($reply), true);
        } catch (Throwable $exception) {
            ($this->logger)(sprintf('Failed to send slash command reply: %s', $exception->getMessage()), 'warning');
        }
    }

    private function isBotMessage(Discord $discord, Message $message): bool
    {
        $author = $message->author;

        if (($author->bot ?? false) === true) {
            return true;
        }

        $authorId = isset($author->id) ? (string) $author->id : '';
        $botId = isset($discord->user->id) ? (string) $discord->user->id : '';

        return $authorId !== '' && $botId !== '' && $authorId === $botId;
    }

    private function isDirectMessage(Message $message): bool
    {
        $guildId = isset($message->guild_id) ? (string) $message->guild_id : '';

        return $guildId === '';
    }

    private function prefix(): string
    {
        $botConfig = $this->config['bot'] ?? [];

        return is_array($botConfig) && isset($botConfig['prefix']) ? (string) $botConfig['prefix'] : '!bot';
    }

    private function interactionEnabled(string $key): bool
    {
        $botConfig = $this->config['bot'] ?? [];
        $interactions = is_array($botConfig) && isset($botConfig['interactions']) && is_array($botConfig['interactions'])
            ? $botConfig['interactions']
            : [];

        $default = $key === 'prefix_commands' ? 'true' : 'false';

        return ConfigValidator::booleanValue($interactions[$key] ?? $default, strtoupper($key));
    }

    private function usesMessageContent(): bool
    {
        return $this->interactionEnabled('prefix_commands')
            || $this->interactionEnabled('mention_commands')
            || $this->interactionEnabled('dm_commands');
    }

    /** @return list<string> */
    private function enabledInteractionLabels(): array
    {
        $labels = [];

        foreach ([
            'prefix_commands' => 'prefix',
            'slash_commands' => 'slash',
            'mention_commands' => 'mention',
            'dm_commands' => 'direct message',
        ] as $key => $label) {
            if ($this->interactionEnabled($key)) {
                $labels[] = $label;
            }
        }

        return $labels === [] ? ['none'] : $labels;
    }

    private function listenForSlashCommands(Discord $discord): void
    {
        foreach ($this->router->slashCommandDefinitions() as $definition) {
            $discord->listenCommand($definition['name'], function (Interaction $interaction) use ($discord): void {
                $this->handleSlashInteraction($discord, $interaction);
            });
        }
    }

    /** @return list<string> */
    private function interactionArguments(Interaction $interaction): array
    {
        return $this->collectInteractionArguments($interaction->data->options ?? null);
    }

    /** @return list<string> */
    private function collectInteractionArguments(mixed $options): array
    {
        if ($options === null) {
            return [];
        }

        $arguments = [];

        if (is_iterable($options)) {
            foreach ($options as $option) {
                foreach ($this->collectInteractionArguments($option) as $argument) {
                    $arguments[] = $argument;
                }
            }

            return $arguments;
        }

        if (is_object($options) && isset($options->options)) {
            foreach ($this->collectInteractionArguments($options->options) as $argument) {
                $arguments[] = $argument;
            }
        }

        if (is_object($options) && isset($options->value)) {
            foreach (preg_split('/\s+/', trim((string) $options->value)) ?: [] as $argument) {
                if ($argument !== '') {
                    $arguments[] = $argument;
                }
            }
        }

        return $arguments;
    }

    private function messageCouldTriggerCommand(Discord $discord, Message $message, string $prefix): bool
    {
        $content = (string) $message->content;

        if ($this->isDirectMessage($message)) {
            return $this->interactionEnabled('dm_commands') && trim($content) !== '';
        }

        $botId = isset($discord->user->id) ? (string) $discord->user->id : '';

        if ($this->interactionEnabled('mention_commands') && $botId !== '') {
            $trimmed = trim($content);
            if (str_starts_with($trimmed, sprintf('<@%s>', $botId)) || str_starts_with($trimmed, sprintf('<@!%s>', $botId))) {
                return true;
            }
        }

        return $this->interactionEnabled('prefix_commands') && str_starts_with(trim($content), $prefix);
    }

    private function allowMessageCommand(Message $message): bool
    {
        return $this->rateLimiter?->allows($this->messageRateLimitKey($message)) ?? true;
    }

    private function allowSlashCommand(Interaction $interaction): bool
    {
        return $this->rateLimiter?->allows($this->slashRateLimitKey($interaction)) ?? true;
    }

    private function messageRateLimitKey(Message $message): string
    {
        $authorId = isset($message->author->id) ? (string) $message->author->id : 'unknown-user';

        return sprintf('message:%s', $authorId);
    }

    private function slashRateLimitKey(Interaction $interaction): string
    {
        $userId = isset($interaction->user->id) ? (string) $interaction->user->id : '';

        if ($userId === '' && isset($interaction->member->user->id)) {
            $userId = (string) $interaction->member->user->id;
        }

        return sprintf('slash:%s', $userId === '' ? 'unknown-user' : $userId);
    }

    private function shutdownDiscord(): void
    {
        if (method_exists($this->discord, 'close')) {
            $this->discord->close();
            return;
        }

        ($this->logger)('Discord runtime shutdown was requested, but DiscordPHP close() is unavailable.', 'warning');
    }
}
