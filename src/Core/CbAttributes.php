<?php


namespace Crocodic\CrudBooster\Core;


trait CbAttributes
{
    public $moduleName;
    public $table;
    public $primaryKey = 'id';
    public $titleColumn = 'id';

    private $gridColumns = [];
    private $formColumns = [];

    public $orderBy = null;
    public $limit = 20;
    public $softDelete = true;

    public $gridNumbering = false;
    public $buttonFilter = true;
    public $buttonExport = true;
    public $buttonImport = true;
    public $buttonShow = true;
    public $buttonAddMore = true;
    public $buttonGridAction = true;
    public $buttonBulk = true;
    public $buttonAdd = true;
    public $buttonDelete = true;
    public $buttonCancel = true;
    public $buttonSave = true;
    public $buttonEdit = true;
    public $buttonDetail = true;
    public $buttonGridActionStyle = 'button_icon';
    public $preGridHTML = null;
    public $postGridHTML = null;
    public $sidebarMode = 'normal';

    private $loadJs = [];
    private $loadCss = [];
    public $scriptJs = null;
    public $scriptCss = null;

    private $subModule = [];
    private $gridRowColor = [];

    public function _GetGridColumns() {
        return $this->gridColumns;
    }

    public function _GetFormColumns() {
        return $this->formColumns;
    }

    public function addColumn(array $gridColumn) {
        $this->gridColumns[] = $gridColumn;
    }

    public function addForm(array $formColumn) {
        $this->formColumns[] = $formColumn;
    }

}