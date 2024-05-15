<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_scans', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('order_no');
            $table->string('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string('rider_id')->nullable();
            $table->foreign('rider_id')->references('id')->on('riders');
            $table->string('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->string('remarks', 5000)->nullable();
            $table->string('status')->nullable();
            $table->integer('scan_type')->default(0); //1 - Inscan, 2 - Outscan
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
        Schema::dropIfExists('order_scans');
    }
}
