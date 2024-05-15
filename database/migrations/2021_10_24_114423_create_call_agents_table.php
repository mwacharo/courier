<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_agents', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('phone_number')->nullable();
            $table->string('client_name')->nullable();
            $table->string('admin_id')->nullable();
            $table->string('status')->default('inactive'); // inactive, busy, available
            $table->string('sessionId')->nullable();
            $table->string('token')->nullable();
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
        Schema::dropIfExists('call_agents');
    }
}
