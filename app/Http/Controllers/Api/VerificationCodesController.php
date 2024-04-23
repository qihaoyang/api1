<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captcha_key  = 'captcha_'.$request->captcha_key;
        $captcha_data = Cache::get($captcha_key);
        if (!$captcha_data) {
            abort('401', '验证码已过期');
        }

        if (!hash_equals($captcha_data['code'], $request->captcha_code)) {
            throw new AuthenticationException('验证码错误');
        }


        if (config('app.env') != 'production') {
            $code = '1234';
        } else {
            $code = rand(1000, 9999);
            try {
                $easySms->send(18124621992, [
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data'     => [
                        'code' => $code
                    ],
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                abort(500, $message ?: '短信发送异常');
            }

        }

        $key        = Str::random();
        $cache_key  = 'verificationCode_'.$key;
        $expired_at = now()->addMinutes(5);

        Cache::put($cache_key, ['code' => $code, 'phone' => $captcha_data['phone']], $expired_at);
        Cache::forget($captcha_key);

        return response()->json([
            'key'        => $key,
            'expired_at' => $expired_at->toDateTimeString()
        ])->setStatusCode(201);
    }
}
