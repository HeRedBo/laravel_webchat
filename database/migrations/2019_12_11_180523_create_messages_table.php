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
            $table->string('msg',255)->comment('文本消息');
            $table->string('img',255)->comment('图片消息');
            $table->bigInteger('user_id');
            $table->smallInteger('room_id');
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
