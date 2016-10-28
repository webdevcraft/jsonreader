<?php

namespace WebDevCraft\JsonReader;

interface ChunkJsonReaderInterface extends JsonReaderInterface
{
    /**
     * @return void
     */
    public function startWriteChunk();

    /**
     * Returns json chunk string and clears chunk buffer
     *
     * @return string
     */
    public function finishWriteChunk();
}
