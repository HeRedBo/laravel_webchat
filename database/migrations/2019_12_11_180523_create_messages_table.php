<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    protected $table = 'messages';
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
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id')->comment("主键ID");
            $table->string('msg',255)->default('')->comment('文本消息');
            $table->string('img',255)->default('')->comment('图片消息');
            $table->bigInteger('user_id')->default(0)->comment("用户ID");
            $table->smallInteger('room_id')->default(0)->comment("房间号");;
            $table->timestamp('created_at')->nullable()->comment('创建时间');
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
