<?php

namespace WebDevCraft\JsonReader\Exception;

use Exception;

class InternalErrorException extends \LogicException implements JsonReaderException
{
    /**
     * @inheritDoc
     */
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $message = 'Internal error. Please report bug. ' . $message;
        parent::__construct($message, $code, $previous);
    }
}
