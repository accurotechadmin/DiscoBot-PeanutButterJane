<?php

declare(strict_types=1);

namespace App;

final class ParsedCommand
{
    /** @param list<string> $arguments */
    public function __construct(
        private readonly string $name,
        private readonly array $arguments = [],
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return list<string> */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
