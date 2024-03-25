<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\source;

    use Coco\dataSource\abstracts\BaseFilter;
    use Coco\dataSource\base\CollectionSourceBase;
    use Coco\dataSource\filter\CollectionFilter;
    use loophp\collection\Collection;

class IterableSource extends CollectionSourceBase
{
    // https://loophp-collection.readthedocs.io/en/latest/pages/api.html
    protected function __construct($source, callable $callback = null)
    {
        $data = $source;

        parent::__construct($data, $callback);
    }

    public static function getIns($source, $callback = null): ?static
    {
        return  new static($source, $callback);
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
