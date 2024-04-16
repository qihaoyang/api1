<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;

class CustomizeThrottleKey extends ThrottleRequests
{

    protected function resolveRequestSignature($request)
    {
        return sha1(
            $request->route()->getName() . '|' . $request->ip()
        );

    }
}
