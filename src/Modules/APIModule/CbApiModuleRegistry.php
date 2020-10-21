<?php


namespace Crocodic\CrudBooster\Modules\APIModule;


use Crocodic\CrudBooster\Core\CBController;
use Crocodic\CrudBooster\Core\ModuleRegistryAbstract;

class CbApiModuleRegistry extends ModuleRegistryAbstract
{

    public function getMenuName()
    {
        return "API Generator";
    }

    public function getIcon()
    {
        return "fa fa-bars";
    }

    public function getPath()
    {
        return admin_path("api");
    }
}