<?php


namespace Crocodic\CrudBooster\Modules\RoleModule;


use Crocodic\CrudBooster\Core\CBController;
use Crocodic\CrudBooster\Core\ModuleRegistryAbstract;
use Crocodic\CrudBooster\Modules\LogModule\Helpers\CbLogModule;
use Crocodic\CrudBooster\Modules\RoleModule\Helpers\CbRoleModule;

class CbRoleModuleRegistry extends ModuleRegistryAbstract
{

    public function getMenuName()
    {
        return "Role Management";
    }

    public function getIcon()
    {
        return "fa fa-bars";
    }

    public function getPath()
    {
        return admin_path("role");
    }

    public function hookPreBrowse(CBController $cbController)
    {
        if(!CbRoleModule::canBrowse()) {
            CbLogModule::logWarning(cb_lang('log_try_browse',['module'=>$cbController->moduleName]));
            force_view("CbLogModule::deny");
        }
    }

    public function hookPreCreate(CBController $cbController, $postData)
    {
        if(!CbRoleModule::canCreate()) {
            CbLogModule::logWarning(cb_lang('log_try_create',['module'=>$cbController->moduleName]));
            force_view("CbLogModule::deny");
        }
    }

    public function hookPreRead(CBController $cbController, $currentId)
    {
        if(!CbRoleModule::canRead()) {
            CbLogModule::logWarning(cb_lang('log_try_read',['module'=>$cbController->moduleName]));
            force_view("CbLogModule::deny");
        }
    }

    public function hookPreUpdate(CBController $cbController, $postData, $currentId)
    {
        if(!CbRoleModule::canUpdate()) {
            CbLogModule::logWarning(cb_lang('log_try_update',['module'=>$cbController->moduleName]));
            force_view("CbLogModule::deny");
        }
    }

    public function hookPreDelete(CBController $cbController, $currentId)
    {
        if(!CbRoleModule::canDelete()) {
            CbLogModule::logWarning(cb_lang('log_try_delete',['module'=>$cbController->moduleName]));
            force_view("CbLogModule::deny");
        }
    }
}