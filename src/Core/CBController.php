<?php

namespace Crocodic\CrudBooster\Core;

use Crocodic\CrudBooster\Core\Helpers\CB;
use Crocodic\CrudBooster\Core\Helpers\CbQuery;
use Crocodic\CrudBooster\Modules\LogModule\Helpers\CbLogModule;
use Crocodic\CrudBooster\Modules\RoleModule\Helpers\CbRoleModule;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CBController extends CbAbstract
{
    use CbAttributes, CbHooks, CbExportImport, ControllerSupport, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function($request, $next) {
            $this->genericLoader();
            return $next($request);
        });
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
        }

        $this->validate(request(),$rules);
    }

    public function dataAssign($id = null)
    {
        $data = [];
        foreach ($this->gridColumns as $gridColumn) {
            if (! isset($gridColumn['column'])) {
                continue;
            }
            $data[ $gridColumn['column'] ] = request($gridColumn['column']);
        }

        return $data;
    }

    public function getIndex()
    {
        if(!CbRoleModule::canBrowse()) {
            CbLogModule::logWarning(cb_lang('log_try_browse',['module'=>$this->moduleName]));
            return view("CbLogModule::deny");
        }

        $data = [];
        $data['pageTitle'] = $this->moduleName;

        $query = new CbQuery($this);
        $query->execute();
        $data['gridColumns'] = $query->getGridColumns();
        $data['resultData'] = $query->getResultData();
        $data['totalData'] = $query->getTotalData();

        return view("crudbooster::crud.index", $data);
    }

    public function getAdd()
    {
        if(!CbRoleModule::canCreate()) {
            CbLogModule::logWarning(cb_lang('log_try_create',['module'=>$this->moduleName]));
            return view("CbLogModule::deny");
        }

        return view('crudbooster::crud.form');
    }

    public function postAddSave()
    {
        if(!CbRoleModule::canCreate()) {
            CbLogModule::logWarning(cb_lang('log_try_create',['module'=>$this->moduleName]));
            return view("CbLogModule::deny");
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

            CbLogModule::logSuccess(cb_lang("log_create_data",['name'=>$data[$this->titleColumn],'module'=>$this->moduleName]));

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
        if(!CbRoleModule::canUpdate()) {
            CbLogModule::logWarning(cb_lang('log_try_update',['module'=>$this->moduleName]));
            return view("CbLogModule::deny");
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
        if(!CbRoleModule::canUpdate()) {
            CbLogModule::logWarning(cb_lang('log_try_update',['module'=>$this->moduleName]));
            return view("CbLogModule::deny");
        }

        try {
            $this->validation();
            $data = $this->dataAssign($id);

            if (CB::hasColumn($this->table, 'updated_at')) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            $data = $this->hookPreEdit($data, $id);

            DB::table($this->table)->where($this->primaryKey, $id)->update($data);

            $this->hookPostEdit($id);

            CbLogModule::logSuccess(cb_lang("log_update_data",['name'=>$data[$this->titleColumn],'module'=>$this->moduleName]));

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
        if(!CbRoleModule::canDelete()) {
            CbLogModule::logWarning(cb_lang('log_try_delete',['module'=>$this->moduleName]));
            return view("CbLogModule::deny");
        }

        $row = DB::table($this->table)->where($this->primaryKey, $id)->first();

        CbLogModule::logSuccess(cb_lang("log_delete_data",['name'=> $row->{$this->titleColumn},'module'=>$this->moduleName]));

        $this->hookPreDelete($id);

        if ($this->softDelete) {
            DB::table($this->table)->where($this->primaryKey, $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            DB::table($this->table)->where($this->primaryKey, $id)->delete();
        }

        $this->hookPostDelete($id);

        if(request('return_url')) {
            return CB::redirect(request('return_url'),cb_lang('data_has_been_deleted'),'success');
        } else {
            return CB::back(cb_lang('data_has_been_deleted'),'success');
        }
    }

    public function getDetail($id)
    {
        if(!CbRoleModule::canRead()) {
            CbLogModule::logWarning(cb_lang('log_try_read',['module'=>$this->moduleName]));
            return view("CbLogModule::deny");
        }

        $row = DB::table($this->table)->where($this->primaryKey,$id)->first();

        return view('crudbooster::crud.detail', [
           'pageTitle'=> $this->moduleName.': '. cb_lang('detail').' - '. $row->{$this->titleColumn}
        ]);
    }
}
