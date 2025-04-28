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
<<<<<<< HEAD
<<<<<<< HEAD
=======
            // $table->unsignedBigInteger('transaction_id')->nullable();
>>>>>>> c88078fc465a0c6707a08714657eabbb89d86fbf
            $table->string("region");
            $table->string("province");
            $table->string("district");
            $table->string("city_municipality");
            $table->string("barangay");
<<<<<<< HEAD
            $table->string("subdivision_or_village");
            $table->string("street_number");
            $table->string("street_name");
            $table->string("unit_number");
=======
            $table->string("house_address");
            $table->string("region");
            $table->string("province");
            $table->string("city");
            $table->string("baranggay");
>>>>>>> 69bff22 (Product Comments)
=======
            $table->string("subdivision_village");
            $table->string("street");
            $table->string("lot_number");
            $table->string("block_number");
>>>>>>> c88078fc465a0c6707a08714657eabbb89d86fbf
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
