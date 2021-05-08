<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesCharacterizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('characterizations', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
            $table->string('code',20);
            $table->integer('order')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('interactions',function(Blueprint $table){
            $table->unsignedBigInteger('characterization_id');
            $table->foreign('characterization_id')->references('id')->on('characterizations');
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
        Schema::table('characterizations',function(Blueprint $table){
            $table->drop();
        });

        Schema::table('interactions',function(Blueprint $table){
            $table->dropColumn('characterization_id');
        });
    }
}
