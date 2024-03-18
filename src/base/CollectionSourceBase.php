<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\base;

    use Coco\dataSource\abstracts\DataSource;
    use Coco\dataSource\filter\CollectionFilter;
    use Coco\dataSource\interfaces\Computable;
    use Coco\dataSource\interfaces\Paginatable;
    use Coco\dataSource\interfaces\Sortable;
    use loophp\collection\Collection;
    use loophp\collection\Contract\Collection as CollectionInterface;

class CollectionSourceBase extends DataSource implements Paginatable, Sortable, Computable
{
    protected CollectionFilter     $filter;
    protected ?CollectionInterface $collection = null;

    protected int $page  = 1;
    protected int $limit = 1000000;

    public function __construct()
    {
        $this->filter = new CollectionFilter();
    }

    /**
     * @return CollectionFilter
     */
    public function getFilter(): CollectionFilter
    {
        return $this->filter;
    }

    public function page(int $page): static
    {
        $this->page = $page;
        $this->addCondition('page', $page);

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        $this->addCondition('limit', $limit);

        return $this;
    }

    public function order(string $field, string $order = 'asc'): static
    {
        $this->addCondition('order', [
            $field,
            $order,
        ]);

        return $this;
    }

    public function orderDate(string $field, string $order = 'asc'): static
    {
        $this->addCondition('orderDate', [
            $field,
            $order,
        ]);

        return $this;
    }

    public function getCollection(): ?CollectionInterface
    {
        return $this->collection;
    }

    public function getIterator(): iterable
    {
        return $this->fetchList();
    }

    public function setCollection(?CollectionInterface $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function raw($callback): static
    {
        $this->collection = call_user_func_array($callback, [$this->getCollection()]);

        return $this;
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function totalPages(): int
    {
        return (int)ceil($this->count() / $this->getLimit());
    }

    /*-
    ------------------------------------------------------------------------------------
    ------------------------------------------------------------------------------------
    -*/

    public function evelCondition(): static
    {
        $data = $this->collection->all();

        foreach ($data as $k1 => &$item) {
            foreach ($this->getFieldCover() as $k2 => $fieldCover) {
                $field = $fieldCover->getName();

                if (isset($item[$field])) {
                    $item[$field] = $fieldCover->getStatusById($item[$field])->getLabel();
                }
            }
        }

        $this->setCollection(Collection::fromIterable($data));

        foreach ($this->conditaion as $k => $v) {
            $key   = $v[0];
            $value = $v[1];

            switch ($key) {
                case 'order':
                    $field = $value[0];
                    $order = $value[1];

                    $arr = $this->collection->all();
                    usort($arr, function ($a, $b) use ($field, $order) {
                        $sortKey       = $field;
                        $sortDirection = $order;

                        if ($sortDirection == 'asc') {
                            return $a[$sortKey] <=> $b[$sortKey];
                        } else {
                            return $b[$sortKey] <=> $a[$sortKey];
                        }
                    });

                    $this->setCollection(Collection::fromIterable($arr));

                    break;
                case 'orderDate':
                    $field = $value[0];
                    $order = $value[1];

                    $arr = $this->collection->all();
                    usort($arr, function ($a, $b) use ($field, $order) {
                        $sortKey       = $field;
                        $sortDirection = $order;

                        if ($sortDirection == 'asc') {
                            return strtotime($a[$sortKey]) <=> strtotime($b[$sortKey]);
                        } else {
                            return strtotime($b[$sortKey]) <=> strtotime($a[$sortKey]);
                        }
                    });

                    $this->setCollection(Collection::fromIterable($arr));

                    break;

                default:
                    #...
                    break;
            }
        }

        $this->filter->evelWhere($this);

        foreach ($this->conditaion as $k => $v) {
            $key   = $v[0];
            $value = $v[1];

            switch ($key) {
                case 'field':
                    $fields = $value;

                    $data = $this->collection->all();

                    $arr = array_map(function ($item) use ($fields) {
                        $keys = explode(',', $fields);

                        $t = array_flip($keys);

                        $result = array_intersect_key($item, $t);

                        return $result;
                    }, $data);

                    $this->setCollection(Collection::fromIterable($arr));

                    break;

                default:
                    #...
                    break;
            }
        }

        return $this;
    }

    public function fetchList(): CollectionInterface
    {
        $this->evelCondition();

        return $this->collection->slice(($this->page - 1) * $this->limit, $this->limit);
    }

    public function fetchItem(): array
    {
        return $this->fetchList()->get(0);
    }

    public function fetchColumn(string $field): array
    {
        $this->evelCondition();

        return $this->collection->column($field)->all();
    }

    public function fetchValue(string $field): mixed
    {
        $this->evelCondition();
        $value = $this->fetchColumn($field);

        return $value[0] ?? null;
    }

    public function max(string $field): int|float
    {
        $this->evelCondition();

        return max($this->collection->column($field)->all());
    }

    public function min(string $field): int|float
    {
        $this->evelCondition();

        return min($this->collection->column($field)->all());
    }

    public function avg(string $field): int|float
    {
        $this->evelCondition();

        $numbers = ($this->collection->column($field)->all());

        return array_sum($numbers) / count($numbers);
    }

    public function sum(string $field): int|float
    {
        $this->evelCondition();

        return array_sum($this->collection->column($field)->all());
    }
}
