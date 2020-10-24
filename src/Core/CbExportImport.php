<?php

namespace Crocodic\CrudBooster\Core;


use Crocodic\CrudBooster\Core\Export\DefaultExportXls;
use Crocodic\CrudBooster\Core\Helpers\CbQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;

trait CbExportImport
{

    public function postExportData()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(180);

        $fileType = request('file_type', 'pdf');

        $query = new CbQuery($this->table, $this->_GetGridColumns(), $this->primaryKey, $this->orderBy, $this->softDelete);
        $query->hookQuery(function(Builder $query) {
            return $this->hookQuery($query);
        });
        $execute = $query->execute();

        $data= [];
        $data['result'] = $execute->getResultData();
        $data['totalData'] = $execute->getTotalData();

        switch ($fileType) {
            case "pdf":
                $view = view('crudbooster::export', $data)->render();
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setPaper('Legal', 'Potrait');
                return $pdf->stream($this->moduleName.' - '.date('d-m-Y').'.pdf');
                break;
            case 'xls':
                return Excel::download(new DefaultExportXls($data),$this->moduleName.' - '.date('d-m-Y').".xls");
                break;
            case 'csv':
                return Excel::download(new DefaultExportXls($data),$this->moduleName.' - '.date('d-m-Y').".csv");
                break;
        }
    }

    public function getImportData()
    {
        return view('crudbooster::import', [
            'pageTitle'=> cb_lang('import_data')
        ]);
    }

}