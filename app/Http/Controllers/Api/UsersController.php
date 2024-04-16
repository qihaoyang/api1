<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
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
        return new UserResource($user);

    }
}
