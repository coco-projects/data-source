<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\source\GeneratorSource;

    $generator = function() {
        yield range(10, 20);
    };

    $source = GeneratorSource::getIns($generator());

    $collection = $source->getIterator();

    echo '-----------------';
    echo PHP_EOL;

    foreach ($collection->reverse() as $record)
    {
        print_r($record);
        echo PHP_EOL;
    }