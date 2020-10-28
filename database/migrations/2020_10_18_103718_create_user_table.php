<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user');
        Schema::create(
            'user',
            function (Blueprint $table) {
                $table->engine = "InnoDB";
                $table->increments('id');
                $table->string('username', 20);
                $table->tinyInteger('gender');
                $table->tinyInteger('age');
                $table->integer('region_id')->unsigned();
                $table->timestamp('created_at')
                    ->default(DB::raw('CURRENT_TIMESTAMP'));
            }
        );
        Schema::table(
            'user',
            function (Blueprint $table) {
                $table->foreign('region_id')
                    ->references('id')
                    ->on('region');
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
        Schema::dropIfExists('user');
    }
}
