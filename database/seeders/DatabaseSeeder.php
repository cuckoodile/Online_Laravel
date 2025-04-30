<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $categories = [
            "Jacket",
            "Hat % Cap",
            "Shirt",
            "T-Shirt",
            "Sunglasses",
            
        ];

        foreach ($categories as $category) {
            Category::create([
                "name" => $category
            ]);
        }

        $user = User::create([
            "username" => "admin",
            "email" => "admin@gmail.com",
            "password" => "admin",
        ]);

        $user->profile()->create([
            "first_name" => "John",
            "last_name" => "Doe",
            "contact_number" => "0909090909",
        ]);

        Product::create([
            "name" => "Lipstick",
            "price" => 10,
            "description" => "Sa unang pahid, putok ang bibig",
            "category_id" => 2,
        ]);

        Product::create([
            "name" => "Dog Food",
            "price" => 100,
            "description" => "Para sa aso mong patay gutom",
            "category_id" => 3,
        ]);

        Product::create([
            "name" => "Rice Cooker",
            "price" => 1000,
            "description" => "Para sa mga tamad magluto",
            "category_id" => 1,
        ]);

        Product::create([
            "name" => "Shampoo",
            "price" => 50,
            "description" => "Para sa mga walang ligo",
            "category_id" => 2,
        ]);

        Product::create([
            "name" => "Rexona",
            "price" => 50,
            "description" => "Para sa kili-kili mong nakakamatay",
            "category_id" => 2,
        ]);
    }
}
