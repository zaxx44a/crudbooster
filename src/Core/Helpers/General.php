<?php
/* 
| ---------------------------------------------------------------------------------------------------------------
| Main Helper of CRUDBooster
| Do not edit or modify this helper unless your modification will be replace if any update from CRUDBooster.
| 
| Homepage : http://crudbooster.com
| ---------------------------------------------------------------------------------------------------------------
|
*/

if(!function_exists('cb_config')) {
    /**
     * @param $key
     * @param null $default
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function cb_config($key, $default = null) {
        return config('crudbooster.'.$key, $default);
    }
}

if(!function_exists('force_view')) {
    /**
     * To force display a view when it called from a sub method
     * @param $view
     * @param $data
     */
    function force_view($view, $data = []) {
        echo view($view, $data)->render();
        exit;
    }
}

if(!function_exists('admin_path')) {
    /**
     * @param null $suffix
     * @return string
     */
    function admin_path($suffix = null) {
        return rtrim(config('crudbooster.ADMIN_PATH').'/'.$suffix,'/');
    }
}

if(!function_exists('admin_url')) {
    /**
     * @param null $suffix
     * @param array $parameters
     * @param null $secure
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function admin_url($suffix = null, $parameters = [], $secure = null) {
        return url(admin_path($suffix), $parameters, $secure);
    }
}

if(!function_exists('cbLang')) {
    /**
     * @param $key
     * @param array $replace
     * @param null $locale
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    function cb_lang($key, array $replace = [], $locale = null) {
        return trans("crudbooster::crudbooster.".$key, $replace, $locale);
    }
}

if(!function_exists('db')) {
    /**
     * @param string $table
     * @return \Illuminate\Database\Query\Builder
     */
    function db(string $table) {
        return \Illuminate\Support\Facades\DB::table($table);
    }
}

if(!function_exists('current_date_time')) {
    /**
     * Get current date time
     * @return false|string
     */
    function current_date_time() {
        return date('Y-m-d H:i:s');
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get data from input post/get more simply
| --------------------------------------------------------------------------------------------------------------
| $name = name of input
|
*/

if(!function_exists('get_setting')) {
    /**
     * Get setting value
     * @param $key
     * @param null $default
     * @return bool
     */
    function get_setting($key, $default = null) {
        $setting = \Crocodic\CrudBooster\Core\Helpers\CB::getSetting($key);
        $setting = ($setting)?:$default;
        return $setting;
    }
}

if(!function_exists('str_random')) {
    /**
     * Replace laravel str_random function
     * @param int $length
     * @return string
     */
    function str_random($length = 16) {
        return \Illuminate\Support\Str::random($length);
    }
}

if(!function_exists('str_slug')) {
    /**
     * Replace laravel str_slug function
     * @param $text
     * @param string $separator
     * @param string $language
     * @return string
     */
    function str_slug($text, $separator = "-", $language = "en") {
        return \Illuminate\Support\Str::slug($text, $separator, $language);
    }
}

if(!function_exists('g')) {
    /**
     * @param $key
     * @param null $default
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\Request|string
     */
    function g($key, $default = null) {
        return request($key, $default);
    }
}

if(!function_exists('min_var_export')) {
    /**
     * @param $input
     * @return string|null
     */
    function min_var_export($input) {
        if(is_array($input)) {
            $buffer = [];
            foreach($input as $key => $value)
                $buffer[] = var_export($key, true)."=>".min_var_export($value);
            return "[".implode(",",$buffer)."]";
        } else
            return var_export($input, true);
    }
}

