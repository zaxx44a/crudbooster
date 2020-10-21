<?php


namespace Crocodic\CrudBooster\Core;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

abstract class CbAbstract extends Controller
{
    use ValidatesRequests;

    public function cbInit() {}

}