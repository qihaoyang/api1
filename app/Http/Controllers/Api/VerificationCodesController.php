<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;

        if (config('app.env') != 'production') {
            $code = '1234';
        } else {
            $code = rand(100000, 999999);
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

        Cache::put($cache_key, ['code' => $code, 'phone' => $phone], $expired_at);

        return response()->json([
            'key'        => $key,
            'expired_at' => $expired_at->toDateTimeString()
        ])->setStatusCode(201);
    }
}
