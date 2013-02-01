<?php

namespace Querycache\Database;
use \Laravel\Database\Query as Query;

class CachedQuery extends Query {
    /**
     * The number of minutes to cache a query
     *
     * @var int
     */
    public $cache = 0;
    
    /**
     * Create a new query instance.
     *
     * @param  Connection  $connection
     * @param  Grammar     $grammar
     * @param  string      $table
     * @return void
     */
    public function __construct(Connection $connection, Query\Grammars\Grammar $grammar, $table)
    {
        $this->from = $table;
        $this->grammar = $grammar;
        $this->connection = new CachedConnection($connection);
    }
    
    public function __destruct() 
    {
        unset($this->connection);
    }
    
    /**
     * Allows the query to retrieve from cache if available
     *
     * @return Query
     */
    public function cache($time) 
    {
        $this->cache = $time;
        return $this;
    }
    
    /**
     * Execute the query as a SELECT statement.
     *
     * @param  array  $columns
     * @return array
     */
    public function get($columns = array('*'))
    {
        if (is_null($this->selects)) $this->select($columns);

        $sql = $this->grammar->select($this);

        $results = $this->connection->query($sql, $this->bindings, $this->cache);

        // If the query has an offset and we are using the SQL Server grammar,
        // we need to spin through the results and remove the "rownum" from
        // each of the objects since there is no "offset".
        if ($this->offset > 0 and $this->grammar instanceof SQLServer)
        {
            array_walk($results, function($result)
            {
                unset($result->rownum);
            });
        }

        // Reset the SELECT clause so more queries can be performed using
        // the same instance. This is helpful for getting aggregates and
        // then getting actual results from the query.
        $this->selects = null;

        return $results;
    }
}
