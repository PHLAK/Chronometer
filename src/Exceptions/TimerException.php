<?php

namespace PHLAK\Chronometer\Exceptions;

class TimerException extends ChronometerException
{
    public const CODE_NOT_STARTED = 1;
    public const CODE_NOT_STOPPED = 2;
    public const CODE_REQUIRES_RESET = 3;

    /** Create a timer not started exception. */
    public static function notStarted(): self
    {
        return new self('Timer must be started first', self::CODE_NOT_STARTED);
    }

    /** Create a timer not stopped exception. */
    public static function notStopped(): self
    {
        return new self('Timer must be stopped first', self::CODE_NOT_STOPPED);
    }

    /** Create a timer requires restart exception. */
    public static function requiresReset(): self
    {
        return new self('Timer must be reset', self::CODE_REQUIRES_RESET);
    }
}
