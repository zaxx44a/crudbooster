<?php


namespace Crocodic\CrudBooster\Modules\LogModule;


use Crocodic\CrudBooster\Core\CBController;
use Crocodic\CrudBooster\Core\Helpers\CB;
use Crocodic\CrudBooster\Core\ModuleRegistryAbstract;
use Crocodic\CrudBooster\Modules\LogModule\Helpers\CbLogModule;

class CbLogModuleRegistry extends ModuleRegistryAbstract
{

    public function getMenuName()
    {
        return "Log History";
    }

    public function getIcon()
    {
        return "fa fa-bars";
    }

    public function getPath()
    {
        return admin_path("log");
    }

    public function hookPostCreate(CBController $cbController, $postData, $currentId)
    {
        CbLogModule::logSuccess(cb_lang("log_create_data",['name'=>$postData[$cbController->titleColumn],'module'=>$cbController->moduleName]));
    }

    public function hookPostUpdate(CBController $cbController, $currentId)
    {
        CbLogModule::logSuccess(cb_lang("log_update_data",['name'=>request($cbController->titleColumn),'module'=>$cbController->moduleName]));
    }

    public function hookPostDelete(CBController $cbController, $currentId)
    {
        $row = CB::first($cbController->table, $currentId);
        CbLogModule::logSuccess(cb_lang("log_delete_data",['name'=>$row->{$cbController->titleColumn},'module'=>$cbController->moduleName]));
    }
}