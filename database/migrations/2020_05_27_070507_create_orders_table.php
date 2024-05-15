<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('order_no')->unique();
            $table->integer('destination_type')->default(0); // 1- Outbound, 2-Inbound
            $table->integer('service_type')->default(0);
            $table->integer('inbound_rate_type')->default(0); // 1- On demand, 2-Zone delivery
            $table->boolean('is_sender_merchant')->default(false);
            $table->string('merchant_id')->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->string('sender_name')->nullable();
            $table->string('sender_address')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('sender_phone_alternative')->nullable();
            $table->string('sender_country')->nullable();
            $table->string('sender_town')->nullable();
            $table->string('pickup_country')->nullable();
            $table->string('pickup_town')->nullable();
            $table->string('pickup_address')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_address')->nullable();
            $table->string('receiver_email')->nullable();
            $table->string('receiver_gender')->nullable(); // male, female
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_phone_alternative')->nullable();
            $table->string('receiver_country')->nullable();
            $table->string('receiver_town')->nullable();
            $table->string('receiver_latitude')->nullable();
            $table->string('receiver_longitude')->nullable();
            $table->string('special_instruction',255)->nullable();
            $table->integer('payment_type')->default(0); // 1 - Cash, 2 - Invoice
            $table->boolean('cash_on_delivery')->default(false);
            $table->double('cash_on_delivery_amount')->default(0)->nullable();
            $table->double('amount')->default(0);
            $table->double('return_amount')->default(0);
            $table->double('total_weight')->default(0);
            $table->boolean('insurance')->default(false);
            $table->string('order_status')->nullable();
            $table->string('status_reason')->nullable();
            $table->string('custom_reason')->nullable();
            $table->integer('payment_status')->default(0); // 0 - pending, 1 - paid,  2 - less payment, 3 - over payment
            $table->integer('payment_method')->default(0); // 1-Cash, 2-Mpesa, 3-Cash&Mpesa
            $table->double('cash_amount')->default(0);
            $table->double('mpesa_amount')->default(0);
            $table->double('cash_mpesa_amount')->default(0);
            $table->string('transaction_code')->nullable();
            $table->string('rider_id')->nullable();
            $table->foreign('rider_id')->references('id')->on('riders');
            $table->string('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->string('booking_date')->nullable();
            $table->string('booking_time')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('follow_up_date')->nullable();
            $table->string('scheduled_date')->nullable();
            $table->string('status_date')->nullable();
            $table->string('delivered_date')->nullable();
            $table->double('delivery_distance')->default(0)->nullable();
            $table->string('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('admins');
            $table->string('agent')->nullable();
            $table->string('zone_id')->nullable();
            $table->boolean('inventory')->default(false);
            $table->boolean('upsell')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER Table orders add order_id INTEGER NOT NULL UNIQUE AUTO_INCREMENT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
