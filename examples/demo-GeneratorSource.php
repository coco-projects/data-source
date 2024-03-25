<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\source\GeneratorSource;

    $generator = function() {
        yield range(10, 20);
    };

    $source = GeneratorSource::getIns($generator());

