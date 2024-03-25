<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\filter\CollectionFilter;
    use Coco\dataSource\source\StringForLineSource;
    use Coco\dataSource\utils\ToTable;

    $command = 'ls -alh';
    $output  = shell_exec($command);
    print_r($output);
    echo PHP_EOL;

    $callback = new ToTable(function($v) {
        $res = preg_split('/ +/mu', $v, -1, PREG_SPLIT_NO_EMPTY);

        return [
            $res[0],
            $res[1],
            $res[2],
            $res[3],
            $res[4],
            date('Y-m-d H:i', strtotime($res[5] . ' ' . $res[6] . ' ' . $res[7])),
            $res[8],
        ];
    }, explode(',', 'privilege,link,user,group,size,time,fileName'));

    $source = StringForLineSource::getIns($output, $callback->setSeek(1));

    $filter = new CollectionFilter();

    $filter->raw('map', function($val) {
//        $val['time'] = strtotime($val['time']);

        return $val;
    });
    $res = $source->fetchList($filter)->all();
    print_r($res);
