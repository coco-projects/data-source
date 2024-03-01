<?php

    require '../vendor/autoload.php';

    use Coco\dataSource\source\StringForLineSource;
    use Coco\dataSource\utils\ToTable;

    $command = 'ls -alh';
    $output  = shell_exec($command);
print_r($output);


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

    /*
        $source->raw(function(CollectionInterface $collection) {
            return $collection->map(function($val) {
                $val['time'] = strtotime($val['time']);

                return $val;
            });
        });
        */

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
//    $res = $source->fetchColumn('name');
//    $res = $source->fetchValue('name');

    print_r($res);
