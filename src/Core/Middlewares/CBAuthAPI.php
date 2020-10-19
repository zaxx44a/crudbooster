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
                $result['api_status'] = 0;
                $result['api_message'] = "THE DEVICE AGENT IS INVALID";
                $res = response()->json($result, 400);
                $res->send();
                exit;
            }
        }

        $accessToken = ltrim($authorization,"Bearer ");
        $accessTokenData = Cache::get("api_token_".$accessToken);
        if(!$accessTokenData) {
            response()->json([
                'api_status'=> 0,
                'api_message'=> 'Forbidden Access!'
            ], 403)->send();
            exit;
        }

        return $next($request);
    }
}
