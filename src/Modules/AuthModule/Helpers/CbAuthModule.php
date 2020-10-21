<?php


namespace Crocodic\CrudBooster\Modules\AuthModule\Helpers;

class CbAuthModule
{
    // Todo : cb role filtering code

    public static function canBrowse() {
        return true;
    }

    public static function canCreate() {
        return true;
    }

    public static function canRead() {
        return true;
    }

    public static function canUpdate() {
        return true;
    }

    public static function canDelete() {
        return true;
    }

}