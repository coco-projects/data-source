<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\source;

    use Coco\dataSource\filter\MysqlFilter;
    use Coco\dataSource\interfaces\Computable;
    use Coco\dataSource\interfaces\Paginatable;
    use Coco\dataSource\interfaces\Writeable;
    use Coco\sqlCache\SqlCache;
    use loophp\collection\Collection;
    use loophp\collection\Contract\Collection as CollectionInterface;
    use think\db\BaseQuery;
    use think\db\ConnectionInterface;
    use Coco\dataSource\abstracts\DataSource;
    use Coco\dataSource\interfaces\Countable;
    use Coco\dataSource\interfaces\Sortable;
    use think\DbManager;

class MysqlSource extends DataSource implements Sortable, Countable, Paginatable, Computable, Writeable
{
    protected ?BaseQuery           $tableHandler  = null;
    protected ?ConnectionInterface $dbConnect     = null;
    protected ?DbManager           $dbManager     = null;
    protected ?string              $tableName     = null;
    protected MysqlFilter          $filter;
    protected int                  $page          = 1;
    protected int                  $limit         = 10;
    protected bool                 $enableCache   = false;
    protected SqlCache|null        $sqlCache      = null;
    private string                 $redisHost     = '127.0.0.1';
    private int                    $redisPort     = 6379;
    private string                 $redisPassword = '';
    private int                    $redisDb       = 9;
    private string                 $prefix        = 'default_db';

    //    https://www.kancloud.cn/manual/think-orm/1257999
    protected function __construct(DbManager $dbManager, $connectionName, $tableName, callable $callback = null)
    {
        $this->dbManager = $dbManager;
        $this->dbConnect = $this->dbManager->connect($connectionName);
        $this->tableName = $tableName;
        $this->filter    = new MysqlFilter();
        if (is_callable($callback)) {
            call_user_func_array($callback, [$this]);
        }
        $this->resetTableHandler();
    }

    public static function getIns(DbManager $dbManager, string $connectionName, $tableName, callable $callback = null): ?static
    {
        $hash = md5(spl_object_hash($dbManager) . $connectionName . $tableName);
        if (!isset(static::$ins[$hash])) {
            static::$ins[$hash] = new static($dbManager, $connectionName, $tableName, $callback);
        }

        return static::$ins[$hash];
    }

    /**
     * @return DbManager|null
     */
    public function getDbManager(): ?DbManager
    {
        return $this->dbManager;
    }

    /**
     * @param bool $isEnable
     *
     * @return MysqlSource
     * @throws \RedisException
     */
    public function enableCache(bool $isEnable = true): static
    {
        $this->enableCache = $isEnable;
        if ($isEnable) {
            $this->sqlCache = new SqlCache($this->redisHost, $this->redisPort, $this->redisPassword, $this->redisDb, $this->prefix);
            $this->sqlCache->setIsAnalysisEnabled(true);
        }

        return $this;
    }

    /**
     * @param string $redisHost
     * @param int    $redisPort
     * @param string $redisPassword
     * @param int    $redisDb
     * @param string $prefix
     *
     * @return $this
     */
    public function setCacheConfig(string $redisHost = '127.0.0.1', int $redisPort = 6379, string $redisPassword = '', int $redisDb = 9, string $prefix = 'default_db'): static
    {
        $this->redisHost     = $redisHost;
        $this->redisPort     = $redisPort;
        $this->redisPassword = $redisPassword;
        $this->redisDb       = $redisDb;
        $this->prefix        = $prefix;

        return $this;
    }


    public function getSqlCache(): ?SqlCache
    {
        return $this->sqlCache;
    }

    public function getFilter(): MysqlFilter
    {
        return $this->filter;
    }

    public function getTableHandler(): BaseQuery
    {
        return $this->tableHandler;
    }

    public function resetTableHandler(): static
    {
        $this->tableHandler = $this->dbConnect->table($this->tableName);

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function totalPages(): int
    {
        return (int)ceil($this->count() / $this->getLimit());
    }

    public function page(int $page): static
    {
        $this->page = $page;
        $this->addCondition('page', $page);

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        $this->addCondition('limit', $limit);

        return $this;
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

    public function order(string $field, string $order = 'asc'): static
    {
        $this->addCondition('order', [
            $field,
            $order,
        ]);

        return $this;
    }

    public function orderDate(string $field, string $order = 'asc'): static
    {
        $this->addCondition('orderDate', [
            $field,
            $order,
        ]);

        return $this;
    }

    public function query(string $sql): mixed
    {
        return $this->getDbManager()->query($sql);
    }

    public function execute(string $sql): mixed
    {
        return $this->getDbManager()->execute($sql);
    }

    public function startTrans(): void
    {
        $this->getTableHandler()->startTrans();
    }

    public function commit(): void
    {
        $this->getTableHandler()->commit();
    }

    public function rollback(): void
    {
        $this->getTableHandler()->rollback();
    }

    public function getFields(): array
    {
        return $this->getTableHandler()->getTableFields();
    }

    protected function evelCondition(): static
    {
        foreach ($this->conditaion as $k => $v) {
            $key   = $v[0];
            $value = $v[1];
            switch ($key) {
                case 'duplicate':
                    $this->getTableHandler()->duplicate($value);
                    break;

                case 'json':
                    $this->getTableHandler()->json($value);
                    break;

                case 'alias':
                    $this->getTableHandler()->alias($value);
                    break;

                case 'distinct':
                    $this->getTableHandler()->distinct($value);
                    break;

                case 'extra':
                    $this->getTableHandler()->extra($value);
                    break;

                case 'union':
                    $this->getTableHandler()->union($value);
                    break;

                case 'having':
                    $this->getTableHandler()->having($value);
                    break;

                case 'group':
                    $this->getTableHandler()->group($value);
                    break;

                case 'page':
                    $this->getTableHandler()->page($value);
                    break;

                case 'limit':
                    $this->getTableHandler()->limit($value);
                    break;

                case 'order':
                case 'orderDate':
                    $this->getTableHandler()->order($value[0], $value[1]);
                    break;

                case 'join':
                    $this->getTableHandler()->join($value[0], $value[1]);
                    break;

                case 'leftJoin':
                    $this->getTableHandler()->leftJoin($value[0], $value[1]);
                    break;

                case 'rightJoin':
                    $this->getTableHandler()->rightJoin($value[0], $value[1]);
                    break;

                case 'fullJoin':
                    $this->getTableHandler()->fullJoin($value[0], $value[1]);
                    break;

                case 'exp':
                    $this->getTableHandler()->exp($value[0], $value[1]);
                    break;

                case 'field':
                    $this->getTableHandler()->field($value);
                    break;

                default:
                    #...
                    break;
            }
        }
        $this->filter->evelWhere($this);

        return $this;
    }

    /*-
    ------------------------------------------------------------------------------------
    ------------------------------------------------------------------------------------
    -*/

    public function fetchList(): CollectionInterface
    {
        $callback = function () {
            return $this->resetTableHandler()->evelCondition()->getTableHandler()->select()->toArray();
        };
        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->fetchListSql(), $callback);
        }

        foreach ($data as $k1 => &$item) {
            foreach ($this->getFieldCover() as $k2 => $fieldCover) {
                $field = $fieldCover->getName();

                if (isset($item[$field])) {
                    $ids = explode(',', (string)$item[$field]);

                    $fieldValue = [];

                    foreach ($ids as $id) {
                        $fieldValue[] = $fieldCover->getStatusById($id)->getLabel();
                    }

                    $item[$field] = implode(',', $fieldValue);
                }
            }
        }

            return Collection::fromIterable($data);
    }

    public function fetchItem(): array
    {
        $callback = function () {
            return $this->resetTableHandler()->evelCondition()->getTableHandler()->findOrEmpty();
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->fetchItemSql(), $callback);
        }

            $item = &$data;

        foreach ($this->getFieldCover() as $k2 => $fieldCover) {
            $field = $fieldCover->getName();

            if (isset($item[$field])) {
                $ids = explode(',', (string)$item[$field]);

                $fieldValue = [];

                foreach ($ids as $id) {
                    $fieldValue[] = $fieldCover->getStatusById($id)->getLabel();
                }

                $item[$field] = implode(',', $fieldValue);
            }
        }

            return $data;
    }

    public function fetchColumn(string $field): array
    {
        $callback = function () use ($field) {
            return $this->resetTableHandler()->evelCondition()->getTableHandler()->column($field);
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->fetchColumnSql($field), $callback);
        }

        foreach ($this->getFieldCover() as $k2 => $fieldCover) {
            if ($field == $fieldCover->getName()) {
                foreach ($data as $k => &$v) {
                    $v = $fieldCover->getStatusById($v)->getLabel();
                }
            }
        }

            return $data;
    }

    public function fetchValue(string $field): mixed
    {
        $callback = function () use ($field) {
            return $this->resetTableHandler()->evelCondition()->getTableHandler()->value($field);
        };
        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->fetchValueSql($field), $callback);
        }

        foreach ($this->getFieldCover() as $k2 => $fieldCover) {
            if ($field == $fieldCover->getName()) {
                $data = $fieldCover->getStatusById($data)->getLabel();
                break;
            }
        }

            return $data;
    }

    public function count(): int
    {
        $callback = function () {
            return $this->resetTableHandler()->evelCondition()->getTableHandler()->count();
        };
        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->countSql(), $callback);
        }

            return $data;
    }

    public function max(string $field): int|float
    {
        $callback = function () use ($field) {
            return $this->resetTableHandler()->evelCondition()->getTableHandler()->max($field);
        };
        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->maxSql($field), $callback);
        }

            return $data;
    }

    public function min(string $field): int|float
    {
        $callback = function () use ($field) {
            return $this->resetTableHandler()->evelCondition()->getTableHandler()->min($field);
        };
        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->minSql($field), $callback);
        }

            return $data;
    }

    public function avg(string $field): int|float
    {
        $callback = function () use ($field) {
            return $this->resetTableHandler()->evelCondition()->getTableHandler()->avg($field);
        };
        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->avgSql($field), $callback);
        }

            return $data;
    }

    public function sum(string $field): int|float
    {
        $callback = function () use ($field) {
            return $this->resetTableHandler()->evelCondition()->getTableHandler()->sum($field);
        };
        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->sumSql($field), $callback);
        }

            return $data;
    }

    /*-
    ------------------------------------------------------------------------------------
    ------------------------------------------------------------------------------------
    -*/
    public function delete(): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->deleteSql());
        }

        return $this->resetTableHandler()->getTableHandler()->evelCondition()->delete();
    }

    public function update(array $data): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->updateSql($data));
        }

        return $this->resetTableHandler()->getTableHandler()->evelCondition()->update($data);
    }

    public function insert(array $data): int|string
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->insertSql($data));
        }

        return $this->resetTableHandler()->getTableHandler()->evelCondition()->insert($data);
    }

    public function insertAll(array $datas): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->insertAllSql($datas));
        }

        return $this->resetTableHandler()->getTableHandler()->evelCondition()->limit(200)->insertAll($datas);
    }

    public function dec(string $field): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->decSql($field));
        }

        return $this->resetTableHandler()->getTableHandler()->evelCondition()->dec($field)->update();
    }

    public function inc(string $field): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->incSql($field));
        }

        return $this->resetTableHandler()->getTableHandler()->evelCondition()->inc($field)->update();
    }

    /*-
    ------------------------------------------------------------------------------------
    ------------------------------------------------------------------------------------
    -*/
    public function fetchListSql(): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->select();
    }

    public function fetchItemSql(): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->findOrEmpty();
    }

    public function fetchColumnSql(string $field): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->column($field);
    }

    public function fetchValueSql(string $field): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->value($field);
    }

    public function countSql(): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->count();
    }

    public function maxSql(string $field): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->max($field);
    }

    public function minSql(string $field): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->min($field);
    }

    public function avgSql(string $field): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->avg($field);
    }

    public function sumSql(string $field): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->sum($field);
    }

    public function deleteSql(): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->delete();
    }

    public function updateSql(array $data): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->update($data);
    }

    public function insertSql(array $data): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->insert($data);
    }

    public function insertAllSql(array $datas): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->limit(200)
            ->insertAll($datas);
    }

    public function decSql(string $field): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->dec($field)->update();
    }

    public function incSql(string $field): string
    {
        return $this->resetTableHandler()->evelCondition()->getTableHandler()->fetchSql()->inc($field)->update();
    }
}
