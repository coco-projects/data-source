<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\utils;

    use Coco\sqlCache\SqlCache;
    use think\db\ConnectionInterface;
    use think\DbManager;

class MysqlHandler
{
    protected static array $ins = [];

    protected ?DbManager $dbManager = null;
    protected ?SqlCache  $sqlCache  = null;

    protected function __construct($config = [])
    {
        $this->dbManager = new DbManager();
        $this->dbManager->setConfig($config);
    }

    public static function getIns($config): ?static
    {
        $hash = md5(json_encode($config));

        if (!isset(static::$ins[$hash])) {
            static::$ins[$hash] = new static($config);
        }

        return static::$ins[$hash];
    }

    public function getDbManager(): ?DbManager
    {
        return $this->dbManager;
    }

    public function getDbConnect($name): ?ConnectionInterface
    {
        return $this->dbManager->connect($name);
    }

    public function getDefaultConnect(): ?ConnectionInterface
    {
        return $this->dbManager->connect('default');
    }
}
