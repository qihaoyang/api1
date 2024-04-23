<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use App\Http\Requests\Api\CaptchaRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CaptchasController extends Controller
{

    public function store(CaptchaRequest $request, CaptchaBuilder $builder)
    {
        $key       = Str::random();
        $cache_key = 'captcha_'.$key;
        $phone     = $request->phone;
        $captcha   = $builder->build();
        $expire_at = now()->addMinutes(5);

        Cache::put($cache_key, ['code' => $captcha->getPhrase(), 'phone' => $phone], $expire_at);

        $result = [
            'key'              => $key,
            'expire_at'        => $expire_at->toDateTimeString(),
            'captcha_img_code' => $captcha->inline()
        ];

        return response()->json($result)->setStatusCode(201);

    }
}
