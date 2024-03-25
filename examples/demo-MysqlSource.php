<?php

    use Coco\dataSource\source\MysqlSource;
    use Coco\dataSource\utils\FieldMap;
    use Coco\dataSource\utils\MapStatus;
    use Coco\dataSource\utils\MysqlHandler;

    require '../vendor/autoload.php';

    $config = [
        'default' => 'default',

        'connections' => [
            'default' => [
                // 数据库类型
                'type'              => 'mysql',
                // 服务器地址
                'hostname'          => '127.0.0.1',
                // 数据库名
                'database'          => 'coco_app_test',
                // 数据库用户名
                'username'          => 'root',
                // 数据库密码
                'password'          => 'root',
                // 数据库连接端口
                'hostport'          => '',
                // 数据库连接参数
                'params'            => [],
                // 数据库编码默认采用utf8
                'charset'           => 'utf8',
                // 数据库表前缀
                'prefix'            => '',
                // 开启字段缓存
                'fields_cache'      => true,
                // 字段缓存路径
                'schema_cache_path' => 'data',
            ],
        ],
    ];

    $handler = MysqlHandler::getIns($config);

    $source = MysqlSource::getIns($handler->getDbManager(), 'default', 'people');

    $source->setCacheConfig()->enableCache(false);

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


    $source->getDbManager()->listen(function($sql, $runtime, $master) {
        echo $sql;
        echo PHP_EOL;
    });


//    $res = $source->query('select * from people');
//    $res = $source->execute('select * from people');
//    $res = $source->getFields();

    $filter = new \Coco\dataSource\filter\MysqlFilter();

    $filter->join('gender', 'people.gender = gender.id');
    $filter->page(1)->limit(15)->field('people.id as pid,people.name pname,gender,people.create_time')->orderDate('people.id');

//    $filter->whereEq('people.id', 7);


//    $filter->whereEq('id', 7);
//    $filter->whereEq('age', 62, 'or');

//    $filter->whereLike('name', '%八');
//    $filter->whereLike('name', '%三','or');

//    $filter->whereIn('id', [2,6]);
//    $filter->whereIn('id', [4,6], 'or');

//    $filter->whereBetween('id', [2,4]);
//    $filter->whereBetween('id', [7,9], 'or');

//    $filter->whereNotBetween('id', [2,4]);

    $filter->whereTimeLt('people.create_time', '2025-08-19 14:38:02');
//    $filter->whereNotBetween('id', [4,6], 'or');

//    $filter->whereNotNull('hobby');
//    $filter->whereNull('hobby');

//    $filter->whereNotEmpty('hobby');
//    $filter->whereEmpty('hobby');

//    $filter->whereNotNull('hobby', 'or');


    $res = $source->fetchList($filter)->all();
//    $res = $source->fetchColumn('gender',$filter);
//    $res = $source->fetchValue('gender',$filter);
//    $res = $source->fetchItem($filter);

    /*
    $res = $source->toMap('id', 'name', function(MapStatus $mapStatus, $item) {
        $mapStatus->label($item['name'] . '-----');
        $mapStatus->disabled(true);
    }, function(FieldMap $fieldMap) {
        $fieldMap->getStatusById(-1)->label('请选择');
    });*/

    print_r($res);
