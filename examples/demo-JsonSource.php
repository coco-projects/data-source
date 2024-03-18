<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\source\JsonSource;
    use Coco\dataSource\utils\FieldMap;
    use Coco\dataSource\utils\MapStatus;

    $jsonFile = 'data/test.json';

    $source = JsonSource::getIns(file_get_contents($jsonFile), function($data) {

        return $data['data1']['data2'];
    });
/*
    $source->coverFieldFormAssoc('gender', [
        "0" => "未知",
        "1" => "男",
        "2" => "女",
    ]);*/

    $source->coverFieldFormMap('gender', [
        "0" => ["label" => "未知",],
        "1" => ["label" => "男",],
        "2" => ["label" => "女",],
    ]);

    $source->page(1)->limit(5)->field('id,name,age,gender,time')->orderDate('time', 'asc');

//    $source->getFilter()->whereEq('id', 7);
//    $source->getFilter()->whereEq('age', 31, 'or');

//    $source->getFilter()->whereLike('name', '%八');
//    $source->getFilter()->whereLike('name', '%三','or');

//    $source->getFilter()->whereIn('id', [2,6]);
//    $source->getFilter()->whereIn('id', [4,6], 'or');

//    $source->getFilter()->whereBetween('id', [2,4]);
//    $source->getFilter()->whereBetween('id', [7,9], 'or');

//    $source->getFilter()->whereNotBetween('id', [2,4]);

//    $source->getFilter()->whereTimeGt('time', '2024-01-6');
//    $source->getFilter()->whereNotBetween('id', [4,6], 'or');

//    $source->getFilter()->whereNotNull('city');
//    $source->getFilter()->whereNotNull('city', 'or');

//
    $res = $source->fetchList()->all();
//    $res = $source->fetchColumn('gender');
//    $res = $source->fetchValue('gender');
//    $res = $source->fetchItem();
//    $res = $source->avg('age');
//    $res = $source->sum('age');


    print_r($res);


