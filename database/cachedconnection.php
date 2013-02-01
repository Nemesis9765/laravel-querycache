<?php
namespace Querycache\Database;
use \Laravel\Database\Connection as Connection;

class CachedConnection extends Connection {
    public function __construct(Connection $connection) {
        $this->pdo = $connection->pdo;
        $this->config = $connection->config;
    }
    
    /**
     * Execute a SQL query and return an array of StdClass objects.
     *
     * @param  string  $sql
     * @param  array   $bindings
     * @return array
     */
    public function query($sql, $bindings = array(), $cache)
    {
        $sql = trim($sql);

        if (!$cache || ($cache && stripos($sql, 'select') === FALSE && stripos($sql, 'show') === FALSE)) {
            list($statement, $result) = $this->execute($sql, $bindings);
        }

        // The result we return depends on the type of query executed against the
        // database. On SELECT clauses, we will return the result set, for update
        // and deletes we will return the affected row count.
        if (stripos($sql, 'select') === 0 || stripos($sql, 'show') === 0)
        {
            if ($cache) {
                $sqlQ = $sql;
                
                foreach ($bindings as $bind) {
                    if (($pos = strpos($sqlQ, '?')) !== false) {
                        $sqlQ = substr_replace($sqlQ, $bind, $pos, 1);
                    }
                }
                
                $hash = "db_" . crc32($sqlQ);
                if (\Cache::has($hash)) {
                    return \Cache::get($hash);
                } else {
                    list($statement, $result) = $this->execute($sql, $bindings);
                    $data = $this->fetch($statement, Config::get('database.fetch'));
                    \Cache::put($hash, $data, $cache);
                    return $data;
                }
            }
            return $this->fetch($statement, Config::get('database.fetch'));
        }
        elseif (stripos($sql, 'update') === 0 or stripos($sql, 'delete') === 0)
        {
            return $statement->rowCount();
        }
        // For insert statements that use the "returning" clause, which is allowed
        // by database systems such as Postgres, we need to actually return the
        // real query result so the consumer can get the ID.
        elseif (stripos($sql, 'insert') === 0 and stripos($sql, 'returning') !== false)
        {
            return $this->fetch($statement, Config::get('database.fetch'));
        }
        else
        {
            return $result;
        }
    }
}
