<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesFirmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('firms', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
            $table->string('address');
            $table->string('telefone_number');
            $table->string('logo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('clients',function (Blueprint $table){
            $table->unsignedBigInteger('firm_id');
            $table->foreign('firm_id')->references('id')->on('firms');
        });
        Schema::table('accounts',function (Blueprint $table){
            $table->unsignedBigInteger('firm_id');
            $table->foreign('firm_id')->references('id')->on('firms');
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
        Schema::table('clients',function(Blueprint $table){
            $table->dropColumn('firm_id');
        });
        Schema::table('accounts',function(Blueprint $table){
            $table->dropColumn('firm_id');
        });
        Schema::table('firms',function(Blueprint $table){
            $table->drop();
        });

       
    }
}
