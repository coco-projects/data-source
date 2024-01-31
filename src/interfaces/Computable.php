<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\interfaces;

interface Computable
{
    public function max(string $field): int|float;

    public function min(string $field): int|float;

    public function avg(string $field): int|float;

    public function sum(string $field): int|float;
}
