<?php

namespace WebDevCraft\JsonReader;

use PHPUnit\Framework\TestCase;
use WebDevCraft\JsonReader\Exception\MalformedJsonException;

class JsonReaderTest extends TestCase
{
    public function testReadTokens()
    {
        $reader = $this->createReaderByFileName('test.json');
        $expectedTokens = [
            /** [depth, state, value] */
            [1, JsonReaderInterface::STATE_OBJECT_START,    null],
            [1, JsonReaderInterface::STATE_OBJECT_KEY,      'a'],
            [1, JsonReaderInterface::STATE_VALUE,           false],
            [1, JsonReaderInterface::STATE_OBJECT_KEY,      'b'],
            [1, JsonReaderInterface::STATE_VALUE,           2],
            [1, JsonReaderInterface::STATE_OBJECT_KEY,      'records'],
            [2, JsonReaderInterface::STATE_ARRAY_START,     null],

            [3, JsonReaderInterface::STATE_OBJECT_START,    null],
            [3, JsonReaderInterface::STATE_OBJECT_KEY,      'id'],
            [3, JsonReaderInterface::STATE_VALUE,           3],
            [3, JsonReaderInterface::STATE_OBJECT_KEY,      'name'],
            [3, JsonReaderInterface::STATE_VALUE,           'some'],
            [3, JsonReaderInterface::STATE_OBJECT_KEY,      'active'],
            [3, JsonReaderInterface::STATE_VALUE,           true],
            [3, JsonReaderInterface::STATE_OBJECT_END,      null],

            [3, JsonReaderInterface::STATE_OBJECT_START,    null],
            [3, JsonReaderInterface::STATE_OBJECT_KEY,      'id'],
            [3, JsonReaderInterface::STATE_VALUE,           5],
            [3, JsonReaderInterface::STATE_OBJECT_KEY,      'name'],
            [3, JsonReaderInterface::STATE_VALUE,           'another'],
            [3, JsonReaderInterface::STATE_OBJECT_KEY,      'active'],
            [3, JsonReaderInterface::STATE_VALUE,           false],
            [3, JsonReaderInterface::STATE_OBJECT_END,      null],

            [2, JsonReaderInterface::STATE_ARRAY_END,       null],
            [1, JsonReaderInterface::STATE_OBJECT_END,      null],
        ];
        $i = 0;
        while ($reader->read()) {
            $this->assertEquals($expectedTokens[$i][0], $reader->getDepth());
            $this->assertSame($expectedTokens[$i][1], $reader->getState());
            $this->assertSame($expectedTokens[$i][2], $reader->getValue());
            ++$i;
        }
    }

    public function testChunks()
    {
        $reader = $this->createReaderByFileName('test.json');
        $expectedChunks = [
            '{"id": 3, "name": "some", "active": true}',
            '{"id": 5, "name": "another", "active": false}',
        ];

        $i = 0;
        while ($reader->read()) {
            if ($reader->getDepth() === 3) {
                if ($reader->getState() === JsonReaderInterface::STATE_OBJECT_START) {
                    $reader->startWriteChunk();
                } elseif ($reader->getState() === JsonReaderInterface::STATE_OBJECT_END) {
                    $chunk = $reader->finishWriteChunk();
                    $this->assertSame($expectedChunks[$i], $chunk);
                    ++$i;
                }
            }
        }
    }

    public function testMalformedPrivitiveValueRoot()
    {
        $reader = $this->createReaderByFileName('malformed_primitive_value_root.json');
        $this->expectException(MalformedJsonException::class);
        while ($reader->read()) {
        }
    }

    public function testMalformedSyntax()
    {
        $reader = $this->createReaderByFileName('malformed_excess_comma.json');
        $this->expectException(MalformedJsonException::class);
        while ($reader->read()) {
        }
    }

    public function testUnknownValueType()
    {
        $reader = $this->createReaderByFileName('malformed_unknown_value_type.json');
        $this->expectException(MalformedJsonException::class);
        while ($reader->read()) {
        }
    }

    /**
     * @param string $fileName
     *
     * @return JsonReader
     */
    private function createReaderByFileName($fileName)
    {
        $factory = new JsonReaderFactory();
        $filePath = __DIR__.'/data/'.$fileName;

        return $factory->createByFilePath($filePath);
    }
}
