<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;
use crocodicstudio\crudbooster\helpers\CB;

class CBAuthAPI
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


        CB::authAPI();

        return $next($request);
    }
}
