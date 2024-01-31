<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\source;

    use Coco\dataSource\base\CollectionSourceBase;
    use loophp\collection\Collection;

class FileForLineSource extends CollectionSourceBase
{
    protected function __construct($source, callable $callback = null)
    {
        $data = file($source, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (is_callable($callback)) {
            $data = call_user_func_array($callback, [$data]);
        }

        $this->collection = Collection::fromIterable($data);
        parent::__construct();
    }

    public static function getIns($source, callable $callback = null): ?static
    {
        $hash = md5(json_encode($source));
        if (!isset(static::$ins[$hash])) {
            static::$ins[$hash] = new static($source, $callback);
        }

        return static::$ins[$hash];
    }
}
