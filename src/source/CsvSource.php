<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\source;

    use Coco\csvReader\Reader;
    use Coco\dataSource\base\CollectionSourceBase;
    use loophp\collection\Collection;

class CsvSource extends CollectionSourceBase
{
    protected function __construct($source, $withHeader = false, string $delimiter = ',', string $enclosure = '"', string $escape = '\\', callable $callback = null)
    {
        $reader = new Reader($delimiter, $enclosure, $escape);
        $reader->openFile($source, $withHeader);

        $data = $reader->readAll();

        $reader->close();

        if (is_callable($callback)) {
            $data = call_user_func_array($callback, [$data]);
        }

        $this->collection = Collection::fromIterable($data);
        parent::__construct();
    }

    public static function getIns($source, $withHeader = false, string $delimiter = ',', string $enclosure = '"', string $escape = '\\', callable $callback = null): ?static
    {
        $hash = md5(json_encode($source));
        if (!isset(static::$ins[$hash])) {
            static::$ins[$hash] = new static($source, $withHeader, $delimiter, $enclosure, $escape, $callback);
        }

        return static::$ins[$hash];
    }
}
