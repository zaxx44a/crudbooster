<?php


namespace Crocodic\CrudBooster\Core;


class ModuleRegistry
{
    private $superAdminMenu = [];
    private $modules = [];

    public function registerModule(ModuleRegistryAbstract $class) {
        $this->modules[] = $class;
        $this->superAdminMenu[] = [
          'name'=> $class->getMenuName(),
          'icon'=> $class->getIcon(),
          'path'=> $class->getPath()
        ];
    }

    /**
     * @return array
     */
    public function getSuperAdminMenu() {
        return $this->superAdminMenu;
    }

    /**
     * @return ModuleRegistryAbstract[]
     */
    public function getModules() {
        return $this->modules;
    }
}