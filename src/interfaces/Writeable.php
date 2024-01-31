<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\interfaces;

interface Writeable
{
    public function delete();

    public function update(array $data);

    public function insert(array $data);

    public function insertAll(array $datas);

    public function dec(string $field);

    public function inc(string $field);
}
