<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id")->unique();
            // $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string("region");
            $table->string("province");
            $table->string("district");
            $table->string("city_municipality");
            $table->string("barangay");
            $table->string("subdivision_village");
            $table->string("street");
            $table->string("lot_number");
            $table->string("block_number");
            $table->string("zip_code");
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            // $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
