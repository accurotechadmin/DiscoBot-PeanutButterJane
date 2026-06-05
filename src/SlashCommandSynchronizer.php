<?php

declare(strict_types=1);

namespace App;

use Discord\Builders\CommandBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Option;
use Throwable;

final class SlashCommandSynchronizer
{
    private Discord $discord;

    /** @var callable(string, string=): void */
    private $logger;

    /** @param array<string, mixed> $config */
    public function __construct(
        private readonly array $config,
        private readonly CommandRouter $router,
        ?callable $logger = null,
    ) {
        $this->logger = $logger ?? static fn (string $message, string $level = 'info'): null => null;
        $botConfig = $this->config['bot'] ?? [];
        $token = is_array($botConfig) && isset($botConfig['token']) ? (string) $botConfig['token'] : '';

        if ($token === '') {
            throw new \RuntimeException('Missing DISCORD_BOT_TOKEN. Copy .env.example to .env and set your bot token.');
        }

        $this->discord = new Discord([
            'token' => $token,
            'storeMessages' => false,
            'loadAllMembers' => false,
            'retrieveBans' => false,
        ]);
    }

    public function sync(): void
    {
        ($this->logger)('Connecting to Discord to synchronize slash command definitions.');

        $this->discord->on('ready', function (Discord $discord): void {
            $this->syncOnReady($discord);
            $this->shutdownDiscord($discord);
        });

        $this->discord->run();
    }

    private function syncOnReady(Discord $discord): void
    {
        $definitions = $this->router->slashCommandDefinitions();
        ($this->logger)(sprintf('Synchronizing %d slash command definition(s).', count($definitions)));

        foreach ($definitions as $definition) {
            try {
                $builder = CommandBuilder::new()
                    ->setName($definition['name'])
                    ->setDescription($definition['description']);

                foreach ($definition['options'] as $optionDefinition) {
                    $builder->addOption((new Option($discord))
                        ->setName($optionDefinition['name'])
                        ->setDescription($optionDefinition['description'])
                        ->setType($optionDefinition['type'] ?? Option::STRING)
                        ->setRequired($optionDefinition['required'] ?? false));
                }

                $discord->application->commands->save(
                    $discord->application->commands->create($builder->toArray()),
                );
                ($this->logger)(sprintf('Synchronized slash command "/%s".', $definition['name']), 'info');
            } catch (Throwable $exception) {
                ($this->logger)(sprintf(
                    'Failed to synchronize slash command "/%s": %s',
                    $definition['name'],
                    $exception->getMessage(),
                ), 'warning');
            }
        }
    }

    private function shutdownDiscord(Discord $discord): void
    {
        if (method_exists($discord, 'close')) {
            $discord->close();
            return;
        }

        ($this->logger)('Slash command synchronization finished, but DiscordPHP close() is unavailable.', 'warning');
    }
}
