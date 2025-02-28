<?php declare(strict_types=1);

namespace time;

final class WallClock implements Clock
{
    /** @var \Closure(): array{int, int<0, 999999999>} */
    private static \Closure $globalTimer;
    private static Duration $globalTimerResolution;

    public readonly Duration $resolution;

    /** @var \Closure(): array{int, int<0,999999999>} */
    private readonly \Closure $timer;

    public function __construct(
        public readonly Duration $modifier = new Duration(),
    ) {
        if (!isset(self::$globalTimer, self::$globalTimerResolution)) {
            // \microtime() function is only available on operating systems
            // that support the gettimeofday() system call.
            if (\function_exists('microtime')) {
                /** @phpstan-ignore assign.propertyType */
                self::$globalTimer = static function () {
                    [$us, $s] = \explode(' ', \microtime(), 2);
                    return [(int)$s, (int)\substr($us, 2, -2) * 1_000];
                };
                self::$globalTimerResolution = new Duration(microseconds: 1);
            } else {
                self::$globalTimer = static function () {
                    return [\time(), 0];
                };
                self::$globalTimerResolution = new Duration(seconds: 1);
            }
        }

        $this->resolution = self::$globalTimerResolution;

        // Setup timer including modifier
        if ($modifier->isZero) {
            $this->timer = self::$globalTimer;
        } else {
            $this->timer = static function () use ($modifier) {
                return $modifier->addToUnixTimestampTuple((self::$globalTimer)());
            };
        }
    }

    public function takeMoment(): Moment
    {
        return Moment::fromUnixTimestampTuple(($this->timer)());
    }

    public function takeZonedDateTime(Zone $zone): ZonedDateTime
    {
        return $this->takeMoment()->toZonedDateTime($zone);
    }

    public function takeUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float
    {
        return $this->takeMoment()->toUnixTimestamp($unit, $fractions);
    }

    /** @return array{int, int<0, 999999999>} */
    public function takeUnixTimestampTuple(): array
    {
        return ($this->timer)();
    }
}
