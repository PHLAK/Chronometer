<?php

namespace PHLAK\Chronometer;

readonly class Lap
{
    /**
     * Create a new Lap object.
     *
     * @param float $time The current time
     * @param float $duration The duration of the lap
     * @param string $description A description of the lap
     */
    public function __construct(
        public float $time,
        public float $duration,
        public string $description = ''
    ) {}
}
