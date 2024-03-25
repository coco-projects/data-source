<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\filter\CollectionFilter;
    use Coco\dataSource\source\JsonSource;

    $file   = 'data/test.json';
    $source = JsonSource::getIns(file_get_contents($file), function($data) {

        return $data['data1']['data2'];
    });

    /*
        $source->coverFieldFormMap('gender', [
            "1" => ["label" => "男",],
            "2" => ["label" => "女",],
            "3" => ["label" => "未知",],
        ]);
    */

    $source->coverFieldFormAssoc('gender', [
        "1" => "男",
        "2" => "女",
        "3" => "未知",
    ]);

    $source->coverFieldFormAssoc('hobby', [
        "1" => "打球",
        "2" => "游泳",
        "3" => "跑步",
    ]);


    $filter = new CollectionFilter();

    $filter->page(1)->limit(13)->field('id,gender,hobby,age,name,create_time')->orderDesc('create_time');

//    $filter->whereEq('id', 7);
//    $filter->whereEq('age', 62, 'or');

//    $filter->whereLike('name', '%八');
//    $filter->whereLike('name', '%三','or');

//    $filter->whereIn('id', [2,6]);
//    $filter->whereIn('id', [4,6], 'or');

//    $filter->whereBetween('id', [2,4]);
//    $filter->whereBetween('id', [7,9], 'or');

//    $filter->whereNotBetween('id', [2,4]);

//    $filter->whereTimeLt('create_time', '2025-08-19 14:38:02');
//    $filter->whereNotBetween('id', [4,6], 'or');

//    $filter->whereNotNull('hobby');
//    $filter->whereNull('hobby');

//    $filter->whereNotEmpty('hobby');
//    $filter->whereEmpty('hobby');

//    $filter->whereNotNull('hobby', 'or');

    $filter->raw('map', function($value) {
        $value['name'] = "<" . $value['name'] . ">";
        $value['create_time'] = date('Y-m-d H:i:s',$value['create_time']);

        return $value;
    });

//
    $res = $source->fetchList($filter)->all();
//    $res = $source->fetchColumn('name', $filter);
//    $res = $source->fetchValue('name', $filter);
//    $res = $source->sum('age', $filter);
//    $res = $source->avg('age', $filter);
//    $res = $source->min('age', $filter);
//    $res = $source->max('age', $filter);
//    $res = $source->totalPages($filter);

    print_r($res);
