<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\abstracts;

    use Coco\dataSource\utils\FieldMap;
    use loophp\collection\Contract\Collection as CollectionInterface;

abstract class DataSource
{
    /**
     * @var $ins static[]
     */
    protected static array $ins = [];

    /**
     * @var $fieldCover FieldMap[]
     */
    protected array|null $fieldCover = [];

    abstract public function fetchList(): CollectionInterface;

    abstract public function fetchItem(): array;

    abstract public function fetchColumn(string $field): array;

    abstract public function fetchValue(string $field): mixed;

    abstract public function createSource(BaseFilter $filter = null): mixed;

    public function toMap($idField, $labelField, callable $callback = null, callable $before = null, callable $after = null): array
    {
        $data = $this->fetchList();

        $map = FieldMap::getIns($labelField);

        if (is_callable($before)) {
            call_user_func_array($before, [
                $map,
            ]);
        }

        foreach ($data as $k1 => $item) {
            $map->getStatusById($item[$idField])->label($item[$labelField]);

            if (is_callable($callback)) {
                call_user_func_array($callback, [
                    $map->getStatusById($item[$idField]),
                    $item,
                ]);
            }
        }

        if (is_callable($after)) {
            call_user_func_array($after, [
                $map,
            ]);
        }

        return $map->getMap();
    }

    public function coverFieldFormAssoc(string $name, array $map): static
    {
        $mapObject = FieldMap::getIns($name);

        foreach ($map as $k => $v) {
            $mapObject->getStatusById($k)->label($v);
        }

        $this->addFieldCover($name, $mapObject);

        return $this;
    }

    public function coverFieldFormMap(string $name, array $map): static
    {
        $mapObject = FieldMap::getIns($name);

        foreach ($map as $k => $v) {
            $mapObject->getStatusById($k)->label($v['label']);
        }

        $this->addFieldCover($name, $mapObject);

        return $this;
    }

    protected function addFieldCover(string $name, FieldMap $fieldCover): static
    {
        $this->fieldCover[$name] = $fieldCover;

        return $this;
    }

    protected function getFieldCover(): ?array
    {
        return $this->fieldCover;
    }
}
