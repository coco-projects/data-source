<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\source\JsonSource;
    use Coco\dataSource\utils\FieldMap;
    use Coco\dataSource\utils\MapStatus;

    $jsonFile = 'data/test.json';

    $source = JsonSource::getIns(file_get_contents($jsonFile), function($data) {

        return $data['data1']['data2'];
    });

    $genderMap = FieldMap::getIns('gender');
    $genderMap->getStatusById(0)->label('未知');
    $genderMap->getStatusById(1)->label('男');
    $genderMap->getStatusById(2)->label('女');
    $source->addFieldCover('gender', $genderMap);

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

    /*
        $res = $source->toMap('id', 'name', function(MapStatus $mapStatus, $item) {
            $mapStatus->label($item['name'] . '-----');
            $mapStatus->disabled(true);
        }, function(FieldMap $fieldMap) {
            $fieldMap->getStatusById(-1)->label('请选择');
        });
    */

    print_r($res);


