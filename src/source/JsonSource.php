<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\source;

    use Coco\dataSource\base\CollectionSourceBase;
    use loophp\collection\Collection;

class JsonSource extends CollectionSourceBase
{
    //    https://packagist.org/packages/coco-project/json-parser
    protected function __construct($source, callable $callback = null)
    {
        $data = json_decode($source, true);

        if (is_callable($callback)) {
            $data = call_user_func_array($callback, [$data]);
        }

        $this->setCollection(Collection::fromIterable($data));
        parent::__construct();
    }

    public static function getIns($source, $callback = null): ?static
    {
        $hash = md5(json_encode($source));
        if (!isset(static::$ins[$hash])) {
            static::$ins[$hash] = new static($source, $callback);
        }

        return static::$ins[$hash];
    }
}
