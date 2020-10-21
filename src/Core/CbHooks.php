<?php


namespace Crocodic\CrudBooster\Core;


use Illuminate\Database\Query\Builder;

trait CbHooks
{
    public function hookQuery(Builder $query)
    {
        return $query;
    }

    public function hookPreAdd($postData)
    {
        return $postData;
    }

    public function hookPostAdd($lastInsertId)
    {
    }

    public function hookPreEdit($postData, $currentId)
    {
        return $postData;
    }

    public function hookPostEdit($currentId)
    {
    }

    public function hookPreDelete($currentId)
    {
    }

    public function hookPostDelete($currentId)
    {
    }

}