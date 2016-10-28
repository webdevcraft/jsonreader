<?php

namespace WebDevCraft\JsonReader\CharacterIterator;

use PHPUnit\Framework\TestCase;

class StringCharacterIteratorTest extends TestCase
{
    /**
     * @var StringCharacterIterator
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
        $string = '{"a": false,  "b": 2}';
        $this->expectedCharacters = str_split($string);
        $this->iterator = new StringCharacterIterator($string);
    }
}
