<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\interfaces;

    use Coco\dataSource\abstracts\DataSource;

interface Filterable
{
    public function evelWhere(DataSource $source);

    public function whereEq(string $field, mixed $value, string $logic = 'and'): static;

    public function whereNotEq(string $field, mixed $value, string $logic = 'and'): static;


    public function whereGt(string $field, string|int $value, string $logic = 'and'): static;

    public function whereEgt(string $field, string|int $value, string $logic = 'and'): static;

    public function whereLt(string $field, string|int $value, string $logic = 'and'): static;

    public function whereElt(string $field, string|int $value, string $logic = 'and'): static;


    public function whereLike(string $field, string|int $value, string $logic = 'and'): static;

    public function whereNotLike(string $field, string|int $value, string $logic = 'and'): static;

    public function whereIn(string $field, array $value, string $logic = 'and'): static;

    public function whereNotIn(string $field, array $value, string $logic = 'and'): static;

    public function whereBetween(string $field, array $value, string $logic = 'and'): static;

    public function whereNotBetween(string $field, array $value, string $logic = 'and'): static;


    public function whereEmpty(string $field, mixed $value, string $logic = 'and'): static;

    public function whereNotEmpty(string $field, mixed $value, string $logic = 'and'): static;


    public function whereNull(string $field, mixed $value, string $logic = 'and'): static;

    public function whereNotNull(string $field, mixed $value, string $logic = 'and'): static;


    public function whereTimeEq(string $field, string $value, string $logic = 'and'): static;

    public function whereTimeNotEq(string $field, string $value, string $logic = 'and'): static;

    public function whereTimeBetween(string $field, array $value, string $logic = 'and'): static;

    public function whereTimeNotBetween(string $field, array $value, string $logic = 'and'): static;

    public function whereTimeGt(string $field, string $value, string $logic = 'and'): static;

    public function whereTimeEgt(string $field, string $value, string $logic = 'and'): static;

    public function whereTimeLt(string $field, string $value, string $logic = 'and'): static;

    public function whereTimeElt(string $field, string $value, string $logic = 'and'): static;
}
