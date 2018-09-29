<?php

namespace PHLAK\Chronometer;

use PHLAK\Chronometer\Exceptions\TimerException;

class Timer
{
    /** @var float Start time in microseconds */
    protected static $started;

    /** @var float End time in micoseconds */
    protected static $stopped;

    /** @var \PHLAK\Chronometer\Lap The last lap */
    protected static $lastLap;

    /** @var array Array of laps */
    protected static $laps = [];

    /**
     * Start the timer.
     *
     * @return float Start time in microseconds
     */
    public static function start() : float
    {
        if (! empty(self::$started)) {
            throw new TimerException('Timer already running, must reset timer before starting again');
        }

        self::$started = microtime(true);
        // self::$laps[] = self::addLap(self::$started, 0);
        self::$lastLap = new Lap(self::$started, 0);
        self::$laps[] = self::$lastLap;

        return self::$started;
    }

    /**
     * Stop the timer.
     *
     * @return float End time in microseconds
     */
    public static function stop() : float
    {
        if (empty(self::$started)) {
            throw new TimerException('Timer must be started before stopping');
        }

        self::$stopped = microtime(true);
        // self::$laps[] = self::addLap(self::$stopped);
        self::$lastLap = new Lap(self::$stopped, self::$stopped - self::$lastLap->time);
        self::$laps[] = self::$lastLap;

        return self::$stopped;
    }

    /**
     * Add a new lap.
     *
     * @return \PHLAK\Chronometer\Lap A Lap object
     */
    public static function addLap() : Lap
    {
        if (empty(self::$started)) {
            throw new TimerException('Timer must be started first');
        }

        if (! empty(self::$stopped)) {
            throw new TimerException('Cannot add a lap after timer has been stopped');
        }

        $now = microtime(true);
        $duration = $now - self::$lastLap->time;

        self::$lastLap = new Lap($now, $duration);
        self::$laps[] = self::$lastLap;

        return self::$lastLap;
    }

    /**
     * Return the timer start time.
     *
     * @return float The timer start time
     */
    public static function started() : float
    {
        if (empty(self::$started)) {
            throw new TimerException('Timer must be started first');
        }

        return self::$started;
    }

    /**
     * Return the timer stop time.
     *
     * @return float The timer stop time
     */
    public static function stopped() : float
    {
        if (empty(self::$stopped)) {
            throw new TimerException('Timer must be started and stopped first');
        }

        return self::$stopped;
    }

    /**
     * Return the total time elapsed in seconds.
     *
     * @return float elapsed time in microseconds
     */
    public static function elapsed() : float
    {
        if (empty(self::$started)) {
            throw new TimerException('Timer must be started first');
        }

        return (self::$stopped ?? microtime(true)) - self::$started;
    }

    /**
     * Return the last lap.
     *
     * @return \PHLAK\Chronometer\Lap The last Lap object
     */
    public static function lastLap() : Lap
    {
        if (empty(self::$lastLap)) {
            throw new TimerException('Timer must be started first');
        }

        return self::$lastLap;
    }

    /**
     * Return the array of laps.
     *
     * @return array Array of Lap objects
     */
    public static function laps() : array
    {
        if (empty(self::$laps)) {
            throw new TimerException('Timer must be started first');
        }

        return self::$laps;
    }

    /**
     * Reset the timer state.
     *
     * @return void
     */
    public static function reset() : void
    {
        self::$started = null;
        self::$stopped = null;
        self::$lastLap = null;
        self::$laps = [];
    }
}
