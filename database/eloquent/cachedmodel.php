<?php
namespace Querycache\Database\Eloquent;
use \Laravel\Database\Eloquent\Model as Eloquent;
use Querycache\Database\Eloquent\CachedQuery as CachedQuery;


class CachedModel extends Eloquent {    
    protected function _query() 
    {
        return new CachedQuery($this);
    }
} 