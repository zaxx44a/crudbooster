<?php

namespace Crocodic\CrudBooster\Core\Middlewares;

use Closure;
use Crocodic\CrudBooster\Core\Helpers\CB;

class CBBackend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth()->guest()) {
            return CB::redirect(admin_path('login'),cb_lang('please_Login_for_first'),'warning');
        }

        // Todo: Filter role permission middleware

        return $next($request);
    }
}
