<?php

declare(strict_types=1);

namespace Tests;

use App\RateLimiter;
use PHPUnit\Framework\TestCase;

final class RateLimiterTest extends TestCase
{
    public function testAllowsRequestsWithinWindowAndRejectsExcess(): void
    {
        $now = 100;
        $limiter = new RateLimiter(2, 10, static fn (): int => $now);

        self::assertTrue($limiter->allows('user-1'));
        self::assertTrue($limiter->allows('user-1'));
        self::assertFalse($limiter->allows('user-1'));
    }

    public function testWindowExpiryAllowsNewRequests(): void
    {
        $now = 100;
        $limiter = new RateLimiter(1, 10, static fn (): int => $now);

        self::assertTrue($limiter->allows('user-1'));
        self::assertFalse($limiter->allows('user-1'));

        $now = 111;

        self::assertTrue($limiter->allows('user-1'));
    }

    public function testZeroMaxAttemptsDisablesRateLimit(): void
    {
        $limiter = new RateLimiter(0, 10, static fn (): int => 100);

        self::assertTrue($limiter->allows('user-1'));
        self::assertTrue($limiter->allows('user-1'));
        self::assertTrue($limiter->allows('user-1'));
    }
}
