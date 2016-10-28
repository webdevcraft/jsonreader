<?php

namespace WebDevCraft\JsonReader;

use WebDevCraft\JsonReader\JsonReaderInterface;

interface JsonReaderFactoryInterface
{
    /**
     * @param string $filePath
     *
     * @return ChunkJsonReaderInterface
     */
    public function createByFilePath($filePath);

    /**
     * @param string $string
     *
     * @return ChunkJsonReaderInterface
     */
    public function createByString($string);

    /**
     * @param resource $resource
     *
     * @return ChunkJsonReaderInterface
     */
    public function createByResource($resource);

    /**
     * @param \Traversable $traversable
     *
     * @return ChunkJsonReaderInterface
     */
    public function createByCharacterTraversable(\Traversable $traversable);
}
