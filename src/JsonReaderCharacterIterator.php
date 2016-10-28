<?php

namespace WebDevCraft\JsonReader;

use WebDevCraft\JsonReader\Exception\InternalErrorException;
use WebDevCraft\JsonReader\Exception\MalformedJsonException;

/**
 * Class Wrapper for character iterator providing Reader with convenience:
 * * previous character
 * * iterate to previous character position
 * * buffer json chunk string
 * * iterate to non-white character
 * Every getNext() call expects next characted exists guaranteed. JsonMalformedException thrown otherwise (part of reader logic)
 */
class JsonReaderCharacterIterator
{
    /**
     * @var string
     */
    private $current;

    /**
     * @var string
     */
    private $previous;

    /**
     * @var bool
     */
    private $currentIsNextMode;

    /**
     * @var \Iterator
     */
    private $characterIterator;

    /**
     * @var string
     */
    private $buffer;

    /**
     * @var bool
     */
    private $isWriteBuffer = false;

    /**
     * @var array
     */
    private static $whiteCharacterCollection = [' ', "\t", "\n", "\r"];

    /**
     * @param \Iterator $characterIterator
     */
    public function __construct(\Iterator $characterIterator)
    {
        $this->characterIterator = $characterIterator;
        $this->characterIterator->rewind();
    }

    /**
     * Get next character and iterates next()
     *
     * @return string
     * @throws \WebDevCraft\JsonReader\Exception\MalformedJsonException
     */
    public function getNext()
    {
        if ($this->currentIsNextMode) {
            $this->currentIsNextMode = false;
            return $this->current;
        }
        if (!$this->characterIterator->valid()) {
            throw new MalformedJsonException('Unexpected end of JSON');
        }
        $this->previous = $this->current;
        $this->current = $this->characterIterator->current();
        if ($this->isWriteBuffer) {
            $this->buffer .= $this->current;
        }
        $this->characterIterator->next();
        return $this->current;
    }

    /**
     * @return string
     */
    public function getCurrent()
    {
        if ($this->currentIsNextMode) {
            return $this->previous;
        }
        return $this->current;
    }

    /**
     * Get previous character. Does NOT iterates to previous
     *
     * @return string
     * @throws \WebDevCraft\JsonReader\Exception\InternalErrorException
     */
    public function getPrevious()
    {
        if ($this->currentIsNextMode) {
            throw new InternalErrorException('Not available. Already iterated to prev(). Buffer size of backward iteration is 1');
        }
        return $this->previous;
    }

    /**
     * Emulates iteration to previous symbol
     * So getNext() will return current instead of next
     *
     * @return void
     * @throws \WebDevCraft\JsonReader\Exception\InternalErrorException
     */
    public function prev()
    {
        if ($this->currentIsNextMode) {
            throw new InternalErrorException('You can iterate to previous max by 1 position');
        }
        $this->currentIsNextMode = true;
    }

    /**
     * Start write string to buffer from current character and on futher each getNext()
     * Until endBuffer()
     *
     * @return void
     */
    public function startBuffer()
    {
        $this->isWriteBuffer = true;
        $this->buffer = $this->current;
    }

    /**
     * Stop write buffer
     * Return buffer
     * Clear buffer (for performance reason)
     *
     * @return string
     */
    public function endBuffer()
    {
        $this->isWriteBuffer = false;
        $buffer = $this->buffer;
        $this->buffer = '';
        return $buffer;
    }

    /**
     * @return string
     * @throws \WebDevCraft\JsonReader\Exception\MalformedJsonException
     */
    public function getNextNonWhite()
    {
        while (true) {
            $character = $this->getNext();
            if (!in_array($character, self::$whiteCharacterCollection)) {
                return $character;
            }
        }
    }
}
