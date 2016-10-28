<?php

namespace WebDevCraft\JsonReader\CharacterIterator;

use PHPUnit\Framework\TestCase;

class FileStreamCharacterIteratorTest extends TestCase
{
    /**
     * @var FileStreamCharacterIterator
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
        $this->iterator = new FileStreamCharacterIterator($filePath);
    }
}
