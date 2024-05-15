<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutboundDeliverySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outbound_delivery_schedules', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('from');
            $table->string('destination');
            $table->double('extra_weight')->default(0);
            $table->double('charge')->default(0);
            $table->double('tax')->default(0);
            $table->double('total_amount')->default(0);
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
        Schema::dropIfExists('outbound_delivery_schedules');
    }
}
