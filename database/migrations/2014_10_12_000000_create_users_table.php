<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    protected $table = 'users';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->table)) {
            return false;
        }
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->comment("主键ID");
            $table->string('name')->comment('用户昵称');
            $table->string('avatar')->default('')->comment('用户头像');
            $table->string('email')->unique()->comment("邮箱");
            $table->timestamp('email_verified_at')->nullable()->comment("邮箱是否校验");;
            $table->string('password')->comment("用户密码");
            $table->rememberToken();
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
