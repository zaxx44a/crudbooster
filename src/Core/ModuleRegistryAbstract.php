<?php


namespace Crocodic\CrudBooster\Core;


abstract class ModuleRegistryAbstract
{

    public function getMenuName() {
        return null;
    }

    public function getIcon() {
        return null;
    }

    public function getPath() {
        return null;
    }

    /**
     * @param CBController $cbController
     * @param array $rules
     * @return array
     */
    public function hookValidation(CBController $cbController, array $rules): array
    {
        return $rules;
    }

    public function hookDataCrudAssign(CBController $cbController, array $data): array
    {
        return $data;
    }

    public function hookPreBrowse(CBController $cbController) {}

    public function hookPostBrowse(CBController $cbController) {}

    public function hookPreCreate(CBController $cbController, $postData) {}

    public function hookPostCreate(CBController $cbController, $postData, $currentId) {}

    public function hookPreRead(CBController $cbController, $currentId) {}

    public function hookPostRead(CBController $cbController, $currentId) {}

    public function hookPreUpdate(CBController $cbController, $postData, $currentId) {}

    public function hookPostUpdate(CBController $cbController, $currentId) {}

    public function hookPreDelete(CBController $cbController, $currentId) {}

    public function hookPostDelete(CBController $cbController, $currentId) {}
}