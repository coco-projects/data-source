<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\source;

    use Coco\dataSource\filter\MysqlFilter;
    use Coco\dataSource\interfaces\Computable;
    use Coco\dataSource\interfaces\Paginatable;
    use Coco\dataSource\interfaces\Writeable;
    use Coco\dataSource\utils\FieldMap;
    use Coco\sqlCache\SqlCache;
    use loophp\collection\Collection;
    use loophp\collection\Contract\Collection as CollectionInterface;
    use think\db\BaseQuery;
    use think\db\ConnectionInterface;
    use think\db\exception\DbException;
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

    public static function getIns(DbManager $dbManager, $connectionName, $tableName, callable $callback = null): ?static
    {
        $hash = md5(spl_object_hash($dbManager) . $connectionName);
        if (!isset(static::$ins[$hash])) {
            static::$ins[$hash] = new static($dbManager, $connectionName, $tableName, $callback);
        }

        return static::$ins[$hash];
    }

    /**
     * @return MysqlSource
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

    public function setCacheConfig($redisHost = '127.0.0.1', $redisPort = 6379, $redisPassword = '', $redisDb = 9, $prefix = 'default_db'): static
    {
        $this->redisHost     = $redisHost;
        $this->redisPort     = $redisPort;
        $this->redisPassword = $redisPassword;
        $this->redisDb       = $redisDb;
        $this->prefix        = $prefix;

        return $this;
    }

    /**
     * @return SqlCache|null
     */
    public function getSqlCache(): ?SqlCache
    {
        return $this->sqlCache;
    }

    /**
     * @return MysqlFilter
     */
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

    /**
     * @throws DbException
     */
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

    protected function evelCondition(): static
    {
        foreach ($this->conditaion as $k => $v) {
            $key   = $v[0];
            $value = $v[1];
            switch ($key) {
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
                    $item[$field] = $fieldCover->getStatusById($item[$field])->getLabel();
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
                $item[$field] = $fieldCover->getStatusById($item[$field])->getLabel();
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
