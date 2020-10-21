<?php


namespace Crocodic\CrudBooster\Core;


class RuntimeCache
{
    private $data;

    public function put($key, $value) {
        $this->data[$key] = $value;
    }

    public function get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

}