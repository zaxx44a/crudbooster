<?php


namespace Crocodic\CrudBooster\Core\Export;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DefaultExportXls implements FromView
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view("crudbooster::export.export_xls",$this->data);
    }
}