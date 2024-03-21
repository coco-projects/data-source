<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\filter;

    use Coco\dataSource\interfaces\Filterable;

abstract class FilterBase implements Filterable
{
    protected array $where = [];

    protected function addWhere(string $name, mixed $value, string $logic): static
    {
        $this->where[] = [
            $name,
            $value,
            $logic,
        ];

        return $this;
    }

    public function whereEq(string $field, mixed $value, string $logic = 'and'): static
    {
        $this->addWhere('whereEq', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereNotEq(string $field, mixed $value, string $logic = 'and'): static
    {
        $this->addWhere('whereNotEq', [
            $field,
            $value,
        ], $logic);

        return $this;
    }


    public function whereGt(string $field, string|int $value, string $logic = 'and'): static
    {
        $this->addWhere('whereGt', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereEgt(string $field, string|int $value, string $logic = 'and'): static
    {
        $this->addWhere('whereEgt', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereLt(string $field, string|int $value, string $logic = 'and'): static
    {
        $this->addWhere('whereLt', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereElt(string $field, string|int $value, string $logic = 'and'): static
    {
        $this->addWhere('whereElt', [
            $field,
            $value,
        ], $logic);

        return $this;
    }


    public function whereLike(string $field, string|int $value, string $logic = 'and'): static
    {
        $this->addWhere('whereLike', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereNotLike(string $field, string|int $value, string $logic = 'and'): static
    {
        $this->addWhere('whereNotLike', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereIn(string $field, array $value, string $logic = 'and'): static
    {
        $this->addWhere('whereIn', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereNotIn(string $field, array $value, string $logic = 'and'): static
    {
        $this->addWhere('whereNotIn', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereBetween(string $field, array $value, string $logic = 'and'): static
    {
        $this->addWhere('whereBetween', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereNotBetween(string $field, array $value, string $logic = 'and'): static
    {
        $this->addWhere('whereNotBetween', [
            $field,
            $value,
        ], $logic);

        return $this;
    }


    public function whereEmpty(string $field, mixed $value, string $logic = 'and'): static
    {
        $this->addWhere('whereEmpty', [
            $field,
            false,
        ], $logic);

        return $this;
    }

    public function whereNotEmpty(string $field, mixed $value, string $logic = 'and'): static
    {
        $this->addWhere('whereNotEmpty', [
            $field,
            false,
        ], $logic);

        return $this;
    }


    public function whereNull(string $field, mixed $value, string $logic = 'and'): static
    {
        $this->addWhere('whereNull', [
            $field,
            false,
        ], $logic);

        return $this;
    }

    public function whereNotNull(string $field, mixed $value, string $logic = 'and'): static
    {
        $this->addWhere('whereNotNull', [
            $field,
            false,
        ], $logic);

        return $this;
    }


    public function whereTimeEq(string $field, string $value, string $logic = 'and'): static
    {
        $this->addWhere('whereTimeEq', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereTimeNotEq(string $field, string $value, string $logic = 'and'): static
    {
        $this->addWhere('whereTimeNotEq', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereTimeBetween(string $field, array $value, string $logic = 'and'): static
    {
        $this->addWhere('whereTimeBetween', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereTimeNotBetween(string $field, array $value, string $logic = 'and'): static
    {
        $this->addWhere('whereTimeNotBetween', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereTimeGt(string $field, string $value, string $logic = 'and'): static
    {
        $this->addWhere('whereTimeGt', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereTimeEgt(string $field, string $value, string $logic = 'and'): static
    {
        $this->addWhere('whereTimeEgt', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereTimeLt(string $field, string $value, string $logic = 'and'): static
    {
        $this->addWhere('whereTimeLt', [
            $field,
            $value,
        ], $logic);

        return $this;
    }

    public function whereTimeElt(string $field, string $value, string $logic = 'and'): static
    {
        $this->addWhere('whereTimeElt', [
            $field,
            $value,
        ], $logic);

        return $this;
    }
}
