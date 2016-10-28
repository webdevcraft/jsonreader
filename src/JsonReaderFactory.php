<?php

namespace WebDevCraft\JsonReader;

use WebDevCraft\JsonReader\CharacterIterator\FileStreamCharacterIterator;
use WebDevCraft\JsonReader\CharacterIterator\ResourceIterator;
use WebDevCraft\JsonReader\CharacterIterator\StringCharacterIterator;
use WebDevCraft\JsonReader\JsonReader;
use WebDevCraft\JsonReader\JsonReaderCharacterIterator;
use WebDevCraft\JsonReader\JsonReaderFactoryInterface;

class JsonReaderFactory implements JsonReaderFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createByFilePath($filePath)
    {
        $characterIterator = new FileStreamCharacterIterator($filePath);

        return $this->createByCharacterIterator($characterIterator);
    }

    /**
     * @inheritdoc
     */
    public function createByString($string)
    {
        $characterIterator = new StringCharacterIterator($string);

        return $this->createByCharacterIterator($characterIterator);
    }

    /**
     * @inheritDoc
     */
    public function createByResource($resource)
    {
        return $this->createByCharacterTraversable(new ResourceIterator($resource));
    }

    /**
     * @inheritDoc
     */
    public function createByCharacterTraversable(\Traversable $traversable)
    {
        $iterator = ($traversable instanceof \Iterator) ? ($traversable) : (new \IteratorIterator($traversable));

        return $this->createByCharacterIterator($iterator);
    }

    /**
     * @param \Iterator $characterIterator
     *
     * @return ChunkJsonReaderInterface
     */
    private function createByCharacterIterator(\Iterator $characterIterator)
    {
        $readerCharacterIterator = new JsonReaderCharacterIterator($characterIterator);

        return new JsonReader($readerCharacterIterator);
    }
}
