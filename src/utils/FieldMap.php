<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\utils;

class FieldMap
{
    /**
     * @var $status FieldMap[]
     */
    protected static array $ins;

    /**
     * @var $status MapStatus[]
     */
    protected array  $status;
    protected string $name;

    public static function getIns($name): static
    {
        if (!isset(static::$ins[$name])) {
            static::$ins[$name] = new static($name);
        }

        return static::$ins[$name];
    }

    protected function __construct($name)
    {
        $this->name = $name;
    }

    public function getStatusById($id): MapStatus
    {
        if (!isset($this->status[$id])) {
            $this->status[$id] = new MapStatus($id);
        }

        return $this->status[$id];
    }

    public function getMap(): array
    {
        $result = [];

        foreach ($this->status as $k => $v) {
            $result[$v->getId()] = $v->getFields();
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
