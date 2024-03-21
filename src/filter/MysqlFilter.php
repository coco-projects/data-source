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
            $key     = $v[0];
            $value   = $v[1];
            $logic   = $v[2];
            $handler = $source->getTableHandler();

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
    }
}
