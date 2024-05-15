<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('national_id')->unique();
            $table->string('date_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->bigInteger('phone_number')->unique();
            $table->string('email')->unique();
            $table->string('profile_image')->nullable();
            $table->string('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->string('password');
            $table->string('firebase_token')->nullable();
            $table->boolean('enabled')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER Table riders add rider_id INTEGER NOT NULL UNIQUE AUTO_INCREMENT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riders');
    }
}
