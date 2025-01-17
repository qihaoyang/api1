<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('weixin_openid')->nullable()->after('password');
            $table->string('weixin_unionid')->nullable()->after('weixin_openid');
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')->whereNull('password')->update(['password' => 'password']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['weixin_openid', 'weixin_unionid']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
