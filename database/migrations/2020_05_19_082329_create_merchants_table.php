<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('name')->nullable();
            $table->integer('merchant_type')->default(0); // 0 - Individual, 1 - Company
            $table->string('address')->nullable();
            $table->bigInteger('phone_number')->unique();
            $table->string('email')->unique();
            $table->string('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->string('town_id')->nullable();
            $table->foreign('town_id')->references('id')->on('towns');
            $table->boolean('enable_cash_on_delivery_fee')->default(false);
            $table->double('cash_on_delivery_fee')->default(0);
            $table->boolean('enable_delivery_fee_nairobi')->default(false);
            $table->double('delivery_fee_nairobi')->default(0);
            $table->boolean('enable_delivery_fee_outbound')->default(false);
            $table->double('delivery_fee_outbound')->default(0);
            $table->boolean('enable_returns_management_fee')->default(false);
            $table->boolean('enable_warehousing_fee')->default(false);
            $table->double('warehousing_fee')->default(0);
            $table->boolean('enable_packaging_fee')->default(false);
            $table->double('packaging_fee')->default(0);
            $table->boolean('enable_call_centre_fee')->default(false);
            $table->double('call_centre_fee')->default(0);
            $table->boolean('enable_label_printing_fee')->default(false);
            $table->double('label_printing_fee')->default(0);
            $table->string('contract')->nullable();
            $table->string('google_sheet')->nullable();
            $table->string('password');
            $table->string('admin_id')->nullable();
            $table->string('merchant_prefix')->nullable();
            $table->boolean('enabled')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER Table merchants add merchant_id INTEGER NOT NULL UNIQUE AUTO_INCREMENT;');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants');
    }
}
