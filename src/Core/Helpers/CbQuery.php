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
    private $cb;

    private $resultData;
    private $totalData;

    public function __construct(CBController $cb) {
        $this->table = $cb->table;
        $this->gridColumns = $cb->_GetGridColumns();
        $this->primaryKey = $cb->primaryKey;
        $this->orderBy = $cb->orderBy;
        $this->softDelete = $cb->softDelete;
        $this->cb = $cb;
    }

    public function execute(): void
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
        $this->query = $this->cb->hookQuery($this->query);

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

        $this->resultData = $this->query->get();
        $this->totalData = $queryTotal->count();
    }

    /**
     * @return int
     */
    public function getTotalData(): int
    {
        return $this->totalData;
    }

    /**
     * @return Collection
     */
    public function getResultData(): Collection
    {
        return $this->resultData;
    }

    /**
     * @return array
     */
    public function getGridColumns() {
        return $this->gridColumns;
    }
}