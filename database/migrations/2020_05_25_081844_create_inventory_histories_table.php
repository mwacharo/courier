<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('transaction_type')->default(0); // 1-Inscan, 2-Outscan
            $table->string('inventory_id')->nullable();
            $table->foreign('inventory_id')->references('id')->on('inventories');
            $table->integer('quantity')->default(0);
            $table->integer('balance')->default(0);
            $table->string('admin_id')->nullable();
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
        Schema::dropIfExists('inventory_histories');
    }
}
