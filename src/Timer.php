<?php

namespace PHLAK\Chronometer;

use PHLAK\Chronometer\Exceptions\TimerException;

class Timer
{
    /** Start time in microseconds */
    private static ?float $started;

    /** End time in micoseconds */
    private static ?float $stopped;

    /** The last lap */
    private static ?Lap $lastLap;

    /** @var list<Lap> Array of laps */
    private static array $laps = [];

    /**
     * Start the timer.
     *
     * @param bool $reset if true, reset the timer before running
     *
     * @throws \PHLAK\Chronometer\Exceptions\TimerException
     *
     * @return float Start time in microseconds
     */
    public static function start($reset = false): float
    {
        if ($reset) {
            self::reset();
        }

        if (! empty(self::$started)) {
            throw TimerException::requiresReset();
        }

        self::$started = microtime(true);
        self::$lastLap = new Lap(self::$started, 0);
        self::$laps[] = self::$lastLap;

        return self::$started;
    }

    /**
     * Stop the timer.
     *
     * @throws \PHLAK\Chronometer\Exceptions\TimerException
     *
     * @return float End time in microseconds
     */
    public static function stop(): float
    {
        if (empty(self::$started)) {
            throw TimerException::notStarted();
        }

        self::$stopped = microtime(true);
        self::$lastLap = new Lap(self::$stopped, self::$stopped - self::$lastLap?->time);
        self::$laps[] = self::$lastLap;

        return self::$stopped;
    }

    /**
     * Add a new lap.
     *
     * @param string $description A lap description
     *
     * @throws \PHLAK\Chronometer\Exceptions\TimerException
     *
     * @return \PHLAK\Chronometer\Lap A Lap object
     */
    public static function addLap(string $description = ''): Lap
    {
        if (empty(self::$started)) {
            throw TimerException::notStarted();
        }

        if (! empty(self::$stopped)) {
            throw TimerException::requiresReset();
        }

        $now = microtime(true);
        $duration = $now - self::$lastLap?->time;

        self::$lastLap = new Lap($now, $duration, $description);
        self::$laps[] = self::$lastLap;

        return self::$lastLap;
    }

    /**
     * Return the timer start time.
     *
     * @throws \PHLAK\Chronometer\Exceptions\TimerException
     *
     * @return float The timer start time
     */
    public static function started(): float
    {
        if (empty(self::$started)) {
            throw TimerException::notStarted();
        }

        return self::$started;
    }

    /**
     * Return the timer stop time.
     *
     * @throws \PHLAK\Chronometer\Exceptions\TimerException
     *
     * @return float The timer stop time
     */
    public static function stopped(): float
    {
        if (empty(self::$started)) {
            throw TimerException::notStarted();
        }

        if (empty(self::$stopped)) {
            throw TimerException::notStopped();
        }

        return self::$stopped;
    }

    /**
     * Return the total time elapsed in seconds.
     *
     * @throws \PHLAK\Chronometer\Exceptions\TimerException
     *
     * @return float elapsed time in microseconds
     */
    public static function elapsed(): float
    {
        if (empty(self::$started)) {
            throw TimerException::notStarted();
        }

        return (self::$stopped ?? microtime(true)) - self::$started;
    }

    /**
     * Return the last lap.
     *
     * @throws \PHLAK\Chronometer\Exceptions\TimerException
     *
     * @return \PHLAK\Chronometer\Lap The last Lap object
     */
    public static function lastLap(): Lap
    {
        if (empty(self::$lastLap)) {
            throw TimerException::notStarted();
        }

        return self::$lastLap;
    }

    /**
     * Return an array of all laps.
     *
     * @throws \PHLAK\Chronometer\Exceptions\TimerException
     *
     * @return list<Lap> Array of Lap objects
     */
    public static function laps(): array
    {
        if (empty(self::$laps)) {
            throw TimerException::notStarted();
        }

        return self::$laps;
    }

    /** Reset the timer state. */
    public static function reset(): void
    {
        self::$started = null;
        self::$stopped = null;
        self::$lastLap = null;
        self::$laps = [];
    }
}
