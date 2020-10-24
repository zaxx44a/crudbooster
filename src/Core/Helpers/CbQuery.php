<?php

namespace Crocodic\CrudBooster\Core\Helpers;

use Crocodic\CrudBooster\Core\CbAbstract;
use Crocodic\CrudBooster\Core\CBController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CbQuery
{

    private $table;
    private $primaryKey;
    private $query;
    private $softDelete = true;
    private $gridColumns;
    private $orderBy;
    private $hookQueryCallback;

    public function __construct(string $table, array $gridColumns, string $primaryKey, array $orderBy, bool $softDelete) {
        $this->table = $table;
        $this->gridColumns = $gridColumns;
        $this->primaryKey = $primaryKey;
        $this->orderBy = $orderBy;
        $this->softDelete = $softDelete;
    }

    /**
     * @param callable $callback
     */
    public function hookQuery(callable $callback) {
        $this->hookQueryCallback = $callback;
    }

    /**
     * @return CbQueryExecute
     * @throws \Exception
     */
    public function execute(): CbQueryExecute
    {
        $displayColumnArray = [];

        $this->query = DB::table($this->table);
        if($this->softDelete===true) $this->query->whereNull("deleted_at");

        // Add all columns from main table
        $tableColumns = CB::getTableColumns($this->table);
        foreach($tableColumns as $column) {
            $this->query->addSelect($this->table.'.'.$column.' as '.$this->table.'_'.$column);
            $displayColumnArray[$this->table.'.'.$column] = $this->table.'_'.$column;
            $displayColumnArray[$column] = $this->table.'_'.$column;
        }

        $joinNumber = 0;
        foreach($this->gridColumns as $i => $gridColumn) {
            /**
             * Example $gridColumn
             * [
             *  'label'=> 'Example',
             *  'column'=> 'column1',
             *  'join'=> 'table2',
             *  'join'=> ['foo','bar','bar.id','=','table1.id'],
             *  'displayColumn'=> 'bar_name'
             * ]
             */

            if(isset($gridColumn['join'])) {
                if(is_array($gridColumn['join'])) {
                    /**
                     * $joinRaw = ['foo','bar','bar.id','=','master.id]
                     * --------------^------^------^-----^------^------
                     *              table  alias  first  op   second
                     */
                    $join = $gridColumn['join'];
                    $this->gridColumns[$i]['joinAlias'] = $join[1];
                    $this->query->join(DB::raw($join[0].' as '.$join[1]),
                        $join[2],$join[3],$join[4]);
                    // Collect all columns from join's table
                    $joinColumns = CB::getTableColumns($join[0]);
                    foreach($joinColumns as $joinColumn) {
                        $this->query->addSelect($join[1].'.'.$joinColumn.' as '.$join[1].'_'.$joinColumn);
                        $displayColumnArray[$join[1].'.'.$joinColumn] = $join[1].'_'.$joinColumn;
                    }
                } else {
                    $joinAlias = $gridColumn['join'].$joinNumber;
                    $this->gridColumns[$i]['joinAlias'] = $joinAlias;
                    $joinPrimaryKey = CB::findPrimaryKey($gridColumn['join']);
                    $this->query->join($gridColumn['join']." as ".$joinAlias,$joinAlias.'.'.$joinPrimaryKey,'=',$this->table.'.'.$this->primaryKey);
                    // Collect all columns from join's table
                    $joinColumns = CB::getTableColumns($gridColumn['join']);
                    foreach($joinColumns as $joinColumn) {
                        $this->query->addSelect($joinAlias.'.'.$joinColumn.' as '.$joinAlias.'_'.$joinColumn);
                        $displayColumnArray[$joinAlias.'.'.$joinColumn] = $joinAlias.'_'.$joinColumn;
                    }
                }

                $joinNumber++;
            }

            if(!isset($gridColumn['displayColumn'])) {
                $this->gridColumns[$i]['displayColumn'] = $gridColumn['column'];
            }

            // Override display column to convert from SQL dot syntax to SQL alias syntax
            $this->gridColumns[$i]['displayColumn'] = $displayColumnArray[$gridColumn['displayColumn']];
        }

        // CONDITION SECTION
        $this->query = call_user_func($this->hookQueryCallback, $this->query);

        // PREPARE QUERY FOR TOTAL
        $queryTotal = clone $this->query;

        // ORDER BY SECTION
        if(request('orderByCol') && request('orderByDir')) {
            $this->query->orderBy($displayColumnArray[request('orderByCol')], request('orderByDir')=='desc'?'desc':'asc');
        } else {
            /**
             * Example orderBy = ['column','asc']
             * ---------------------^-------^-----
             *                    Column    Direction
             */
            $this->query->orderBy($this->orderBy[0], $this->orderBy[1]);
        }

        // LIMIT
        if($limit = filter_var(request('limit'),FILTER_SANITIZE_NUMBER_INT)) {
            $this->query->take($limit);
        }

        // OFFSET
        if($offset = filter_var(request('offset'), FILTER_SANITIZE_NUMBER_INT)) {
            $this->query->skip($offset);
        }

        $cbQueryExecute = new CbQueryExecute();
        $cbQueryExecute->setGridColumns($this->gridColumns);
        $cbQueryExecute->setTotalData($queryTotal->count());
        $cbQueryExecute->setResultData($this->query->get());
        return $cbQueryExecute;
    }
}