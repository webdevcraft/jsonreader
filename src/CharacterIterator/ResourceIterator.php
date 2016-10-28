<?php

namespace WebDevCraft\JsonReader\CharacterIterator;

class ResourceIterator implements \IteratorAggregate
{
    /**
     * @var resource
     */
    private $resource;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        while (!feof($this->resource)) {
            $string = fread($this->resource, 4048);
            $characters = str_split($string);
            foreach ($characters as $character) {
                yield $character;
            }
        }
    }
}
