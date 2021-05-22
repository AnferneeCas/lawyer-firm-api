<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsAnothersExtraColumnsToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demands', function (Blueprint $table) {
            $table->string('type');        
        });

        Schema::create('subcharacterizations', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
            $table->string('code');       
            $table->string('order');
            $table->timestamps();    
        });

        Schema::table('accounts', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('subcharacterization_id')->nullable();
            $table->foreign('subcharacterization_id')->references('id')->on('subcharacterizations');
        });

        Schema::table('document_requests', function (Blueprint $table) {
            //
            $table->date('received_date')->nullable()->change();  
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table', function (Blueprint $table) {
            //
        });
    }
}
