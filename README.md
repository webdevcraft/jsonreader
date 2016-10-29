# JsonReader

[![Build Status](https://travis-ci.org/webdevcraft/jsonreader.svg?branch=master)](https://travis-ci.org/webdevcraft/jsonreader)

JSON streaming reader (memory safety parser) with chunking option

## Purpose and advantages
1. **Memory safety** read of huge JSON files: solve "out of RAM" problem. Successfully tested with 1.5Gb JSON files
1. Possibility to split large JSON file to smaller **JSON chunks**
1. **Custom traverse** through JSON tree
1. Read **any JSON source**: file, string, resource, character iterator
1. Installation with **Composer**, no need any extra PHP extensions

## Requirements
PHP 5.6, 7.0

## Installation
With Composer:
```sh
composer require webdevcraft/jsonreader
```

## Usage

### Basic
JSON Reader iterates through JSON structure elements

Each ```$reader->read()``` iterates to next element until it returns ```false``` when finished

So common usage is:
```php
while ($reader->read()) {
    // next element iteration
}
```

Reader provides information on each element:

1. **DEPTH** in JSON tree starting from 1: ```$reader->getDepth()```

1. **STATE** (type of element): ```$reader->getState()```. There is syntax sugar issers exist for each of states. State could be one of:

    * **Object Start**: _JsonReaderInterface::STATE_OBJECT_START_ (or ```$reder->isObjectStartState()```)
    
    * **Object Key**: _JsonReaderInterface::STATE_OBJECT_KEY_ (or ```$reder->isObjectKeyState()```)
    
    * **Object End** _JsonReaderInterface::STATE_OBJECT_END_ (or ```$reder->isObjectEndState()```)
    
    * **Array Start** _JsonReaderInterface::STATE_ARRAY_START_ (or ```$reder->isArrayStartState()```)
    
    * **Array End** _JsonReaderInterface::STATE_ARRAY_END_ (or ```$reder->isArrayEndState()```)
    
    * **Value** _JsonReaderInterface::STATE_VALUE_ (or ```$reder->isValueState()```)
    
1. **VALUE** of element by ```$reader->getValue()```. Types:

    1. For ```$reader->getState() === JsonReaderInterface::STATE_VALUE``` the type of value is automatically casted to PHP type: _string, int, float, boolean, null_
    
    1. For ```JsonReaderInterface::STATE_OBJECT_KEY``` state is always _string_
    
    1. For other states value is always _null_
    
**Example** of elements sequence for [test.json](https://github.com/webdevcraft/jsonreader/blob/master/tests/data/test.json) is presented in [JsonReaderTest.php::testReadTokens()](https://github.com/webdevcraft/jsonreader/blob/master/tests/JsonReaderTest.php)

### Chunks
In case if your code is already dependent on JSON string denormanization you could use JsonReader library for chunking of huge file onto smaller JSON strings

Just start buffering chunk on element position you wish by ```$reader->startWriteChunk()``` and get chunk by ```$reader->finishWriteChunk()```. That returnes JSON chunk string and flushes buffer.

As chunk is buffered in memory make sure your JSON schema guarantees acceptable size of chunk

That's nice practice to wrap chunks retrieve to iterator

**Example** of [test.json](https://github.com/webdevcraft/jsonreader/blob/master/tests/data/test.json) chunking is presented in [JsonReaderTest.php::testChunks()](https://github.com/webdevcraft/jsonreader/blob/master/tests/JsonReaderTest.php)

### Factory
To create reader you have to use __JsonReaderFactory__

Example of JsonReader creating:

```php
$factory = new JsonReaderFactory();
$reader = $factory->createByFilePath('/tmp/test.json');
```

Factory supports any sources by such methods:

1. __createByFilePath__ by file path in local filesystem
1. __createByString__ by JSON string passing
1. __createByResource__ by opened resource link, for example with __fopen()__
1. __createByCharacterTraversable__ - if options above do not suite your needs feel free to write your custom characters iterator using any source you have to deal with. For example you could iterate __PSR-7 Message\StreamInterface::read($acceptableSize)__ and iterate characters inside each of __read()__

[Example of characters iterators](https://github.com/webdevcraft/jsonreader/tree/master/src/CharacterIterator)
 
