<?php

declare(strict_types=1);

use App\Commands\EchoCommand;
use App\Commands\HelpCommand;
use App\Commands\PingCommand;
use App\Commands\SettingsCommand;
use App\Commands\TimeCommand;

return [
    // Register commands by the names users type through enabled interaction paths.
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
];
