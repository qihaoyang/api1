<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
    /**
     * @param  UserRequest  $request
     * @return UserResource
     * @throws AuthenticationException
     * 用户登录
     */
    public function store(UserRequest $request)
    {
        $cache_key  = 'verificationCode_'.$request->verification_key;
        $cache_data = Cache::get($cache_key);
        if (!$cache_data) {
            abort('403', '验证码已过期');
        }

        if (!hash_equals($cache_data['code'], $request->verification_code)) {
            throw new AuthenticationException('验证码错误');
        }

        $user = User::create([
            'name'     => $request->input('name'),
            'password' => $request->input('password'),
            'phone'    => $cache_data['phone'],
        ]);

        Cache::forget($cache_key);
        return (new UserResource($user))->showSensitiveFields();

    }


    /**
     * @param  Request  $request
     * @return UserResource
     * 当前登录用户信息
     */
    public function me(Request $request)
    {

        return (new UserResource($request->user()))->showSensitiveFields();
    }

    /**
     * @param  User  $user
     * @return UserResource
     * 获取某个用户的信息
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UserRequest $request)
    {
        $user = $request->user();
        $data = $request->only(['name', 'email', 'introduction']);
        if ($request->avatar_image_id) {
            $image          = Image::find($request->avatar_image_id);
            $data['avatar'] = $image->path;
        }

        $user->update($data);
        return (new UserResource($user))->showSensitiveFields();
    }
}
