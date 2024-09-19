<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\filter\CollectionFilter;
    use Coco\dataSource\source\StringForLineSource;
    use Coco\dataSource\utils\ToTable;

    $string = '
1|张三|23|1|3|1755685480|1000|1|0|1755585480|0
2|李四|4|1,2|1|1755685481|1000|1|1|1755585481|1755585485
3|王五|34|2,3|1|1755685482|1000|0|0|1755585482|0
4|赵六|52||1|1755685483|1000|0|1|1755585483|1755585485
5|陈七|62|1|2|1755685484|1000|1|0|1755585484|0
6|刘八|13|3|2|1755685485|1000|0|1|1755585485|1755585485
7|黄九|52||3|1755685486|1000|1|0|1755585486|0
8|周十|41|1|3|1755685487|1000|1|0|1755585487|0
';

    $callback = new ToTable(function($v) {
        return explode('|', $v);
    }, explode(',', 'id,name,age,hobby,gender,join_time,order,status,deleted,create_time,delete_time'));

    $source = StringForLineSource::getIns($string, $callback->setSeek(1));

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
