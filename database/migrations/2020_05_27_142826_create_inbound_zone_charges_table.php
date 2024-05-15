<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInboundZoneChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inbound_zone_charges', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('zone'); //zone1, zone2, zone3, zone4
            $table->double('extra_weight')->default(0);
            $table->double('same_day_charge')->default(0);
            $table->double('same_day_tax')->default(0);
            $table->double('same_day_total_amount')->default(0);
            $table->double('overnight_charge')->default(0);
            $table->double('overnight_tax')->default(0);
            $table->double('overnight_total_amount')->default(0);
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
        Schema::dropIfExists('inbound_zone_charges');
    }
}
