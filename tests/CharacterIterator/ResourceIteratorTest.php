<?php

namespace WebDevCraft\JsonReader\CharacterIterator;

use PHPUnit\Framework\TestCase;

class ResourceIteratorTest extends TestCase
{
    /**
     * @var ResourceIterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $expectedCharacters;

    public function testIterator()
    {
        $actualCharacters = [];
        foreach ($this->iterator as $item) {
            $actualCharacters[] = $item;
        }
        $this->assertEquals($this->expectedCharacters, $actualCharacters);
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $filePath = __DIR__.'/../data/test.json';
        $this->expectedCharacters = str_split(file_get_contents($filePath));
        $resource = fopen($filePath, 'r');
        $this->iterator = new ResourceIterator($resource);
    }
}
