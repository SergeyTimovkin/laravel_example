<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('phone');
        Schema::create(
            'phone',
            function (Blueprint $table) {
                $table->engine = "InnoDB";
                $table->increments('id');
                $table->char('number', 11);
                $table->integer('user_id')
                    ->unsigned();
            }
        );
        Schema::table(
            'phone',
            function (Blueprint $table) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('user');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phone');
    }
}
