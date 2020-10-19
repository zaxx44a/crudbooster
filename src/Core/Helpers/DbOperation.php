<?php


namespace Crocodic\CrudBooster\Core\Helpers;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait DbOperation
{
    /**
     * @param $table
     * @return mixed|null
     */
    public static function findPrimaryKey($table)
    {
        $pk = DB::getDoctrineSchemaManager()->listTableDetails($table)->getPrimaryKey();
        if(!$pk) {
            return null;
        }
        return $pk->getColumns()[0];
    }

    public static function insert($table, $data = [])
    {
        $data['id'] = DB::table($table)->max('id') + 1;
        if (! $data['created_at']) {
            if (Schema::hasColumn($table, 'created_at')) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
        }

        if (DB::table($table)->insert($data)) {
            return $data['id'];
        } else {
            return false;
        }
    }

    public static function first($table, $id)
    {
        $table = self::parseSqlTable($table)['table'];
        if (is_array($id)) {
            $first = DB::table($table);
            foreach ($id as $k => $v) {
                $first->where($k, $v);
            }

            return $first->first();
        } else {
            $pk = self::pk($table);

            return DB::table($table)->where($pk, $id)->first();
        }
    }


    public static function isColumnNULL($table, $field)
    {
        if (Cache::has('field_isNull_'.$table.'_'.$field)) {
            return Cache::get('field_isNull_'.$table.'_'.$field);
        }

        try {
            //MySQL & SQL Server
            $isNULL = DB::select(DB::raw("select IS_NULLABLE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table' and COLUMN_NAME = '$field'"))[0]->IS_NULLABLE;
            $isNULL = ($isNULL == 'YES') ? true : false;
            Cache::forever('field_isNull_'.$table.'_'.$field, $isNULL);
        } catch (\Exception $e) {
            $isNULL = false;
            Cache::forever('field_isNull_'.$table.'_'.$field, $isNULL);
        }

        return $isNULL;
    }

    /**
     * @param $table
     * @param $column
     * @return string
     */
    public static function getColumnDataType($table, $column)
    {
        try {
            //MySQL & SQL Server
            $type = DB::select(DB::raw("select DATA_TYPE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table' and COLUMN_NAME = '$column'"))[0]->DATA_TYPE;
        } catch (\Exception $e) {
            $type = 'varchar';
        }

        return $type;
    }


    public static function getForeignKey($parentTable, $childTable)
    {
        if (Schema::hasColumn($childTable, 'id_'.$parentTable)) {
            return 'id_'.$parentTable;
        } else {
            return $parentTable.'_id';
        }
    }

    public static function getTableForeignKey($column)
    {
        $table = null;
        if (substr($column, 0, 3) == 'id_') {
            $table = substr($column, 3);
        } elseif (substr($column, -3) == '_id') {
            $table = substr($column, 0, (strlen($column) - 3));
        }

        return $table;
    }

    /**
     * @param $column
     * @return bool|\Illuminate\Contracts\Cache\Repository|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public static function isForeignKey($column)
    {
        if (substr($column, 0, 3) == 'id_') {
            $table = substr($column, 3);
        } elseif (substr($column, -3) == '_id') {
            $table = substr($column, 0, (strlen($column) - 3));
        } else {
            $table = null;
        }

        if (cache()->has('isForeignKey_'.$column)) {
            return cache()->get('isForeignKey_'.$column);
        } else {
            if ($table) {
                $hasTable = Schema::hasTable($table);
                if ($hasTable) {
                    cache()->forever('isForeignKey_'.$column, true);

                    return true;
                } else {
                    cache()->forever('isForeignKey_'.$column, false);

                    return false;
                }
            } else {
                return false;
            }
        }
    }

}