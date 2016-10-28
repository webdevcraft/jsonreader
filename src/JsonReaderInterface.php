<?php

namespace WebDevCraft\JsonReader;

use WebDevCraft\JsonReader\Exception\MalformedJsonException;

interface JsonReaderInterface
{
    const STATE_FINISHED_OR_NOT_STARTED = 0;
    const STATE_OBJECT_START = 1;
    const STATE_OBJECT_KEY = 2;
    const STATE_OBJECT_END = 3;
    const STATE_ARRAY_START = 4;
    const STATE_ARRAY_END = 5;
    const STATE_VALUE = 6;

    /**
     * @return bool
     *
     * @throws MalformedJsonException
     */
    public function read();

    /**
     * @return self::STATE_OBJECT_START|self::STATE_OBJECT_KEY|self::STATE_OBJECT_END|self::STATE_ARRAY_START|self::STATE_ARRAY_END|self::STATE_VALUE|self::STATE_FINISHED_OR_NOT_STARTED
     */
    public function getState();

    /**
     * @return mixed depending on current state
     */
    public function getValue();

    /**
     * @return int
     */
    public function getDepth();

    /**
     * @return bool
     */
    public function isObjectStartState();

    /**
     * @return bool
     */
    public function isObjectKeyState();

    /**
     * @return bool
     */
    public function isObjectEndState();

    /**
     * @return bool
     */
    public function isArrayStartState();

    /**
     * @return bool
     */
    public function isArrayEndState();

    /**
     * @return bool
     */
    public function isValueState();
}
