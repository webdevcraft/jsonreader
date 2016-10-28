<?php

namespace WebDevCraft\JsonReader\Struct;

use WebDevCraft\JsonReader\JsonReader;

abstract class AbstractStruct
{
    /**
     * @var mixed
     */
    private $state;

    /**
     * @var JsonReader
     */
    protected $reader;

    /**
     * @param \WebDevCraft\JsonReader\JsonReader $reader
     */
    final public function __construct(JsonReader $reader)
    {
        $this->reader = $reader;
    }

    abstract public function read();

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}
