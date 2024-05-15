<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesa_payments', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('FirstName')->nullable();
            $table->string('MiddleName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('MSISDN')->nullable();
            $table->string('TransID')->nullable();
            $table->string('InvoiceNumber')->nullable();
            $table->string('BusinessShortCode')->nullable();
            $table->string('ThirdPartyTransID')->nullable();
            $table->string('TransactionType')->nullable();
            $table->string('OrgAccountBalance')->nullable();
            $table->string('BillRefNumber')->nullable();
            $table->string('TransAmount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mpesa_payments');
    }
}
