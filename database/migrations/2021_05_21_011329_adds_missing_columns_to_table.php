<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsMissingColumnsToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // jefatura
        Schema::table('account_ficohsa_ptmo', function (Blueprint $table) {
            //
            $table->string('administration');
            $table->dropColumn('balance_usd');
            $table->string('product');
            $table->string('segmentation');
            $table->string('product_type');
            $table->string('wallet');
        });
        Schema::table('account_ficohsa_tc', function (Blueprint $table) {
            //
            $table->string('administration');
            $table->string('product');
            $table->string('segmentation');
            $table->string('product_type');
            $table->string('wallet');
            
        });

        Schema::table('clients', function (Blueprint $table) {
            //
            $table->string('company_type')->nullable();
            
        });
        Schema::table('demands', function (Blueprint $table) {
            //
            $table->date('started_at');
            
        });

        Schema::create('external_client_db_search', function (Blueprint $table) {
            //
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->string('work_address')->nullable();
            $table->string('home_address')->nullable();
            $table->string('company_name')->nullable();
            $table->string('phone_numbers')->nullable();
            $table->boolean('matched');
            $table->timestamps();
            
        });

        Schema::create('client_assets', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->string('asset_type');
            $table->string('asset_description');       
        });

        Schema::create('document_requests', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->date('request_date');
            $table->date('received_date');            
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('table', function (Blueprint $table) {
        //     //
        // });
    }
}
