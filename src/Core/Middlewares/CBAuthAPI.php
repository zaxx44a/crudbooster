<?php

namespace Crocodic\CrudBooster\Core\Middlewares;

use Closure;

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

        $allowedUserAgent = config('crudbooster.API_USER_AGENT_ALLOWED');
        $userAgent = $request->header('User-Agent');
        $authorization = $request->header('Authorization');

        if ($allowedUserAgent && count($allowedUserAgent)) {
            $userAgentValid = false;
            foreach ($allowedUserAgent as $a) {
                if (stripos($userAgent, $a) !== false) {
                    $userAgentValid = true;
                    break;
                }
            }
            if ($userAgentValid == false) {
                return response()->json([
                    'status'=> 0,
                    'message'=> cb_lang('device_agent_invalid')
                ],403);
            }
        }

        $accessToken = ltrim($authorization,"Bearer ");
        if(!cache("api_token_".$accessToken)) {
            return response()->json([
                'status'=> 0,
                'message'=> cb_lang('forbidden_access')
            ]);
        }

        return $next($request);
    }
}
