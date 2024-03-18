<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\source\IterableSource;
    use loophp\collection\Collection;

    $collection = Collection::fromCallable(function() {
        return $array = [
            [
                "id"     => 1,
                "name"   => "张三",
                "age"    => 27,
                "time"   => "2024-01-11",
                "region" => "北京",
            ],
            [
                "id"     => 2,
                "name"   => "李四",
                "age"    => 35,
                "time"   => "2024-01-11",
                "region" => "上海",
            ],
            [
                "id"     => 3,
                "name"   => "王五",
                "age"    => 42,
                "time"   => "2024-7-11",
                "region" => "广州",
            ],
            [
                "id"     => 4,
                "name"   => "赵六",
                "age"    => 18,
                "time"   => "2024-2-11",
                "region" => "成都",
            ],
            [
                "id"     => 5,
                "name"   => "陈七",
                "age"    => 31,
                "time"   => "2024-01-2",
                "region" => "深圳",
            ],
            [
                "id"     => 6,
                "name"   => "刘八",
                "age"    => 26,
                "time"   => "2024-01-6",
                "region" => "杭州",
            ],
            [
                "id"     => 7,
                "name"   => "黄九",
                "age"    => 39,
                "time"   => "2024-9-16",
                "region" => "重庆",
            ],
            [
                "id"     => 8,
                "name"   => "周十",
                "age"    => 23,
                "time"   => "2024-01-2",
                "region" => "天津",
            ],
            [
                "id"     => 9,
                "name"   => "吴十一",
                "age"    => 29,
                "time"   => "2024-01-4",
                "region" => "南京",
            ],
            [
                "id"     => 10,
                "name"   => "郑十二",
                "age"    => 37,
                "time"   => "2024-01-25",
                "region" => "武汉",
            ],
        ];
    });

    $source = IterableSource::getIns($collection);

    $source->page(1)->limit(15)->field('id,name,time')->orderDate('time', 'asc');

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
