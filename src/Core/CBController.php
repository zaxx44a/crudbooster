<?php

namespace Crocodic\CrudBooster\Core;

use Crocodic\CrudBooster\Core\Helpers\CB;
use Crocodic\CrudBooster\Core\Helpers\CbQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CBController extends CbAbstract
{
    use CbAttributes, CbHooks, CbExportImport, ControllerSupport, ValidatesRequests;

    private $registryModules;

    public function __construct()
    {
        $this->middleware(function($request, $next) {
            $this->genericLoader();
            return $next($request);
        });
        $this->registryModules = app("CbModuleRegistry")->getModules();
    }

    public function genericLoader()
    {
        $this->cbInit();
        $data = [];
        $data['table'] = $this->table;
        $data['moduleName'] = $this->moduleName;
        $data['gridNumbering'] = $this->gridNumbering;
        $data['buttonDetail'] = $this->buttonDetail;
        $data['buttonEdit'] = $this->buttonEdit;
        $data['buttonShow'] = $this->buttonShow;
        $data['buttonAdd'] = $this->buttonAdd;
        $data['buttonDelete'] = $this->buttonDelete;
        $data['buttonFilter'] = $this->buttonFilter;
        $data['buttonExport'] = $this->buttonExport;
        $data['buttonAddMore'] = $this->buttonAddMore;
        $data['buttonCancel'] = $this->buttonCancel;
        $data['buttonSave'] = $this->buttonSave;
        $data['buttonGridAction'] = $this->buttonGridAction;
        $data['buttonBulk'] = $this->buttonBulk;
        $data['buttonImport'] = $this->buttonImport;
        $data['preGridHTML'] = $this->preGridHTML;
        $data['postGridHTML'] = $this->postGridHTML;
        $data['loadJs'] = $this->loadJs;
        $data['loadCss'] = $this->loadCss;
        $data['scriptJs'] = $this->scriptJs;
        $data['scriptCSs'] = $this->scriptCss;
        view()->share($data);
    }

    /**
     * @throws ValidationException
     */
    public function validation()
    {
        $rules = [];
        foreach($this->gridColumns as $gridColumn) {
            if(isset($gridColumn['validation'])) {
                $rules[$gridColumn['column']] = $gridColumn['validation'];
            }
            foreach($this->registryModules as $moduleRegistry) {
                /** @var ModuleRegistryAbstract $moduleRegistry */
                $rules = $moduleRegistry->hookValidation($this, $rules);
            }
        }

        $this->validate(request(),$rules);
    }

    public function dataAssign()
    {
        $data = [];
        foreach ($this->gridColumns as $gridColumn) {
            if (! isset($gridColumn['column'])) {
                continue;
            }
            $data[ $gridColumn['column'] ] = request($gridColumn['column']);

            foreach($this->registryModules as $moduleRegistry) {
                /** @var ModuleRegistryAbstract $moduleRegistry */
                $data = $moduleRegistry->hookDataCrudAssign($this, $data);
            }
        }

        return $data;
    }

    public function getIndex()
    {
        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPreBrowse($this);
        }

        $data = [];
        $data['pageTitle'] = $this->moduleName;

        $query = new CbQuery($this->table, $this->_GetGridColumns(), $this->primaryKey, $this->orderBy, $this->softDelete);
        $query->hookQuery(function(Builder $query) {
            return $this->hookQuery($query);
        });
        $exec = $query->execute();
        $data['gridColumns'] = $exec->getGridColumns();
        $data['resultData'] = $exec->getResultData();
        $data['totalData'] = $exec->getTotalData();


        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPostBrowse($this);
        }

        return view("crudbooster::crud.index", $data);
    }

    public function getAdd()
    {
        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPreCreate($this, []);
        }

        return view('crudbooster::crud.form');
    }

    public function postAddSave()
    {
        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPreCreate($this, request()->all());
        }

        try {
            $this->validation();

            $data = $this->dataAssign();

            if (CB::hasColumn($this->table, 'created_at')) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            $data = $this->hookPreAdd($data);

            $lastInsertId = DB::table($this->table)->insertGetId($data);
            $lastInsertId = (isset($data[$this->primaryKey]))?$data[$this->primaryKey] : $lastInsertId;

            $this->hookPostAdd($lastInsertId);

            foreach($this->registryModules as $moduleRegistry) {
                /** @var ModuleRegistryAbstract $moduleRegistry */
                $moduleRegistry->hookPostCreate($this, $data, $lastInsertId);
            }

            if (request('return_url')) {
                if (request('submit') == cb_lang('button_save_more')) {
                    return CB::back(cb_lang("data_has_been_created"),'success');
                } else {
                    return CB::redirect(request('return_url'),cb_lang("data_has_been_created"),'success');
                }
            } else {
                if (request('submit') == cb_lang('button_save_more')) {
                    return CB::back(cb_lang("data_has_been_created"),'success');
                } else {
                    return CB::redirect(action(static::class.'@getIndex'),cb_lang("data_has_been_created"),'success');
                }
            }
        } catch (ValidationException $e) {
            return CB::back($e->getMessage());
        } catch (\Exception $e) {
            return CB::back($e->getMessage(), "danger");
        }
    }

    public function getEdit($id)
    {
        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPreUpdate($this, request()->all(), $id);
        }

        $row = DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->first();

        return view('crudbooster::crud.form', [
            'pageTitle'=> $this->moduleName.': '.cb_lang('create'),
            'row'=> $row
        ]);
    }

    public function postEditSave($id)
    {
        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPreUpdate($this, request()->all(), $id);
        }

        try {
            $this->validation();
            $data = $this->dataAssign();

            if (CB::hasColumn($this->table, 'updated_at')) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            $data = $this->hookPreEdit($data, $id);

            DB::table($this->table)->where($this->primaryKey, $id)->update($data);

            $this->hookPostEdit($id);

            foreach($this->registryModules as $moduleRegistry) {
                /** @var ModuleRegistryAbstract $moduleRegistry */
                $moduleRegistry->hookPostUpdate($this, $id);
            }

            if (request('return_url')) {
                return CB::redirect(request('return_url'),cb_lang("data_has_been_updated"),'success');
            } else {
                return CB::redirect(action(static::class.'@getIndex'),cb_lang("data_has_been_updated"),'success');
            }

        } catch (ValidationException $e) {
            return CB::back($e->getMessage());
        } catch (\Exception $e) {
            return CB::back($e->getMessage(), "danger");
        }
    }

    public function getDelete($id)
    {
        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPreDelete($this, $id);
        }

        $row = DB::table($this->table)->where($this->primaryKey, $id)->first();

        $this->hookPreDelete($id);

        if ($this->softDelete) {
            DB::table($this->table)->where($this->primaryKey, $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            DB::table($this->table)->where($this->primaryKey, $id)->delete();
        }

        $this->hookPostDelete($id);

        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPostDelete($this, $id);
        }

        if(request('return_url')) {
            return CB::redirect(request('return_url'),cb_lang('data_has_been_deleted'),'success');
        } else {
            return CB::back(cb_lang('data_has_been_deleted'),'success');
        }
    }

    public function getDetail($id)
    {
        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPreRead($this, $id);
        }

        $row = DB::table($this->table)->where($this->primaryKey,$id)->first();

        foreach($this->registryModules as $moduleRegistry) {
            /** @var ModuleRegistryAbstract $moduleRegistry */
            $moduleRegistry->hookPostRead($this, $id);
        }

        return view('crudbooster::crud.detail', [
           'pageTitle'=> $this->moduleName.': '. cb_lang('detail').' - '. $row->{$this->titleColumn}
        ]);
    }
}
