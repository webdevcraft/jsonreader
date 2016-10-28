<?php

namespace WebDevCraft\JsonReader;

use PHPUnit\Framework\TestCase;
use WebDevCraft\JsonReader\CharacterIterator\FileStreamCharacterIterator;
use WebDevCraft\JsonReader\Exception\InternalErrorException;

class JsonReaderCharacterIteratorTest extends TestCase
{
    /**
     * @var JsonReaderCharacterIterator
     */
    private $readerIterator;

    public function testCurrentNext()
    {
        $iterator = $this->readerIterator;

        $this->assertEquals(null, $iterator->getCurrent());

        $this->assertEquals('{', $iterator->getNext());

        $this->assertEquals('{', $iterator->getCurrent());

        $iterator->getNext();
        $this->assertEquals('"', $iterator->getCurrent());
    }

    public function testPrevious()
    {
        $iterator = $this->readerIterator;
        $iterator->getNext();
        $iterator->getNext();

        $previousChar = $iterator->getCurrent();
        $currentChar = $iterator->getNext();

        $this->assertNotEquals($previousChar, $currentChar);

        $this->assertEquals($previousChar, $iterator->getPrevious());

        $iterator->prev();
        $this->assertEquals($previousChar, $iterator->getCurrent());

        try {
            $iterator->getPrevious();
            $this->assertTrue(false);
        } catch (InternalErrorException $exception) {
            $this->assertTrue(true);
        }

        try {
            $iterator->prev();
            $this->assertTrue(false);
        } catch (InternalErrorException $exception) {
            $this->assertTrue(true);
        }
    }

    public function testNextIncludingWhite()
    {
        $iterator = $this->readerIterator;

        for ($i = 0; $i < 6; ++$i) {
            $iterator->getNext();
        }
        $this->assertEquals(' ', $iterator->getCurrent());
    }

    public function testNextNonWhite()
    {
        $iterator = $this->readerIterator;

        for ($i = 0; $i < 6; ++$i) {
            $iterator->getNextNonWhite();
        }
        $this->assertEquals('f', $iterator->getCurrent());
    }

    /**
     * @return void
     */
    public function testChunkBufferFromStart()
    {
        $readerIterator = $this->readerIterator;
        $readerIterator->startBuffer();
        for ($i = 0; $i < 13; ++$i) {
            $readerIterator->getNext();
        }
        $buffer = $readerIterator->endBuffer();
        $this->assertEquals('{"a":  false,', $buffer);
    }

    /**
     * @return void
     */
    public function testChunkBufferFromSecondChar()
    {
        $iterator = $this->readerIterator;

        $iterator->getNext();
        $iterator->getNext();
        $iterator->startBuffer();
        for ($i = 0; $i < 11; ++$i) {
            $iterator->getNext();
        }
        $buffer = $iterator->endBuffer();
        $this->assertEquals(',', $iterator->getCurrent());
        $this->assertEquals('"a":  false,', $buffer);

        $iterator->getNext();
        $this->assertEquals(' ', $iterator->getCurrent());
        $iterator->startBuffer();
        for ($i = 0; $i < 4; ++$i) {
            $iterator->getNext();
        }
        $buffer = $iterator->endBuffer();
        $this->assertEquals('  "b"', $buffer);
        $this->assertEquals('"', $iterator->getCurrent());
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $filePath = __DIR__.'/data/test.json';
        $characterIterator = new FileStreamCharacterIterator($filePath);
        $this->readerIterator = new JsonReaderCharacterIterator($characterIterator);
    }
}
