<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\interfaces;

interface Paginatable extends Countable
{
    public function page(int $page): static;

    public function limit(int $limit): static;

    public function getPage(): int;

    public function getLimit(): int;

    public function totalPages(): int;
}
