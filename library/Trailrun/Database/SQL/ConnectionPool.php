<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Database\SQL;

use Fightmaster\Trailrun\Database\PDOReconnectProxy;

class ConnectionPool
{

    /**
     * @var PDOReconnectProxy[]
     */
    private $connections;

    private $connectionsData;

    public function __construct($shards)
    {
        //init all available connections
        $this->connectionsData = array_values($shards);
        $this->connections = array_fill(0, count($shards), null);
    }

    public function getConnection($key)
    {
        $shardKey = $this->getShardKey($key);
        return $this->getConnectionByShardKey($shardKey);
    }

    public function closeConnection($key)
    {
        $shardKey = $this->getShardKey($key);
        $this->connections[$shardKey] = null;
    }

    public function closeAllConnections()
    {
        foreach ($this->connections as $shardKey => $connection) {
            if ($connection !== null) {
                $this->connections[$shardKey] = null;
            }
        }
    }

    /**
     * @return \PDO[]
     */
    public function getAvailableConnections()
    {
        foreach ($this->connections as $shardKey => $connection) {
            $this->getConnectionByShardKey($shardKey);
        }
        return $this->connections;
    }

    private function getConnectionByShardKey($shardKey)
    {
        if ($this->connections[$shardKey] === null) {
            $this->connections[$shardKey] = new PDOReconnectProxy(
                $this->connectionsData[$shardKey]['dsn'],
                $this->connectionsData[$shardKey]['username'],
                $this->connectionsData[$shardKey]['password']
            );
            $this->connections[$shardKey]->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
        }
        return $this->connections[$shardKey];
    }

    private function getShardKey($key) {
        return 0;//currently we have single shard
    }
}
