<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('merchant_id')->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->string('sku')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('description', 2000)->nullable();
            $table->string('image')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('spoilt')->default(0);
            $table->integer('low_count')->default(0);
            $table->double('amount')->default(0);
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
        Schema::dropIfExists('inventories');
    }
}
