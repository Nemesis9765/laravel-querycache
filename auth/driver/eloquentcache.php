<?php

namespace Querycache\Auth\Drivers;
use \Laravel\Auth\Drivers\Eloquent as Eloquent;

class EloquentCache extends Eloquent {
    /**
     * Get the current user of the application.
     *
     * If the user is a guest, null should be returned.
     *
     * @param  int|object  $token
     * @return mixed|null
     */
    public function retrieve($token)
    {
        // We return an object here either if the passed token is an integer (ID)
        // or if we are passed a model object of the correct type
        if (filter_var($token, FILTER_VALIDATE_INT) !== false)
        {
            return $this->model()->cache(Config::get('auth.cacheUser'))->find($token);
        }
        else if (is_object($token) and get_class($token) == Config::get('auth.model'))
        {
            return $token;
        }
    }
    
}
