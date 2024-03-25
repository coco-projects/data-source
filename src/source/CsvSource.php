<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\source;

    use Coco\csvReader\Reader;
    use Coco\dataSource\abstracts\BaseFilter;
    use Coco\dataSource\base\CollectionSourceBase;
    use Coco\dataSource\filter\CollectionFilter;
    use loophp\collection\Collection;

class CsvSource extends CollectionSourceBase
{
    protected function __construct($source, $withHeader = false, string $delimiter = ',', string $enclosure = '"', string $escape = '\\', callable $callback = null)
    {
        $reader = new Reader($delimiter, $enclosure, $escape);
        $reader->openFile($source, $withHeader);
        $data = $reader->readAll();
        $reader->close();

        parent::__construct($data, $callback);
    }


    // https://loophp-collection.readthedocs.io/en/latest/pages/api.html

    public static function getIns($source, $callback = null): ?static
    {
        $hash = md5($source);

        if (!isset(static::$ins[$hash])) {
            static::$ins[$hash] = new static($source, $callback);
        }

        return static::$ins[$hash];
    }

    public function createSource(BaseFilter $filter = null): \loophp\collection\Contract\Collection
    {
        if (is_null($filter)) {
            $filter = new CollectionFilter();
        }

        $data = $this->data;

        if (is_callable($this->callback)) {
            $data = call_user_func_array($this->callback, [$data]);
        }

        $handler = Collection::fromIterable($data);

        $data = $handler->all();

        foreach ($data as $k1 => &$item) {
            foreach ($this->getFieldCover() as $k2 => $fieldCover) {
                $field = $fieldCover->getName();

                if (isset($item[$field])) {
                    $ids = explode(',', (string)$item[$field]);

                    $fieldValue = [];

                    foreach ($ids as $id) {
                        $fieldValue[] = $fieldCover->getStatusById($id)->getLabel();
                    }

                    $item[$field] = implode(',', $fieldValue);
                }
            }
        }

        $handler = Collection::fromIterable($data);

        return $filter->eval($handler);
    }
}
