<?php

/*Autoloader::map(array(
    'Querycache' => Bundle::path('querycache') . DS,
    'Base' => Bundle::path('querycache') . DS . "models/Base.php",
    'CacheQuery' => Bundle::path('querycache') . DS . "database/CacheQuery.php",
    'CacheConnection' => Bundle::path('querycache') . DS . "database/cacheconnection.php",
    'EloquentCache' =>  Bundle::path('querycache') . DS . "auth/driver/eloquentcache.php",
));*/

Autoloader::directories(array(
    Bundle::path('querycache').'Database' . DS . 'Eloquent',
));

Autoloader::namespaces(array(
    'Querycache'   => Bundle::path('querycache'),
));
