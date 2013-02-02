<?php
namespace Querycache\Database\Eloquent;

class CachedQuery extends \Laravel\Database\Eloquent\Query {
    public $cache = 0;
    
    /**
     * Allows the query to retrieve from cache if available
     *
     * @return Query
     */
    public function cache($time) {
        $this->cache = $time;
        return $this->model;
    }
    
    public function get($columns = array('*')) {
        $sql = $this->grammar->select($columns);

        if ($this->cache && (stripos($sql, 'select') === 0 || stripos($sql, 'show') === 0)) {
            foreach ($this->bindings as $bind) {
                if (($pos = strpos($sql, '?')) !== false) {
                    $sqlQ = substr_replace($sql, $bind, $pos, 1);
                }
            }
            
            $hash = "db_" . crc32($sql);
            if (\Cache::has($hash)) {
                return \Cache::get($hash);
            } else {
                $data = parent::get($columns);
                \Cache::put($hash, $data, $this->cache);
                return $data;
            }
        }
        
        return parent::get();
    }
    
}
