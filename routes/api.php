<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {

        Route::middleware('customThrottle:'.config('api.rate_limits.sign'))->group(function () {
            //手机验证码
            Route::post('verificationCodes', 'VerificationCodesController@store')->name('verificationCodes.store');

            //用户注册
            Route::post('users', 'UsersController@store')->name('users.store');

            //第三方登录
            Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
                /**
                 * 注意这里的参数，我们对 social_type 进行了限制，只会匹配 wechat，如果你增加了其他的第三方登录，
                 * 可以在这里增加限制，例如支持微信及微博：->where('social_type', 'wechat|weibo') 。
                 */
                ->where('social_type', 'wechat')
                ->name('socials.authorization.store');

            //登录
            Route::post('authorizations', 'AuthorizationsController@store')->name('authorizations.store');

            //刷新token
            Route::put('authorizations/current', 'AuthorizationsController@update')->name('authorizations.update');

            //删除token
            Route::delete('authorizations/current', 'AuthorizationsController@destroy')->name('authorizations.destroy');

            //图片验证码
            Route::post('captchas', 'CaptchasController@store')->name('captchas.store');


        });

        Route::middleware('customThrottle:'.config('api.rate_limits.access'))->group(function () {

        });

    });


