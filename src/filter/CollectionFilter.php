<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\filter;

    use Coco\dataSource\abstracts\DataSource;
    use Coco\dataSource\base\CollectionSourceBase;

class CollectionFilter extends FilterBase
{
    public function evelWhere(CollectionSourceBase|DataSource $source): void
    {
        $filterOr  = [];
        $filterAnd = [];

        foreach ($this->where as $k => $v) {
            $key   = $v[0];
            $value = $v[1];
            $logic = $v[2];

            switch ($key) {
                case 'whereEq':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] == $value[1];
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] == $value[1];
                        };
                    }
                    break;

                case 'whereNotEq':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] != $value[1];
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] != $value[1];
                        };
                    }
                    break;

                case 'whereGt':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] > $value[1];
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] > $value[1];
                        };
                    }
                    break;

                case 'whereEgt':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] >= $value[1];
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] >= $value[1];
                        };
                    }
                    break;

                case 'whereLt':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] < $value[1];
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] < $value[1];
                        };
                    }

                    break;

                case 'whereElt':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] <= $value[1];
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return $item[$value[0]] <= $value[1];
                        };
                    }
                    break;

                case 'whereLike':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            $t = strtr($value[1], ["%" => ".*",]);

                            return !!preg_match("#$t#iums", $item[$value[0]]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            $t = strtr($value[1], ["%" => ".*",]);

                            return !!preg_match("#$t#iums", $item[$value[0]]);
                        };
                    }
                    break;

                case 'whereNotLike':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            $t = strtr($value[1], ["%" => ".*",]);

                            return !preg_match("#$t#iums", $item[$value[0]]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            $t = strtr($value[1], ["%" => ".*",]);

                            return !preg_match("#$t#iums", $item[$value[0]]);
                        };
                    }
                    break;

                case 'whereIn':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return in_array($item[$value[0]], $value[1]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return in_array($item[$value[0]], $value[1]);
                        };
                    }
                    break;

                case 'whereNotIn':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return !in_array($item[$value[0]], $value[1]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return !in_array($item[$value[0]], $value[1]);
                        };
                    }
                    break;

                case 'whereBetween':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return ($item[$value[0]] >= $value[1][0]) && ($item[$value[0]] <= $value[1][1]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return ($item[$value[0]] >= $value[1][0]) && ($item[$value[0]] <= $value[1][1]);
                        };
                    }
                    break;

                case 'whereNotBetween':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return !(($item[$value[0]] >= $value[1][0]) && ($item[$value[0]] <= $value[1][1]));
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return !(($item[$value[0]] >= $value[1][0]) && ($item[$value[0]] <= $value[1][1]));
                        };
                    }
                    break;

                case 'whereEmpty':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return (is_null($item[$value[0]])) or ($item[$value[0]] == '');
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return (is_null($item[$value[0]])) or ($item[$value[0]] == '');
                        };
                    }
                    break;

                case 'whereNotEmpty':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return !(is_null($item[$value[0]])) or ($item[$value[0]] == '');
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return !(is_null($item[$value[0]])) or ($item[$value[0]] == '');
                        };
                    }
                    break;

                case 'whereNull':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return (is_null($item[$value[0]]));
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return (is_null($item[$value[0]]));
                        };
                    }
                    break;

                case 'whereNotNull':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return !(is_null($item[$value[0]]));
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return !(is_null($item[$value[0]]));
                        };
                    }
                    break;

                case 'whereTimeEq':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) == strtotime($value[1]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) == strtotime($value[1]);
                        };
                    }

                    break;

                case 'whereTimeNotEq':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) != strtotime($value[1]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) != strtotime($value[1]);
                        };
                    }
                    break;

                case 'whereTimeGt':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) > strtotime($value[1]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) > strtotime($value[1]);
                        };
                    }
                    break;

                case 'whereTimeEgt':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) >= strtotime($value[1]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) >= strtotime($value[1]);
                        };
                    }
                    break;

                case 'whereTimeLt':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) < strtotime($value[1]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) < strtotime($value[1]);
                        };
                    }
                    break;

                case 'whereTimeElt':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) <= strtotime($value[1]);
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return strtotime($item[$value[0]]) <= strtotime($value[1]);
                        };
                    }
                    break;

                case 'whereTimeBetween':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return (strtotime($item[$value[0]]) >= strtotime($value[1][0])) && (strtotime($item[$value[0]]) <= strtotime($value[1][1]));
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return (strtotime($item[$value[0]]) >= strtotime($value[1][0])) && (strtotime($item[$value[0]]) <= strtotime($value[1][1]));
                        };
                    }
                    break;

                case 'whereTimeNotBetween':
                    if (($logic == 'and') && ($k > 0)) {
                        $filterAnd[] = static function ($item) use ($key, $value) {
                            return !(strtotime($item[$value[0]]) >= strtotime($value[1][0])) && (strtotime($item[$value[0]]) <= strtotime($value[1][1]));
                        };
                    } else {
                        $filterOr[] = static function ($item) use ($key, $value) {
                            return !(strtotime($item[$value[0]]) >= strtotime($value[1][0])) && (strtotime($item[$value[0]]) <= strtotime($value[1][1]));
                        };
                    }
                    break;

                default:
                    #...
                    break;
            }
        }

        $source->setCollection($source->getCollection()->filter(...$filterOr));

        foreach ($filterAnd as $k => $v) {
            $source->setCollection($source->getCollection()->filter($v));
        }
    }
}
