<?php

    use Coco\dataSource\utils\FieldMap;

    require '../vendor/autoload.php';

    $test = FieldMap::getIns('test');

    $test->getStatusById(0)->label('未知');
    $test->getStatusById(1)->label('男');
    $test->getStatusById(2)->label('女');

    print_r($test->getMap());
