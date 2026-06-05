<?php

declare(strict_types=1);

namespace App\Commands;

use App\CommandContext;

interface CommandInterface
{
    public function execute(CommandContext $context): string;

    public function description(): string;

    public function usage(string $prefix): string;
}
