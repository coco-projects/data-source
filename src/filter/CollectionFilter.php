<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\filter;

    use Coco\dataSource\abstracts\BaseFilter;
    use loophp\collection\Collection;
    use loophp\collection\Contract\Collection as CollectionInterface;

class CollectionFilter extends BaseFilter
{

    public function raw(string $operator, callable $callback): static
    {
        $this->addCondition('raw', [$operator,$callback]);

        return $this;
    }

    public function eval(mixed $handler): CollectionInterface
    {
        foreach ($this->conditaion as $k => $v) {
            $key   = $v[0];
            $value = $v[1];

            switch ($key) {
                case 'raw':
                    $handler = call_user_func_array([$handler,$value[0]], [$value[1]]);
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
                case 'order':
                    $field = $value[0];
                    $order = $value[1];

                    $arr = $handler->all();
                    usort($arr, function ($a, $b) use ($field, $order) {
                        $sortKey       = $field;
                        $sortDirection = $order;

                        if ($sortDirection == 'asc') {
                            return $a[$sortKey] <=> $b[$sortKey];
                        } else {
                            return $b[$sortKey] <=> $a[$sortKey];
                        }
                    });

                    $handler = Collection::fromIterable($arr);
                    break;

                case 'orderDate':
                    $field = $value[0];
                    $order = $value[1];

                    $arr = $handler->all();
                    usort($arr, function ($a, $b) use ($field, $order) {
                        $sortKey       = $field;
                        $sortDirection = $order;

                        if ($sortDirection == 'asc') {
                            return strtotime($a[$sortKey]) <=> strtotime($b[$sortKey]);
                        } else {
                            return strtotime($b[$sortKey]) <=> strtotime($a[$sortKey]);
                        }
                    });

                    $handler = Collection::fromIterable($arr);
                    break;

                default:
                    #...
                    break;
            }
        }

        /*-
        ------------------------------------------------------------------------------------
        ------------------------------------------------------------------------------------
        -*/

        $filterOr  = [];
        $filterAnd = [];

        foreach ($this->where as $k => $v) {
            $key   = $v[0];
            $value = $v[1];
            $logic = $v[2];

            switch ($key) {
                case 'whereEq':
                    $condition = static function ($item) use ($key, $value) {
                        return $item[$value[0]] == $value[1];
                    };

                    if ($logic == 'and' && $k > 0) {
                            $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereNotEq':
                    $condition = static function ($item) use ($key, $value) {
                        return $item[$value[0]] != $value[1];
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereGt':
                    $condition = static function ($item) use ($key, $value) {
                        return $item[$value[0]] > (int)$value[1];
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereEgt':
                    $condition = static function ($item) use ($key, $value) {
                        return $item[$value[0]] >= (int)$value[1];
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereLt':
                    $condition = static function ($item) use ($key, $value) {
                        return $item[$value[0]] < (int)$value[1];
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereElt':
                    $condition = static function ($item) use ($key, $value) {
                        return $item[$value[0]] <= (int)$value[1];
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereLike':
                    $condition = static function ($item) use ($key, $value) {
                        $t = strtr((string)$value[1], ["%" => ".*",]);

                        return !!preg_match("#$t#iums", $item[$value[0]]);
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereNotLike':
                    $condition = static function ($item) use ($key, $value) {
                        $t = strtr((string)$value[1], ["%" => ".*",]);

                        return !preg_match("#$t#iums", $item[$value[0]]);
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereIn':
                    $condition = static function ($item) use ($key, $value) {
                        return in_array($item[$value[0]], $value[1]);
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereNotIn':
                    $condition = static function ($item) use ($key, $value) {
                        return !in_array($item[$value[0]], $value[1]);
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereBetween':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $min       = ($value[1][0]);
                        $max       = ($value[1][1]);

                        return ($itemValue >= $min) and ($itemValue <= $max);
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereNotBetween':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $min       = ($value[1][0]);
                        $max       = ($value[1][1]);

                        return ($itemValue < $min) or ($itemValue > $max);
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereEmpty':
                    $condition = static function ($item) use ($key, $value) {
                        return (empty($item[$value[0]]));
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereNotEmpty':
                    $condition = static function ($item) use ($key, $value) {
                        return !(empty($item[$value[0]]));
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereNull':
                    $condition = static function ($item) use ($key, $value) {
                        return (is_null($item[$value[0]]));
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereNotNull':
                    $condition = static function ($item) use ($key, $value) {
                        return !(is_null($item[$value[0]]));
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereTimeEq':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $time      = strtotime($value[1]);

                        return $itemValue == $time;
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereTimeNotEq':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $time      = strtotime($value[1]);

                        return $itemValue != $time;
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereTimeGt':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $time      = strtotime($value[1]);

                        return $itemValue > $time;
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereTimeEgt':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $time      = strtotime($value[1]);

                        return $itemValue >= $time;
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereTimeLt':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $time      = strtotime($value[1]);

                        return $itemValue < $time;
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereTimeElt':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $time      = strtotime($value[1]);

                        return $itemValue <= $time;
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereTimeBetween':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $min       = strtotime($value[1][0]);
                        $max       = strtotime($value[1][1]);

                        return ($itemValue >= $min) and ($itemValue <= $max);
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                case 'whereTimeNotBetween':
                    $condition = static function ($item) use ($key, $value) {
                        $itemValue = $item[$value[0]];
                        $min       = strtotime($value[1][0]);
                        $max       = strtotime($value[1][1]);

                        return ($itemValue < $min) or ($itemValue > $max);
                    };

                    if ($logic == 'and' && $k > 0) {
                        $filterAnd[] = $condition;
                    } else {
                        $filterOr[] = $condition;
                    }

                    break;

                default:
                    #...
                    break;
            }
        }

        $handler = $handler->filter(...$filterOr);

        foreach ($filterAnd as $k => $v) {
            $handler = $handler->filter($v);
        }

        /*-
        ------------------------------------------------------------------------------------
        ------------------------------------------------------------------------------------
        -*/

        foreach ($this->conditaion as $k => $v) {
            $key   = $v[0];
            $value = $v[1];

            switch ($key) {
                case 'field':
                    $fields = $value;

                    $data = $handler->all();

                    $arr = array_map(function ($item) use ($fields) {
                        $keys = explode(',', $fields);

                        $t = array_flip($keys);

                        $result = array_intersect_key($item, $t);

                        return $result;
                    }, $data);

                    $handler = Collection::fromIterable($arr);

                    break;

                default:
                    #...
                    break;
            }
        }

        return $handler;
    }
}
