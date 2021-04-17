<?php

namespace PHLAK\Chronometer\Tests;

use PHLAK\Chronometer\Exceptions\TimerException;
use PHLAK\Chronometer\Lap;
use PHLAK\Chronometer\Timer;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class TimerTest extends TestCase
{
    /** @var string Microtime regular expression */
    protected $microtimeRegex = '/[0-9]{10}(\.[0-9]+)?/';

    protected function tearDown(): void
    {
        Timer::reset();
    }

    public function testItCanStartATimer()
    {
        $start = Timer::start();
        $lastLap = Timer::lastLap();

        $this->assertMatchesRegularExpression($this->microtimeRegex, (string) $start);
        $this->assertEquals(new Lap($start, 0), $lastLap);
    }

    public function testItCanEndATimer()
    {
        $start = Timer::start();
        $end = Timer::stop();
        $lastLap = Timer::lastLap();

        $this->assertMatchesRegularExpression($this->microtimeRegex, (string) $end);
        $this->assertEquals(new Lap($end, $end - $start), $lastLap);
    }

    public function testItCanGetTheTimeElapsed()
    {
        Timer::start();
        usleep(5000);

        $elapsed = Timer::elapsed();

        $this->assertMatchesRegularExpression('/0\.005[0-9]+/', (string) $elapsed);
    }

    public function testItCanGetTheTotalTimeElapsed()
    {
        Timer::start();
        usleep(10000);
        Timer::stop();

        $elapsed = Timer::elapsed();

        $this->assertMatchesRegularExpression('/0\.01[0-9]+/', (string) $elapsed);
    }

    public function testItCanAddALap()
    {
        $start = Timer::start();
        usleep(2000);
        $lap = Timer::addLap();
        usleep(10000);
        $end = Timer::stop();

        $laps = Timer::laps();

        $this->assertInstanceOf(Lap::class, $lap);
        $this->assertMatchesRegularExpression($this->microtimeRegex, (string) $lap->time);
        $this->assertMatchesRegularExpression('/0\.[0-9]+/', (string) $lap->duration);
        $this->assertEmpty($lap->description);

        $this->assertEquals([new Lap($start, 0), $lap, new Lap($end, $end - $lap->time)], $laps);
    }

    public function testItCanNotBeStartedTwice()
    {
        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Timer already running, must reset timer before starting again');

        Timer::start();
        Timer::start();
    }

    public function testItCanBeStartedTwiceWithAParameter()
    {
        Timer::start();
        $start = Timer::start($reset = true);
        $lastLap = Timer::lastLap();

        $this->assertMatchesRegularExpression($this->microtimeRegex, (string) $start);
        $this->assertEquals(new Lap($start, 0), $lastLap);
    }

    public function testItCannotBeStoppedWithoutBeingStarted()
    {
        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Timer must be started before stopping');

        Timer::stop();
    }

    public function testItCanGetTheStartTime()
    {
        $start = Timer::start();
        usleep(1000);
        $started = Timer::started();

        $this->assertEquals($start, $started);
    }

    public function testItCanGetTheStopTime()
    {
        Timer::start();
        usleep(1000);
        $stop = Timer::stop();

        $stopped = Timer::stopped();

        $this->assertEquals($stop, $stopped);
    }

    public function testItCanNotGetAnElapsedTimeWithoutBeingStarted()
    {
        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Timer must be started first');

        Timer::elapsed();
    }

    public function testItCanNotGetTheStartTimeWithoutBeingStarted()
    {
        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Timer must be started first');

        Timer::started();
    }

    public function testItCanNotGetTheStoppedTimeWithoutBeingStarted()
    {
        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Timer must be started and stopped first');

        Timer::stopped();
    }

    public function testItCanNotGetTheStoppedTimeWithoutBeingStopped()
    {
        Timer::start();

        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Timer must be started and stopped first');

        Timer::stopped();
    }

    public function testItCanNotAddALapWithoutBeingStarted()
    {
        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Timer must be started first');

        Timer::addLap();
    }

    public function testItCannotAddALapAfterBeingStopped()
    {
        Timer::start();
        Timer::stop();

        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Cannot add a lap after timer has been stopped');

        Timer::addLap();
    }

    public function testItCanNotGetTheLastLapWithoutBeingStarted()
    {
        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Timer must be started first');

        Timer::lastLap();
    }

    public function testItCanNotGetAnArrayOfLapsWithoutBeingStarted()
    {
        $this->expectException(TimerException::class);
        $this->expectExceptionMessage('Timer must be started first');

        Timer::laps();
    }

    public function testItCanGiveALapADescription()
    {
        $start = Timer::start();
        usleep(1000);
        $lap = Timer::addLap('The first lap.');

        $this->assertEquals('The first lap.', $lap->description);
    }
}
