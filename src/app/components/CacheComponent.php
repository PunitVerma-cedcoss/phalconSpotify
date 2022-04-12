<?php

namespace App\Components;

use Phalcon\Di\Injectable;
use Phalcon\Cache;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\Adapter\Redis;

use Phalcon\Cache\Adapter\Stream;

/**
 * helper class to initlize cache
 */

class CacheComponent extends Injectable
{
    /**
     * returns a cache object
     *
     * @return Object
     */
    public function initCache()
    {
        $serializerFactory = new SerializerFactory();
        $options = [
            'defaultSerializer' => 'Php',
            'lifetime'          => 7200,
            'storageDir'        => BASE_PATH . '/storage/cache',
        ];

        $adapter = new Stream($serializerFactory, $options);
        $cache = new Cache($adapter);
        return $cache;
    }
}
