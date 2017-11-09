<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Database;

class PDOReconnectProxy extends \PDO
{
    const ERROR_SERVER_GONE_AWAY = 2006;

    /**
     * @var \PDO
     */
    private $connection;

    private $dsn;
    private $username;
    private $password;
    private $options;

    /**
     * @var int
     */
    private $retry;

    public function __construct($dsn, $username, $password, $options = [], $retry = 1)
    {
        $this->retry = max(1, $retry);
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;

        if (!empty($options[\PDO::ATTR_PERSISTENT])) {
            throw new \Exception(__CLASS__ . " can't be used for persistent connections.");
        }

        $this->provideNewConnection();
    }

    private function provideNewConnection()
    {
        $this->connection = new \PDO($this->dsn, $this->username, $this->password, $this->options);

        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    public function prepare($statement, array $driverOptions = [])
    {
        return $this->connection->prepare($statement, $driverOptions);
    }

    public function beginTransaction()
    {
        //connection can be refreshed only in the beginning of the transaction
        return $this->tryToExecute(__FUNCTION__);
    }

    public function commit()
    {
        return $this->connection->commit();
    }

    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }

    public function setAttribute($attribute, $value)
    {
        return $this->connection->setAttribute($attribute, $value);
    }

    public function exec($statement)
    {
        return $this->connection->exec($statement);
    }

    public function query($statement, $mode = \PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
    {
        return $this->connection->query($statement);
    }

    public function lastInsertId($name = null)
    {
        return $this->connection->lastInsertId($name);
    }

    public function errorCode()
    {
        return $this->connection->errorCode();
    }

    public function errorInfo()
    {
        return $this->connection->errorInfo();
    }

    public function getAttribute($attribute)
    {
        return $this->connection->getAttribute($attribute);
    }

    public function quote($string, $parameterType = parent::PARAM_STR)
    {
        return $this->connection->quote($string, $parameterType);
    }

    private function tryToExecute($method, $parameters = [])
    {
        $counter = 0;
        //there is no other way to suppress WARNING.
        $result = @call_user_func_array([$this->connection, $method], $parameters);
        if (!$this->isServerGoneAway()) {
            return $result;
        }
        while ($counter < $this->retry) {
            $this->provideNewConnection();
            $counter++;
            $result = @call_user_func_array([$this->connection, $method], $parameters);
            if (!$this->isServerGoneAway()) {
                return $result;
            }
        }
        return $result;
    }

    private function isServerGoneAway()
    {
        $errorInfo = $this->connection->errorInfo();
        return !empty($errorInfo[1]) && $errorInfo[1] == self::ERROR_SERVER_GONE_AWAY;
    }

}
