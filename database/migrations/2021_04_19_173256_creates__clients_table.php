<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('clients', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
            $table->string('email');
            $table->string('social_id');
            $table->string('ui');
            $table->string('work_address');
            $table->string('home_address');
            $table->string('contact_number');
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
        Schema::table('Clients',function(Blueprint $table){
            $table->drop();
        });
    }
}
