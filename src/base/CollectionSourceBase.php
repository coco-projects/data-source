<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\base;

    use Coco\dataSource\abstracts\BaseFilter;
    use Coco\dataSource\abstracts\DataSource;
    use Coco\dataSource\filter\CollectionFilter;
    use loophp\collection\Contract\Collection as CollectionInterface;

abstract class CollectionSourceBase extends DataSource
{
    protected mixed $callback;
    protected mixed $data;

    public function __construct(mixed $data, callable $callback = null)
    {
        $this->data     = $data;
        $this->callback = $callback;
    }

    public function count(BaseFilter $filter = null): int
    {
        return $this->createSource($filter)->count();
    }

    public function totalPages(BaseFilter $filter = null): int
    {
        if (is_null($filter)) {
            $filter = new CollectionFilter();
        }

        return (int)ceil($this->count($filter) / $filter->getLimit());
    }

    public function fetchList(BaseFilter $filter = null): CollectionInterface
    {
        if (is_null($filter)) {
            $filter = new CollectionFilter();
        }

        return $this->createSource($filter)
            ->slice(($filter->getPage() - 1) * $filter->getLimit(), $filter->getLimit());
    }

    public function fetchItem(BaseFilter $filter = null): array
    {
        return $this->fetchList()->get(0);
    }

    public function fetchColumn(string $field, BaseFilter $filter = null): array
    {
        return $this->createSource($filter)->column($field)->all();
    }

    public function fetchValue(string $field, BaseFilter $filter = null): mixed
    {
        $value = $this->fetchColumn($field);

        return $value[0] ?? null;
    }

    public function max(string $field, BaseFilter $filter = null): int|float
    {
        return max($this->createSource($filter)->column($field)->all());
    }

    public function min(string $field, BaseFilter $filter = null): int|float
    {
        return min($this->createSource($filter)->column($field)->all());
    }

    public function avg(string $field, BaseFilter $filter = null): int|float
    {
        $numbers = ($this->createSource($filter)->column($field)->all());

        return array_sum($numbers) / count($numbers);
    }

    public function sum(string $field, BaseFilter $filter = null): int|float
    {
        return array_sum($this->createSource($filter)->column($field)->all());
    }
}
