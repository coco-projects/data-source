<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\interfaces;

interface Sortable
{
    public function order(string $field, string $order = 'desc');
}
