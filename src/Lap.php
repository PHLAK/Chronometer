<?php

namespace PHLAK\Chronometer;

class Lap
{
    /** @var float Lap time */
    protected $time;

    /** @var float Lap duration */
    protected $duration;

    /** @var string A lap description */
    protected $description;

    /**
     * Create a new Lap object.
     *
     * @param float $time The current time
     * @param float $duration The duration of the lap
     * @param string $description A description of the lap
     */
    public function __construct(float $time, float $duration, string $description = '')
    {
        $this->time = $time;
        $this->duration = $duration;
        $this->description = $description;
    }

    /**
     * Return the value of a property.
     *
     * @param string $property The property to return
     *
     * @return mixed The value of the property
     */
    public function __get($property)
    {
        return $this->{$property};
    }
}
