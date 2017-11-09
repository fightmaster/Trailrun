<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace TPP\Common\Database\SQL;

class SingleConnectionPool
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var \PDO
     */
    private $connection;

    public function __construct($options)
    {
        $this->options = $options;
        $this->connection = null;
    }

    public function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = new \PDO(
                $this->options['dsn'],
                $this->options['username'],
                $this->options['password']
            );
            $this->connection->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
        }

        return $this->connection;
    }

    public function closeConnection()
    {
        $this->connection = null;
    }
}
