<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->boolean('inventory_product')->default(false);
            $table->string('inventory_id')->nullable();
            $table->foreign('inventory_id')->references('id')->on('inventories');
            $table->string('description');
            $table->string('sku')->nullable();
            $table->double('weight')->default(0);
            $table->double('price')->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('quantity_returned')->default(0);
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
        Schema::dropIfExists('order_items');
    }
}
