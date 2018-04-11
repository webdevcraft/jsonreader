<?php

namespace WebDevCraft\JsonReader;

use WebDevCraft\JsonReader\Struct\ObjectStruct;
use WebDevCraft\JsonReader\Struct\ArrayStruct;
use WebDevCraft\JsonReader\Exception\MalformedJsonException;
use WebDevCraft\JsonReader\JsonReaderCharacterIterator;

class JsonReader implements ChunkJsonReaderInterface
{
    /**
     * @var JsonReaderCharacterIterator
     */
    private $characterIterator;

    /**
     * @var \SplStack[AbstractStruct]
     */
    private $structStack;

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $firstRead = true;

    /**
     * @param JsonReaderCharacterIterator $characterIterarator
     */
    public function __construct(JsonReaderCharacterIterator $characterIterarator)
    {
        $this->characterIterator = $characterIterarator;
        $this->structStack = new \SplStack();
    }

    /**
     * @inheritdoc
     */
    public function read()
    {
        if ($this->firstRead) {
            $this->readValue();
            if (!$this->structStack->count()) {
                throw new MalformedJsonException('Root level should be object or array struct, primitive value type not allowed');
            }
            $this->firstRead = false;

            return true;
        }

        if (!$this->structStack->count()) {
            return false;
        }
        $this->structStack->top()->read();

        return $this->structStack->count() > 0;
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        if (!$this->structStack->count()) {
            return self::STATE_FINISHED_OR_NOT_STARTED;
        }
        return $this->structStack->top()->getState();
    }

    /**
     * @inheritdoc
     */
    public function startWriteChunk()
    {
        $this->characterIterator->startBuffer();
    }

    /**
     * @inheritdoc
     */
    public function finishWriteChunk()
    {
        return $this->characterIterator->endBuffer();
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function getDepth()
    {
        return $this->structStack->count();
    }

    /**
     * @inheritDoc
     */
    public function isObjectStartState()
    {
        return $this->getState() === self::STATE_OBJECT_START;
    }

    /**
     * @inheritDoc
     */
    public function isObjectKeyState()
    {
        return $this->getState() === self::STATE_OBJECT_KEY;
    }

    /**
     * @inheritDoc
     */
    public function isObjectEndState()
    {
        return $this->getState() === self::STATE_OBJECT_END;
    }

    /**
     * @inheritDoc
     */
    public function isArrayStartState()
    {
        return $this->getState() === self::STATE_ARRAY_START;
    }

    /**
     * @inheritDoc
     */
    public function isArrayEndState()
    {
        return $this->getState() === self::STATE_ARRAY_END;
    }

    /**
     * @inheritDoc
     */
    public function isValueState()
    {
        return $this->getState() === self::STATE_VALUE;
    }

    /**
     * @throws MalformedJsonException
     */
    public function readValue()
    {
        $character = $this->characterIterator->getNextNonWhite();
        if ($this->structStack->count()) {
            /** For root element stack is empty yet */
            $this->structStack->top()->setState(self::STATE_VALUE);
        }

        if ($character == '{') {
            $this->structStack->push(
                new ObjectStruct($this)
            );
            $this->structStack->top()->setState(self::STATE_OBJECT_START);
            $this->value = null;

            return;
        }

        if ($character == '[') {
            $this->structStack->push(
                new ArrayStruct($this)
            );
            $this->structStack->top()->setState(self::STATE_ARRAY_START);
            $this->value = null;

            return;
        }

        if ($character == '"') {
            $this->readStringValue();

            return;
        }

        if ($character == 't') {
            $this->readTrueValue();

            return;
        }

        if ($character == 'f') {
            $this->readFalseValue();

            return;
        }

        if ($character == 'n') {
            $this->readNullValue();

            return;
        }

        if ($character == '-' || ($character >= '0' && $character <= '9')) {
            $this->readNumberValue();

            return;
        }

        throw new MalformedJsonException('Unexpected value type');
    }

    /**
     * @param bool|false $isNext
     *
*@throws MalformedJsonException
     */
    public function readObjectKeyOrEnd($isNext = false)
    {
        $character = $this->characterIterator->getNextNonWhite();
        if ($character == '}') {
            $this->structStack->top()->setState(self::STATE_OBJECT_END);
            $this->value = null;
            return;
        }
        if ($isNext) {
            if ($character !== ',') {
                throw new MalformedJsonException('"," expected');
            }
            $character = $this->characterIterator->getNextNonWhite();
        }
        if ($character !== '"') {
            throw new MalformedJsonException('" expected as object key definition start');
        }
        $this->readObjectKey();
    }

    /**
     * @throws MalformedJsonException
     */
    public function readObjectNextKeyOrEnd()
    {
        $this->readObjectKeyOrEnd(true);
    }

    /**
     * @param bool|false $isNext
     *
*@throws \WebDevCraft\JsonReader\Exception\MalformedJsonException
     */
    public function readArrayValueOrEnd($isNext = false)
    {
        $character = $this->characterIterator->getNextNonWhite();
        if ($character == ']') {
            $this->structStack->top()->setState(self::STATE_ARRAY_END);
            $this->value = null;
            return;
        }
        if ($isNext) {
            if ($character != ',') {
                throw new MalformedJsonException('Next array value expected');
            }
        } else {
            $this->characterIterator->prev();
        }
        $this->readValue();
    }

    /**
     * @throws MalformedJsonException
     */
    public function readArrayNextValueOrEnd()
    {
        $this->readArrayValueOrEnd(true);
    }

    /**
     * @return bool
     */
    public function readAfterEnd()
    {
        $this->structStack->pop();
        if (!$this->structStack->count()) {
            return false;
        }
        $this->structStack->top()->read();
        return true;
    }

    /**
     * @throws \WebDevCraft\JsonReader\Exception\MalformedJsonException
     */
    private function readStringValue()
    {
        $string = '';
        do {
            $character = $this->characterIterator->getNext();
            $string .= $character;
        } while (
            !(
                $character == '"' &&
                $this->characterIterator->getPrevious() != '\\'
            )
        );
        if (strlen($string) > 0) {
            $string = substr($string, 0, (strlen($string) - 1));
            str_replace('\\n', "\n", $string);
            str_replace('\\"', '"', $string);
            // TODO convert other special characters

        }
        $this->value = $string;
    }

    /**
     * @throws MalformedJsonException
     */
    private function readNumberValue()
    {
        $isFloat = false;
        $numberString = '';
        $character = $this->characterIterator->getCurrent();
        $numberString .= $character;
        if ($character == '-') {
            $character = $this->characterIterator->getNext();
            $numberString .= $character;
        }
        if ($character >= '1' && $character <= '9') {
            while (true) {
                $character = $this->characterIterator->getNext();
                $numberString .= $character;
                if (!($character >= '0' && $character <= '9')) {
                    break;
                }
            }
        } elseif ($character == '0') {
            $character = $this->characterIterator->getNext();
            $numberString .= $character;
        } else {
            throw new MalformedJsonException('Digit expected');
        }

        if ($character == '.') {
            $isFloat = true;
            $character = $this->characterIterator->getNext();
            $numberString .= $character;
            if (!($character >= '0' && $character <= '9')) {
                throw new MalformedJsonException('Digit after . expected for float');
            }
            while (true) {
                $character = $this->characterIterator->getNext();
                $numberString .= $character;
                if (!($character >= '0' && $character <= '9')) {
                    break;
                }
            }
        }
        if (in_array($character, ['e', 'E'])) {
            $isFloat = true;
            if (in_array($character, ['+', '-'])) {
                $character = $this->characterIterator->getNext();
                $numberString .= $character;
            }
            if (!($character >= '0' && $character <= '9')) {
                throw new MalformedJsonException('Digit expected for after E for float');
            }
            while (true) {
                $character = $this->characterIterator->getNext();
                $numberString .= $character;
                if (!($character >= '0' && $character <= '9')) {
                    break;
                }
            }
        }
        $numberString = substr($numberString, 0, (strlen($numberString) - 1));
        if ($isFloat) {
            $this->value = (float) $numberString;
        } else {
            $this->value = (int) $numberString;
        }
        $this->characterIterator->prev();
    }

    /**
     * @throws MalformedJsonException
     */
    private function readTrueValue()
    {
        $this->characterIterator->prev();
        $this->readNeedle('true');
        $this->value = true;
    }

    /**
     * @throws MalformedJsonException
     */
    private function readFalseValue()
    {
        $this->characterIterator->prev();
        $this->readNeedle('false');
        $this->value = false;
    }

    /**
     * @throws MalformedJsonException
     */
    private function readNullValue()
    {
        $this->characterIterator->prev();
        $this->readNeedle('null');
        $this->value = null;
    }

    /**
     * @param string $needle
     *
     * @throws MalformedJsonException
     */
    private function readNeedle($needle)
    {
        $haystack = '';
        for ($i = 0; $i < strlen($needle); ++$i) {
            $character = $this->characterIterator->getNext();
            $haystack .= $character;
        }
        if ($needle !== $haystack) {
            throw new MalformedJsonException('"'.$needle.'" expected');
        }
    }

    /**
     * @throws MalformedJsonException
     */
    private function readObjectKey()
    {
        $objectKey = '';
        while (true) {
            $character = $this->characterIterator->getNext();
            if ($character == '"' && $this->characterIterator->getPrevious() != '\\') {
                break;
            }
            $objectKey .= $character;
        }
        $character = $this->characterIterator->getNextNonWhite();
        if ($character != ':') {
            throw new MalformedJsonException('Expected : after object key');
        }
        $this->value = $objectKey;
        $this->structStack->top()->setState(self::STATE_OBJECT_KEY);
    }
}
