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
            $table->string('email')->nullable();
            $table->string('social_id');
            $table->string('ui');
            $table->string('work_address')->nullable();
            $table->string('home_address')->nullable();
            $table->string('contact_number')->nullable();
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
