<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Address;
use App\Models\TransactionType;
use App\Models\TransactionStatus;
use App\Models\TransactionPaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        // Get random or first related models
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $address = Address::inRandomOrder()->first() ?? Address::factory()->create(['user_id' => $user->id]);
        $type = TransactionType::inRandomOrder()->first() ?? TransactionType::factory()->create();
        $status = TransactionStatus::inRandomOrder()->first() ?? TransactionStatus::factory()->create();
        $paymentMethod = TransactionPaymentMethod::inRandomOrder()->first() ?? TransactionPaymentMethod::factory()->create();

        return [
            'user_id' => $user->id,
            'address_id' => $address->id,
            'type_id' => $type->id,
            'status_id' => $status->id,
            'payment_method_id' => $paymentMethod->id,
        ];
    }
}
