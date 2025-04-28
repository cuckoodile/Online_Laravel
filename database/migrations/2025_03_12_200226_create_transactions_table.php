<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("address_id");
            $table->unsignedBigInteger("type_id");
            $table->unsignedBigInteger("status_id");
            $table->unsignedBigInteger("payment_method_id");
            $table->boolean('is_void')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("address_id")->references("id")->on("addresses")->onDelete("cascade");
            $table->foreign("type_id")->references("id")->on("transaction_types")->onDelete("cascade");
            $table->foreign("status_id")->references("id")->on("transaction_statuses")->onDelete("cascade");
            $table->foreign("payment_method_id")->references("id")->on("transaction_payment_methods")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
