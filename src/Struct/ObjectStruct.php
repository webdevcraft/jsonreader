<?php

namespace WebDevCraft\JsonReader\Struct;

use WebDevCraft\JsonReader\Exception\InternalErrorException;
use WebDevCraft\JsonReader\Struct\AbstractStruct;
use WebDevCraft\JsonReader\JsonReader;

class ObjectStruct extends AbstractStruct
{
    /**
     * @throws InternalErrorException
     * @throws \WebDevCraft\JsonReader\Exception\MalformedJsonException
     */
    public function read()
    {
        switch ($this->getState()) {
            case JsonReader::STATE_OBJECT_START:
                $this->reader->readObjectKeyOrEnd();
                break;
            case JsonReader::STATE_OBJECT_KEY:
                $this->reader->readValue();
                break;
            case JsonReader::STATE_VALUE:
                $this->reader->readObjectNextKeyOrEnd();
                break;
            case JsonReader::STATE_OBJECT_END:
                $this->reader->readAfterEnd();
                break;
            default:
                throw new InternalErrorException('Unexpected state');
        }
    }
}
