<?php

namespace WebDevCraft\JsonReader\CharacterIterator;

class FileStreamCharacterIterator implements \Iterator
{
    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * @var string
     */
    private $currentCharacter;

    /**
     * @var string
     */
    private $fileName;

    /**
     * FileStreamCharacterIterator constructor.
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $this->file = new \SplFileObject($fileName);
    }


    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->currentCharacter;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->currentCharacter = $this->file->fgetc();
    }

    /**
     * @inheritDoc
     */
    public function key()
    {

    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->currentCharacter !== false;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        if ($this->currentCharacter !== null) {
            $this->__construct($this->fileName);
        }
        $this->next();
    }
}
