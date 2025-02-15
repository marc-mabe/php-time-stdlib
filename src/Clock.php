<?php declare(strict_types=1);

namespace time;

interface Clock
{
    public Duration $resolution { get; }

    public function takeMoment(): Moment;

    public function takeZonedDateTime(Zone $zone): ZonedDateTime;

    public function takeUnixTimestamp(TimeUnit $unit = TimeUnit::Second, bool $fractions = false): int|float;

    /** @return array{int, int<0, 999999999>} */
    public function takeUnixTimestampTuple(): array;
}
