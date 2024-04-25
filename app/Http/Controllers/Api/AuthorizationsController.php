<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    public function socialStore($type, AuthorizationRequest $request)
    {
        $driver = \Socialite::create($type);

        try {
            if ($code = $request->code) {
                $oauth_user = $driver->userFromCode($code);
            } else {
                if ($type == 'wechat') {
                    $driver->withOpenid($request->openid);
                }
                $oauth_user = $driver->userFromToken($request->access_token);
            }
        } catch (\Exception $e) {
            throw new AuthenticationException('参数错误，未获取用户信息');
        }

        $openid = $oauth_user->getId();
        if (!$openid) {
            throw new AuthenticationException('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'wechat':
                $unionid = $oauth_user->getRaw()['unionid'] ?? null;

                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $openid)->first();
                }

                if (!$user) {
                    User::create([
                        'name'           => $oauth_user->getNickname(),
                        'avatar'         => $oauth_user->getAvatar(),
                        'weixin_unionid' => $unionid,
                        'weixin_openid'  => $openid,
                    ]);
                }
                break;

        }

        return response()->json(['token' => $user->id]);
    }
}
