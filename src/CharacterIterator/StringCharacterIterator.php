<?php

namespace WebDevCraft\JsonReader\CharacterIterator;

class StringCharacterIterator implements \Iterator
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var int
     */
    private $position;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return substr($this->string, $this->position, 1);
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->position < strlen($this->string);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->position = 0;
    }

}
