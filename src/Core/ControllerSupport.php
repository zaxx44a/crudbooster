<?php


namespace Crocodic\CrudBooster\Core;


use Crocodic\CrudBooster\Core\Helpers\CB;

trait ControllerSupport
{

    public function postActionSelected()
    {
        $this->genericLoader();
        $id_selected = Request::input('checkbox');
        $button_name = Request::input('button_name');

        if (! $id_selected) {
            CRUDBooster::redirect($_SERVER['HTTP_REFERER'], cbLang("alert_select_a_data"), 'warning');
        }

        if ($button_name == 'delete') {
            if (! CRUDBooster::isDelete()) {
                CRUDBooster::insertLog(cbLang("log_try_delete_selected", ['module' => CRUDBooster::getCurrentModule()->name]));
                CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
            }

            $this->hook_before_delete($id_selected);
            $tablePK = CB::pk($this->table);
            if (CRUDBooster::isColumnExists($this->table, 'deleted_at')) {

                DB::table($this->table)->whereIn($tablePK, $id_selected)->update(['deleted_at' => date('Y-m-d H:i:s')]);
            } else {
                DB::table($this->table)->whereIn($tablePK, $id_selected)->delete();
            }
            CRUDBooster::insertLog(cbLang("log_delete", ['name' => implode(',', $id_selected), 'module' => CRUDBooster::getCurrentModule()->name]));

            $this->hook_after_delete($id_selected);

            $message = cbLang("alert_delete_selected_success");

            return redirect()->back()->with(['message_type' => 'success', 'message' => $message]);
        }

        $action = str_replace(['-', '_'], ' ', $button_name);
        $action = ucwords($action);
        $type = 'success';
        $message = cbLang("alert_action", ['action' => $action]);

        if ($this->actionButtonSelected($id_selected, $button_name) === false) {
            $message = ! empty($this->alert['message']) ? $this->alert['message'] : 'Error';
            $type = ! empty($this->alert['type']) ? $this->alert['type'] : 'danger';
        }

        return redirect()->back()->with(['message_type' => $type, 'message' => $message]);
    }


}