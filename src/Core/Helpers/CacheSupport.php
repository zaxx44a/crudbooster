<?php


namespace Crocodic\CrudBooster\Core\Helpers;


trait CacheSupport
{
    /**
     * @param $section
     * @param $cache_name
     * @param $cache_value
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public static function putCache($section, $cache_name, $cache_value, $ttl = 3600)
    {
        if (cache()->has($section)) {
            $cache_open = cache()->get($section);
        } else {
            cache()->put($section, [], $ttl);
            $cache_open = cache()->get($section);
        }
        $cache_open[$cache_name] = $cache_value;
        cache()->put($section, $cache_open, $ttl);

        return true;
    }

    /**
     * @param $section
     * @param $cache_name
     * @return mixed|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public static function getCache($section, $cache_name)
    {
        if (cache()->has($section)) {
            $cache_open = cache()->get($section);
            return $cache_open[$cache_name];
        } else {
            return null;
        }
    }

    /**
     * @param $section
     * @param $cache_name
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public static function forgetCache($section, $cache_name)
    {
        if (cache()->has($section)) {
            $open = cache()->get($section);
            unset($open[$cache_name]);
            cache()->forever($section, $open);

            return true;
        } else {
            return false;
        }
    }

}