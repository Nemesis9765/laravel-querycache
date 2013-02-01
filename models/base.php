<?php

namespace Querycache\Models;
use \Querycache\Database\CachedQuery as CachedQuery;
use \Laravel\Database\Eloquent\Model as Eloquent;

class Base extends Eloquent {

    public static $connection = 'cacheConnection'; // Something goes here just not sure what

    protected function query() {
        $val = new CachedQuery( $this );
        return $val->query();
    }
}