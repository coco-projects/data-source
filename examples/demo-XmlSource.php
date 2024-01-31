<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\source\XmlSource;


    $xmlFilePath = 'data/test.xml';

    $source = XmlSource::getIns(file_get_contents($xmlFilePath), function($data) {

        return $data['data1']['data2']['item'];
    });

    $source->page(2)->limit(5)->field('id,name,time')->orderDate('time', 'desc');

//    $source->getFilter()->whereEq('id', 7);
//    $source->getFilter()->whereEq('age', 31, 'or');

//    $source->getFilter()->whereLike('name', '%八');
//    $source->getFilter()->whereLike('name', '%三','or');

//    $source->getFilter()->whereIn('id', [2,6]);
//    $source->getFilter()->whereIn('id', [4,6], 'or');

//    $source->getFilter()->whereBetween('id', [2,4]);
//    $source->getFilter()->whereBetween('id', [7,9], 'or');

//    $source->getFilter()->whereNotBetween('id', [2,4]);

    $source->getFilter()->whereTimeGt('time', '2024-01-6');
//    $source->getFilter()->whereNotBetween('id', [4,6], 'or');

//    $source->getFilter()->whereNotNull('city');
//    $source->getFilter()->whereNotNull('city', 'or');

//
    $res = $source->fetchList()->all();
//    $res = $source->fetchColumn('name');
//    $res = $source->fetchValue('name');

    print_r($res);
