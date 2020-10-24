<?php

namespace Crocodic\CrudBooster\Core\Helpers;

use Crocodic\CrudBooster\Core\CbAbstract;
use Crocodic\CrudBooster\Core\CBController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CbQueryExecute
{

    private $totalData;
    private $gridColumns;
    private $resultData;

    /**
     * @return mixed
     */
    public function getTotalData()
    {
        return $this->totalData;
    }

    /**
     * @param mixed $totalData
     */
    public function setTotalData($totalData): void
    {
        $this->totalData = $totalData;
    }

    /**
     * @return mixed
     */
    public function getGridColumns()
    {
        return $this->gridColumns;
    }

    /**
     * @param mixed $gridColumns
     */
    public function setGridColumns($gridColumns): void
    {
        $this->gridColumns = $gridColumns;
    }

    /**
     * @return mixed
     */
    public function getResultData()
    {
        return $this->resultData;
    }

    /**
     * @param mixed $resultData
     */
    public function setResultData($resultData): void
    {
        $this->resultData = $resultData;
    }


}