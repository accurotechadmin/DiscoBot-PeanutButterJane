<?php

declare(strict_types=1);

namespace App;

final class RateLimiter
{
    /** @var array<string, list<int>> */
    private array $hits = [];

    public function __construct(
        private readonly int $maxAttempts,
        private readonly int $windowSeconds,
        private readonly mixed $clock = null,
    ) {
    }

    public static function fromConfig(array $config): self
    {
        $botConfig = $config['bot'] ?? [];
        $rateLimit = is_array($botConfig) && isset($botConfig['rate_limit']) && is_array($botConfig['rate_limit'])
            ? $botConfig['rate_limit']
            : [];

        return new self(
            maxAttempts: max(0, (int) ($rateLimit['max_attempts'] ?? 5)),
            windowSeconds: max(1, (int) ($rateLimit['window_seconds'] ?? 10)),
        );
    }

    public function allows(string $key): bool
    {
        if ($this->maxAttempts === 0) {
            return true;
        }

        $now = $this->now();
        $windowStart = $now - $this->windowSeconds;
        $existingHits = $this->hits[$key] ?? [];
        $activeHits = array_values(array_filter(
            $existingHits,
            static fn (int $timestamp): bool => $timestamp > $windowStart,
        ));

        if (count($activeHits) >= $this->maxAttempts) {
            $this->hits[$key] = $activeHits;

            return false;
        }

        $activeHits[] = $now;
        $this->hits[$key] = $activeHits;

        return true;
    }

    private function now(): int
    {
        if (is_callable($this->clock)) {
            return (int) ($this->clock)();
        }

        return time();
    }
}
