<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api1:generate-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建指定id的 token';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user_id = $this->ask('请输入一个用户id');
        $user    = User::find($user_id);
        if (!$user) {
            $this->error('指定的用户不存在');
        }

        //token 过期时间，这里是一年
        $ttl   = 365 * 24 * 60;
        $token = auth('api')->setTTL($ttl)->login($user);
        $this->info($token);
        return true;
    }
}
