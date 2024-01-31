<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\filter;

    use Coco\dataSource\abstracts\DataSource;
    use Coco\dataSource\source\MysqlSource;

class MysqlFilter extends FilterBase
{
    public function evelWhere(MysqlSource|DataSource $source): void
    {
        foreach ($this->where as $k => $v) {
            $key   = $v[0];
            $value = $v[1];
            $logic = $v[2];

            switch ($key) {
                case 'whereEq':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '=', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '=', $value[1]);
                    }
                    break;

                case 'whereNotEq':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '<>', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '<>', $value[1]);
                    }
                    break;

                case 'whereGt':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '>', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '>', $value[1]);
                    }
                    break;

                case 'whereEgt':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '>=', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '>=', $value[1]);
                    }
                    break;

                case 'whereLt':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '<', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '<', $value[1]);
                    }
                    break;

                case 'whereElt':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '<=', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '<=', $value[1]);
                    }
                    break;

                case 'whereLike':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], 'like', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], 'like', $value[1]);
                    }
                    break;

                case 'whereNotLike':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], 'not like', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], 'not like', $value[1]);
                    }
                    break;

                case 'whereIn':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], 'in', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], 'in', $value[1]);
                    }
                    break;

                case 'whereNotIn':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], 'not in', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], 'not in', $value[1]);
                    }
                    break;

                case 'whereBetween':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], 'between', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], 'between', $value[1]);
                    }
                    break;

                case 'whereNotBetween':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], 'not between', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], 'not between', $value[1]);
                    }
                    break;

                case 'whereEmpty':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where(function ($query) use ($value) {
                            $query->whereNull($value[0])->whereOr($value[0], '=', '');
                        });
                    } else {
                        $source->getTableHandler()->whereOr(function ($query) use ($value) {
                            $query->whereNull($value[0])->whereOr($value[0], '=', '');
                        });
                    }
                    break;

                case 'whereNotEmpty':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where(function ($query) use ($value) {
                            $query->whereNotNull($value[0])->where($value[0], '<>', '');
                        });
                    } else {
                        $source->getTableHandler()->whereOr(function ($query) use ($value) {
                            $query->whereNotNull($value[0])->where($value[0], '<>', '');
                        });
                    }
                    break;

                case 'whereNull':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where(function ($query) use ($value) {
                            $query->whereNull($value[0]);
                        });
                    } else {
                        $source->getTableHandler()->whereOr(function ($query) use ($value) {
                            $query->whereNull($value[0]);
                        });
                    }
                    break;

                case 'whereNotNull':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where(function ($query) use ($value) {
                            $query->whereNotNull($value[0]);
                        });
                    } else {
                        $source->getTableHandler()->whereOr(function ($query) use ($value) {
                            $query->whereNotNull($value[0]);
                        });
                    }
                    break;

                case 'whereTimeEq':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '=', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '=', $value[1]);
                    }
                    break;

                case 'whereTimeNotEq':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '<>', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '<>', $value[1]);
                    }
                    break;

                case 'whereTimeGt':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '>', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '>', $value[1]);
                    }
                    break;

                case 'whereTimeEgt':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '>=', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '>=', $value[1]);
                    }
                    break;

                case 'whereTimeLt':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '<', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '<', $value[1]);
                    }
                    break;

                case 'whereTimeElt':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], '<=', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], '<=', $value[1]);
                    }
                    break;

                case 'whereTimeBetween':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], 'between', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], 'between', $value[1]);
                    }
                    break;

                case 'whereTimeNotBetween':
                    if ($logic == 'and') {
                        $source->getTableHandler()->where($value[0], 'not between', $value[1]);
                    } else {
                        $source->getTableHandler()->whereOr($value[0], 'not between', $value[1]);
                    }
                    break;

                default:
                    #...
                    break;
            }
        }
    }
}
