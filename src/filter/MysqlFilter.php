<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\filter;

    use Coco\dataSource\abstracts\BaseFilter;
    use think\db\BaseQuery;

class MysqlFilter extends BaseFilter
{
    public function eval(mixed $handler): BaseQuery
    {
        foreach ($this->where as $k => $v) {
            $key   = $v[0];
            $value = $v[1];
            $logic = $v[2];

            switch ($key) {
                case 'whereEq':
                    if ($logic == 'and') {
                        $handler->where($value[0], '=', $value[1]);
                    } else {
                        $handler->whereOr($value[0], '=', $value[1]);
                    }
                    break;

                case 'whereNotEq':
                    if ($logic == 'and') {
                        $handler->where($value[0], '<>', $value[1]);
                    } else {
                        $handler->whereOr($value[0], '<>', $value[1]);
                    }
                    break;

                case 'whereGt':
                    if ($logic == 'and') {
                        $handler->where($value[0], '>', (int)$value[1]);
                    } else {
                        $handler->whereOr($value[0], '>', (int)$value[1]);
                    }
                    break;

                case 'whereEgt':
                    if ($logic == 'and') {
                        $handler->where($value[0], '>=', (int)$value[1]);
                    } else {
                        $handler->whereOr($value[0], '>=', (int)$value[1]);
                    }
                    break;

                case 'whereLt':
                    if ($logic == 'and') {
                        $handler->where($value[0], '<', (int)$value[1]);
                    } else {
                        $handler->whereOr($value[0], '<', (int)$value[1]);
                    }
                    break;

                case 'whereElt':
                    if ($logic == 'and') {
                        $handler->where($value[0], '<=', (int)$value[1]);
                    } else {
                        $handler->whereOr($value[0], '<=', (int)$value[1]);
                    }
                    break;

                case 'whereLike':
                    if ($logic == 'and') {
                        $handler->where($value[0], 'like', (string)$value[1]);
                    } else {
                        $handler->whereOr($value[0], 'like', (string)$value[1]);
                    }
                    break;

                case 'whereNotLike':
                    if ($logic == 'and') {
                        $handler->where($value[0], 'not like', (string)$value[1]);
                    } else {
                        $handler->whereOr($value[0], 'not like', (string)$value[1]);
                    }
                    break;

                case 'whereIn':
                    if ($logic == 'and') {
                        $handler->where($value[0], 'in', $value[1]);
                    } else {
                        $handler->whereOr($value[0], 'in', $value[1]);
                    }
                    break;

                case 'whereNotIn':
                    if ($logic == 'and') {
                        $handler->where($value[0], 'not in', $value[1]);
                    } else {
                        $handler->whereOr($value[0], 'not in', $value[1]);
                    }
                    break;

                case 'whereBetween':
                    if ($logic == 'and') {
                        $handler->where($value[0], 'between', $value[1]);
                    } else {
                        $handler->whereOr($value[0], 'between', $value[1]);
                    }
                    break;

                case 'whereNotBetween':
                    if ($logic == 'and') {
                        $handler->where($value[0], 'not between', $value[1]);
                    } else {
                        $handler->whereOr($value[0], 'not between', $value[1]);
                    }
                    break;

                case 'whereEmpty':
                    if ($logic == 'and') {
                        $handler->where(function ($query) use ($value) {
                            $query->whereNull($value[0])->whereOr($value[0], '=', '');
                        });
                    } else {
                        $handler->whereOr(function ($query) use ($value) {
                            $query->whereNull($value[0])->whereOr($value[0], '=', '');
                        });
                    }
                    break;

                case 'whereNotEmpty':
                    if ($logic == 'and') {
                        $handler->where(function ($query) use ($value) {
                            $query->whereNotNull($value[0])->where($value[0], '<>', '');
                        });
                    } else {
                        $handler->whereOr(function ($query) use ($value) {
                            $query->whereNotNull($value[0])->where($value[0], '<>', '');
                        });
                    }
                    break;

                case 'whereNull':
                    if ($logic == 'and') {
                        $handler->where(function ($query) use ($value) {
                            $query->whereNull($value[0]);
                        });
                    } else {
                        $handler->whereOr(function ($query) use ($value) {
                            $query->whereNull($value[0]);
                        });
                    }
                    break;

                case 'whereNotNull':
                    if ($logic == 'and') {
                        $handler->where(function ($query) use ($value) {
                            $query->whereNotNull($value[0]);
                        });
                    } else {
                        $handler->whereOr(function ($query) use ($value) {
                            $query->whereNotNull($value[0]);
                        });
                    }
                    break;

                case 'whereTimeEq':
                    if ($logic == 'and') {
                        $handler->where($value[0], '=', strtotime($value[1]));
                    } else {
                        $handler->whereOr($value[0], '=', strtotime($value[1]));
                    }
                    break;

                case 'whereTimeNotEq':
                    if ($logic == 'and') {
                        $handler->where($value[0], '<>', strtotime($value[1]));
                    } else {
                        $handler->whereOr($value[0], '<>', strtotime($value[1]));
                    }
                    break;

                case 'whereTimeGt':
                    if ($logic == 'and') {
                        $handler->where($value[0], '>', strtotime($value[1]));
                    } else {
                        $handler->whereOr($value[0], '>', strtotime($value[1]));
                    }
                    break;

                case 'whereTimeEgt':
                    if ($logic == 'and') {
                        $handler->where($value[0], '>=', strtotime($value[1]));
                    } else {
                        $handler->whereOr($value[0], '>=', strtotime($value[1]));
                    }
                    break;

                case 'whereTimeLt':
                    if ($logic == 'and') {
                        $handler->where($value[0], '<', strtotime($value[1]));
                    } else {
                        $handler->whereOr($value[0], '<', strtotime($value[1]));
                    }
                    break;

                case 'whereTimeElt':
                    if ($logic == 'and') {
                        $handler->where($value[0], '<=', strtotime($value[1]));
                    } else {
                        $handler->whereOr($value[0], '<=', strtotime($value[1]));
                    }
                    break;

                case 'whereTimeBetween':
                    $value[1][0] = strtotime($value[1][0]);
                    $value[1][1] = strtotime($value[1][1]);

                    if ($logic == 'and') {
                        $handler->where($value[0], 'between', $value[1]);
                    } else {
                        $handler->whereOr($value[0], 'between', $value[1]);
                    }
                    break;

                case 'whereTimeNotBetween':
                    $value[1][0] = strtotime($value[1][0]);
                    $value[1][1] = strtotime($value[1][1]);

                    if ($logic == 'and') {
                        $handler->where($value[0], 'not between', $value[1]);
                    } else {
                        $handler->whereOr($value[0], 'not between', $value[1]);
                    }
                    break;

                default:
                    #...
                    break;
            }
        }

        foreach ($this->conditaion as $k => $v) {
            $key   = $v[0];
            $value = $v[1];

            switch ($key) {
                case 'duplicate':
                    $handler->duplicate($value);
                    break;

                case 'json':
                    $handler->json($value);
                    break;

                case 'alias':
                    $handler->alias($value);
                    break;

                case 'distinct':
                    $handler->distinct($value);
                    break;

                case 'extra':
                    $handler->extra($value);
                    break;

                case 'union':
                    $handler->union($value);
                    break;

                case 'having':
                    $handler->having($value);
                    break;

                case 'group':
                    $handler->group($value);
                    break;

                case 'page':
                    $handler->page($value);
                    break;

                case 'limit':
                    $handler->limit($value);
                    break;

                case 'order':
                case 'orderDate':
                    $handler->order($value[0], $value[1]);
                    break;

                case 'join':
                    $handler->join($value[0], $value[1]);
                    break;

                case 'leftJoin':
                    $handler->leftJoin($value[0], $value[1]);
                    break;

                case 'rightJoin':
                    $handler->rightJoin($value[0], $value[1]);
                    break;

                case 'fullJoin':
                    $handler->fullJoin($value[0], $value[1]);
                    break;

                case 'exp':
                    $handler->exp($value[0], $value[1]);
                    break;

                case 'field':
                    $handler->field($value);
                    break;

                default:
                    #...
                    break;
            }
        }

        return $handler;
    }

    public function group(string $group): static
    {
        $this->addCondition('group', $group);

        return $this;
    }

    public function having(string $having): static
    {
        $this->addCondition('having', $having);

        return $this;
    }

    public function alias(string $alias): static
    {
        $this->addCondition('alias', $alias);

        return $this;
    }

    public function duplicate(array $duplicate): static
    {
        $this->addCondition('duplicate', $duplicate);

        return $this;
    }

    public function extra(string $extra): static
    {
        $this->addCondition('extra', $extra);

        return $this;
    }

    public function distinct(bool $distinct): static
    {
        $this->addCondition('distinct', $distinct);

        return $this;
    }

    public function lock(bool $lock): static
    {
        $this->addCondition('lock', $lock);

        return $this;
    }

    public function union(callable|array $callback): static
    {
        $this->addCondition('union', $callback);

        return $this;
    }

    public function json(array $json): static
    {
        $this->addCondition('json', $json);

        return $this;
    }

    public function exp(string $field, string $value): static
    {
        $this->addCondition('exp', [
            $field,
            $value,
        ]);

        return $this;
    }

    public function join(string|array $field, string $on = null): static
    {
        $this->addCondition('join', [
            $field,
            $on,
        ]);

        return $this;
    }

    public function leftJoin(string|array $field, string $on = null): static
    {
        $this->addCondition('leftJoin', [
            $field,
            $on,
        ]);

        return $this;
    }

    public function rightJoin(string|array $field, string $on = null): static
    {
        $this->addCondition('rightJoin', [
            $field,
            $on,
        ]);

        return $this;
    }

    public function fullJoin(string|array $field, string $on = null): static
    {
        $this->addCondition('fullJoin', [
            $field,
            $on,
        ]);

        return $this;
    }
}
