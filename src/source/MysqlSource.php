<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\source;

    use Coco\dataSource\abstracts\BaseFilter;
    use Coco\dataSource\filter\MysqlFilter;
    use Coco\sqlCache\SqlCache;
    use loophp\collection\Collection;
    use loophp\collection\Contract\Collection as CollectionInterface;
    use think\db\BaseQuery;
    use think\db\ConnectionInterface;
    use Coco\dataSource\abstracts\DataSource;
    use think\DbManager;

class MysqlSource extends DataSource
{
    protected ?ConnectionInterface $dbConnect    = null;
    protected ?DbManager           $dbManager    = null;
    protected ?string              $tableName    = null;

    protected bool          $enableCache   = false;
    protected SqlCache|null $sqlCache      = null;
    private string          $redisHost     = '127.0.0.1';
    private int             $redisPort     = 6379;
    private string          $redisPassword = '';
    private int             $redisDb       = 9;
    private string          $prefix        = 'default_db';

    //    https://www.kancloud.cn/manual/think-orm/1257999
    protected function __construct(DbManager $dbManager, $connectionName, $tableName, callable $callback = null)
    {
        $this->dbManager = $dbManager;
        $this->dbConnect = $this->dbManager->connect($connectionName);
        $this->tableName = $tableName;

        if (is_callable($callback)) {
            call_user_func_array($callback, [$this]);
        }
    }

    public static function getIns(DbManager $dbManager, string $connectionName, $tableName, callable $callback = null): ?static
    {
        $hash = md5(spl_object_hash($dbManager) . $connectionName);

        if (!isset(static::$ins[$hash])) {
            static::$ins[$hash] = new static($dbManager, $connectionName, $tableName, $callback);
        }

        return static::$ins[$hash];
    }

    public function createSource(BaseFilter $filter = null): BaseQuery
    {
        if (is_null($filter)) {
            $filter = new MysqlFilter();
        }

        $handler = $this->dbConnect->table($this->tableName);

        return $filter->eval($handler);
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
        $this->createSource()->startTrans();
    }

    public function commit(): void
    {
        $this->createSource()->commit();
    }

    public function rollback(): void
    {
        $this->createSource()->rollback();
    }

    public function getFields(): array
    {
        return $this->createSource()->getTableFields();
    }


    /*-
    ------------------------------------------------------------------------------------
    ------------------------------------------------------------------------------------
    -*/

    public function fetchList(BaseFilter $filter = null): CollectionInterface
    {
        if (is_null($filter)) {
            $filter = new MysqlFilter();
        }

        $callback = function () use ($filter) {
            return $this->createSource($filter)->select()->toArray();
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->fetchListSql($filter), $callback);
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

    public function fetchItem(BaseFilter $filter = null): array
    {
        $callback = function () use ($filter) {
            return $this->createSource($filter)->findOrEmpty();
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->fetchItemSql($filter), $callback);
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

    public function fetchColumn(string $field, BaseFilter $filter = null): array
    {
        $callback = function () use ($filter, $field) {
            return $this->createSource($filter)->column($field);
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->fetchColumnSql($field, $filter), $callback);
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

    public function fetchValue(string $field, BaseFilter $filter = null): mixed
    {
        $callback = function () use ($filter, $field) {
            return $this->createSource($filter)->value($field);
        };

        if (!$this->enableCache) {
                $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->fetchValueSql($field, $filter), $callback);
        }

        foreach ($this->getFieldCover() as $k2 => $fieldCover) {
            if ($field == $fieldCover->getName()) {
                $data = $fieldCover->getStatusById($data)->getLabel();
                break;
            }
        }

            return $data;
    }

    public function count(BaseFilter $filter = null): int
    {
        $callback = function () use ($filter) {
            return $this->createSource($filter)->count();
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->countSql($filter), $callback);
        }

            return $data;
    }

    public function totalPages(BaseFilter $filter = null): int
    {
        if (is_null($filter)) {
            $filter = new MysqlFilter();
        }

        return (int)ceil($this->count($filter) / $filter->getLimit());
    }

    public function max(string $field, BaseFilter $filter = null): int|float
    {
        $callback = function () use ($filter, $field) {
            return $this->createSource($filter)->max($field);
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->maxSql($field, $filter), $callback);
        }

            return $data;
    }

    public function min(string $field, BaseFilter $filter = null): int|float
    {
        $callback = function () use ($filter, $field) {
            return $this->createSource($filter)->min($field);
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->minSql($field, $filter), $callback);
        }

            return $data;
    }

    public function avg(string $field, BaseFilter $filter = null): int|float
    {
        $callback = function () use ($filter, $field) {
            return $this->createSource($filter)->avg($field);
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->avgSql($field, $filter), $callback);
        }

            return $data;
    }

    public function sum(string $field, BaseFilter $filter = null): int|float
    {
        $callback = function () use ($filter, $field) {
            return $this->createSource($filter)->sum($field);
        };

        if (!$this->enableCache) {
            $data = $callback();
        } else {
            $data = $this->sqlCache->autoCache($this->sumSql($field, $filter), $callback);
        }

            return $data;
    }

    /*-
    ------------------------------------------------------------------------------------
    ------------------------------------------------------------------------------------
    -*/
    public function delete(BaseFilter $filter = null): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->deleteSql($filter));
        }

        return $this->createSource($filter)->delete();
    }

    public function update(array $data, BaseFilter $filter = null): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->updateSql($data, $filter));
        }

        return $this->createSource($filter)->update($data);
    }

    public function insert(array $data): int|string
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->insertSql($data));
        }

        return $this->createSource()->insert($data);
    }

    public function insertAll(array $datas): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->insertAllSql($datas));
        }

        return $this->createSource()->limit(200)->insertAll($datas);
    }

    public function dec(string $field, float $step = 1, BaseFilter $filter = null): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->decSql($field, $step, $filter));
        }

        return $this->createSource($filter)->dec($field, $step)->update();
    }

    public function inc(string $field, float $step = 1, BaseFilter $filter = null): int
    {
        if ($this->enableCache) {
            $this->sqlCache->clearBySql($this->incSql($field, $step, $filter));
        }

        return $this->createSource($filter)->inc($field, $step)->update();
    }

    /*-
    ------------------------------------------------------------------------------------
    ------------------------------------------------------------------------------------
    -*/
    public function fetchListSql(BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->select();
    }

    public function fetchItemSql(BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->findOrEmpty();
    }

    public function fetchColumnSql(string $field, BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->column($field);
    }

    public function fetchValueSql(string $field, BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->value($field);
    }

    public function maxSql(string $field, BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->max($field);
    }

    public function minSql(string $field, BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->min($field);
    }

    public function avgSql(string $field, BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->avg($field);
    }

    public function sumSql(string $field, BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->sum($field);
    }

    public function updateSql(array $data, BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->update($data);
    }

    public function decSql(string $field, float $step = 1, BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->dec($field, $step)->update();
    }

    public function incSql(string $field, float $step = 1, BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->inc($field, $step)->update();
    }

    public function deleteSql(BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->delete();
    }

    public function countSql(BaseFilter $filter = null): string
    {
        return $this->createSource($filter)->fetchSql()->count();
    }

    public function insertSql(array $data): string
    {
        return $this->createSource()->fetchSql()->insert($data);
    }

    public function insertAllSql(array $datas): string
    {
        return $this->createSource()->fetchSql()->limit(200)->insertAll($datas);
    }
}
