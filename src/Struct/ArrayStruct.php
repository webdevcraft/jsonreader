<?php

namespace WebDevCraft\JsonReader\Struct;

use WebDevCraft\JsonReader\Exception\InternalErrorException;
use WebDevCraft\JsonReader\Struct\AbstractStruct;
use WebDevCraft\JsonReader\JsonReader;

class ArrayStruct extends AbstractStruct
{
    /**
     * @throws InternalErrorException
     * @throws \WebDevCraft\JsonReader\Exception\MalformedJsonException
     */
    public function read()
    {
        switch($this->getState()) {
            case JsonReader::STATE_ARRAY_START:
                $this->reader->readValue();
                break;
            case JsonReader::STATE_VALUE:
                $this->reader->readArrayNextValueOrEnd();
                break;
            case JsonReader::STATE_ARRAY_END:
                $this->reader->readAfterEnd();
                break;
            default:
                throw new InternalErrorException('Unexpected state');
        }
    }
}
