<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesAccountFicohsaTcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('account_ficohsa_tc', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('ui');
            $table->string('status');
            $table->double('balance');
            $table->double('balance_usd');
            $table->date('assign_date');
            $table->date('separation_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('account_ficohsa_tc',function(Blueprint $table){
            $table->drop();
        });
    }
}
