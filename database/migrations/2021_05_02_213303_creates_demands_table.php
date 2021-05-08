<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesDemandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('courts', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('judges', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('demands', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->date('presentation_date');
            $table->string('record_number');
            $table->string('city');
            $table->double('amount');

            $table->unsignedBigInteger('court_id');
            $table->foreign('court_id')->references('id')->on('courts');

            $table->unsignedBigInteger('judge_id');
            $table->foreign('judge_id')->references('id')->on('judges');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('interactions',function(Blueprint $table){
            $table->string('interaction_status_type');
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
        Schema::table('demands',function(Blueprint $table){
            $table->drop();
        });
        
        Schema::table('judges',function(Blueprint $table){
            $table->drop();
        });

        Schema::table('courts',function(Blueprint $table){
            $table->drop();
        });

        Schema::table('interactions',function(Blueprint $table){
            $table->dropColumn('interaction_status_type');
        });
    }
}
