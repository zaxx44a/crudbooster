<?php


namespace Crocodic\CrudBooster\Modules\AuthModule\Controllers;


use Crocodic\CrudBooster\Core\Helpers\CB;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CbAuthController extends Controller
{
    use ValidatesRequests;

    public function getLogin()
    {
        $data = [];
        $data['pageTitle'] = cb_lang("login");
        return view("CbAuthModule::login", $data);
    }

    public function postDoLogin()
    {
        try {
            $usernameColumn = cb_config('USERNAME')['column'];
            $passwordColumn = cb_config('PASSWORD')['column'];
            $this->validate(request(), [
                $usernameColumn => cb_config('USERNAME')['validation'],
                $passwordColumn => cb_config('PASSWORD')['validation']
            ]);

            $user = CB::first('users',[$usernameColumn=>request($usernameColumn)]);

            if($user && Hash::check($user->{$passwordColumn}, $user->{$passwordColumn})) {
                auth()->loginUsingId($user->id);
                return redirect()->intended(admin_path());
            } else {
                throw new \Exception(cb_lang('credential_wrong'),400);
            }

        } catch (ValidationException $e) {
            return CB::back($e->getMessage());
        } catch (\Exception $e) {
            return CB::back($e->getMessage());
        }
    }

    public function getLogout()
    {
        auth()->logout();
        return CB::redirectAdmin("login",cb_lang("see_you_later"),"success");
    }
}