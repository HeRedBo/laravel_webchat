<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountsTable extends Migration
{
    protected $table = 'counts';
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
            $table->bigInteger('user_id')->comment("用户ID");
            $table->smallInteger('room_id')->comment("房间ID");
            $table->integer('count')->default(0)->comment("房间未读消息数");
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
