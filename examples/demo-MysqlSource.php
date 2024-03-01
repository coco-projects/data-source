<?php

    use Coco\dataSource\source\MysqlSource;
    use Coco\dataSource\utils\FieldMap;
    use Coco\dataSource\utils\MapStatus;
    use Coco\dataSource\utils\MysqlHandler;

    require '../vendor/autoload.php';
    require 'common.php';

    $config = [
        'default' => 'default',

        'connections' => [
            'default' => [
                // 数据库类型
                'type'              => 'mysql',
                // 服务器地址
                'hostname'          => '127.0.0.1',
                // 数据库名
                'database'          => 'webuploader',
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
                'prefix'            => 'think_',
                // 开启字段缓存
                'fields_cache'      => true,
                // 字段缓存路径
                'schema_cache_path' => 'data',
            ],
        ],
    ];

    $handler = MysqlHandler::getIns($config);

    $handler->getDbManager()->listen(function($sql, $runtime, $master) {
        echo $sql;
        echo PHP_EOL;
    });

    $source = MysqlSource::getIns($handler->getDbManager(), 'default', 'people');
    $source->setCacheConfig()->enableCache(!false);

    $genderMap = FieldMap::getIns('gender');
    $genderMap->getStatusById(0)->label('未知');
    $genderMap->getStatusById(1)->label('男');
    $genderMap->getStatusById(2)->label('女');
    $source->addFieldCover('gender', $genderMap);

    $source->page(1)->limit(15)->field('id,name,gender,time')->orderDate('time', 'asc');

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
//    $res = $source->fetchList()->all();
//    $res = $source->fetchColumn('gender');
//    $res = $source->fetchValue('gender');
//    $res = $source->fetchItem();

    $res = $source->toMap('id', 'name', function(MapStatus $mapStatus, $item) {
        $mapStatus->label($item['name'] . '-----');
        $mapStatus->disabled(true);
    }, function(FieldMap $fieldMap) {
        $fieldMap->getStatusById(-1)->label('请选择');
    });

    print_r($res);
