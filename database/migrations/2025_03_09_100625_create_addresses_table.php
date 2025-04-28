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
            $table->string("region");
            $table->string("province");
            $table->string("district");
            $table->string("city_or_municipality");
            $table->string("barangay");
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
            $table->string("zip_code");
            $table->timestamps();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
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
