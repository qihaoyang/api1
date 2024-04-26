<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizationsController extends Controller
{

    public function store(AuthorizationRequest $request)
    {
        $username           = $request->username;
        $params['password'] = $request->password;
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $params['email'] = $username :
            $params['phone'] = $username;

        if (!$token = Auth::guard('api')->attempt($params)) {
            throw new AuthenticationException('用户名或密码错误');
        }

        return $this->responseWithToken($token)->setStatusCode(200);
    }

    public function socialStore($type, SocialAuthorizationRequest $request)
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
                    $user = User::create([
                        'name'           => $oauth_user->getNickname(),
                        'avatar'         => $oauth_user->getAvatar(),
                        'weixin_unionid' => $unionid,
                        'weixin_openid'  => $openid,
                    ]);
                }
                break;

        }
        $token = auth('api')->login($user);

        return $this->responseWithToken($token)->setStatusCode(201);
    }

    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->responseWithToken($token);
    }

    public function destroy()
    {
        Auth::guard('api')->logout();

        //204 No Content - 对不会返回响应体的成功请求进行响应（比如 DELETE 请求）
        return response(null, 204);
    }


    protected function responseWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => env('JWT_TTL') * 60,

        ]);
    }
}
