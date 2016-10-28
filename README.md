# JsonReader
JSON streaming reader (memory safety parser) with chunking option

## Purpose and advantages
1. **Memory safety** read of huge JSON files: solve "out of RAM" problem. Successfully Tested with 1.5Gb JSON files
1. Possibility to split large JSON file to smaller **JSON chunks**
1. **Custom traverse** through JSON tree
1. Read **any JSON source**: file, string, resource, character iterator
1. Installation with **Composer**, no need any extra PHP extensions

## Requirements
PHP 5.5, 5.6, 7.0 or above

## Installation
With Composer:
```sh
composer require webdevcraft/jsonreader
```

## Usage
JSON Reader iterates through JSON structure elements

Each ```$reader->read()``` iterates to next element until it does not return ```false``` when finished

So common usage is:
```php
while ($reader->read()) {
    // next element iteration
}
```

Reader provides information on each element:
1. Depth in JSON tree starting from 1: ```$reader->getDepth()```
1. Type of token (or reader state): ```$reader->getState()```. State could be one of:
    1. Object start
    1. Object key
    1. Object end
    1. Array start
    1. Array end
    1. Value
1. Value of element by ```$reader->getValue()```
    1. For ```$reader->getState() === JsonReaderInterface::STATE_VALUE``` the type of value is casted to PHP type: _string, int, float, boolean, null_
    1. For ```STATE_OBJECT_KEY``` state it's always _string_
    1. For other states value is always _null_