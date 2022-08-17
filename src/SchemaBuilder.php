<?php

namespace GraphClass;

use GraphClass\Config\ConfigCache;
use GraphClass\Config\Exception\ConfigException;
use GraphClass\Config\Exception\IOException;

final class SchemaBuilder {
    /**
     * @throws ConfigException
     */
    public static function build(SchemaOptions $options): Schema{
        $cache = new ConfigCache($options);
        return new Schema($cache->config);
    }

    /**
     * @throws ConfigException
     * @throws IOException
     */
    public static function cacheAndBuild(SchemaOptions $options): Schema{
        $cache = new ConfigCache($options);
        $cache->persist();
        return new Schema($cache->config);
    }

    /**
     * @throws ConfigException
     * @throws IOException
     */
    public static function createCache(SchemaOptions $options): void{
        $cache = new ConfigCache($options);
        $cache->persist();
    }
}









