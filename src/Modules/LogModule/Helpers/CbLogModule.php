<?php


namespace Crocodic\CrudBooster\Modules\LogModule\Helpers;


use Crocodic\CrudBooster\Core\CbAbstract;

class CbLogModule
{
    private static function log($description, $flag) {
        db("cb_logs")->insert([
            "created_at"=> now(),
            "ip_address"=> request()->ip(),
            "user_agent"=> request()->userAgent(),
            "description"=> $description,
            "flag"=> $flag
        ]);
    }
    public static function logSuccess($description) {
        static::log($description, "SUCCESS");
    }

    public static function logWarning($description) {
        static::log($description, "WARNING");
    }

    public static function logDanger($description) {
        static::log($description, "DANGER");
    }

    public static function logFailed($description) {
        static::log($description, "FAILED");
    }
}