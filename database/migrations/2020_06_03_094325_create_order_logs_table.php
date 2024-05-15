<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_logs', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('admins');
            $table->string('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string('inventory_id')->nullable();
            $table->foreign('inventory_id')->references('id')->on('inventories');
            $table->string('status')->nullable();
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
        Schema::dropIfExists('order_logs');
    }
}
