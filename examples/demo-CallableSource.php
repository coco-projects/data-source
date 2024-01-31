<?php

    require '../vendor/autoload.php';

    use Bcremer\LineReader\LineReader;
    use JsonCollectionParser\Parser;
    use loophp\collection\Collection;

    $collection = Collection::fromCallable(function() {
        return range(1, 10);
    });

    print_r($collection->count());

    echo '-----------------';
    echo PHP_EOL;

    foreach ($collection->reverse() as $record)
    {
        print_r($record);
        echo PHP_EOL;
    }

    echo '-----------------';
    echo PHP_EOL;

    print_r($collection->all());
