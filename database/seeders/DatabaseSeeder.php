<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductSpecification;
use App\Models\ProductComment;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionPaymentMethod;
use App\Models\TransactionType;
use App\Models\TransactionStatus;
use Illuminate\Support\Facades\Log; //for debugging only
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $users = [
            [
                "username" => "admin",
                "email" => "admin@gmail.com",
                "password" => bcrypt("admin"),
                "profile" => [
                    "profile_image" => "https://img.freepik.com/free-vector/environmental-logo-vector-with-ecology-text_53876-112070.jpg",
                    "first_name" => "Admin",
                    "last_name" => "Admin",
                    "contact_number" => "09090909091",
                    "is_admin" => true
                ],
            ],
            [
                "username" => "DEVSIXauth",
                "email" => "devsix@gmail.com",
                "password" => bcrypt("password123"),
                "profile" => [
                    "profile_image" => "https://img.freepik.com/free-vector/environmental-logo-vector-with-ecology-text_53876-112070.jpg",
                    "first_name" => "Developer",
                    "last_name" => "Six",
                    "contact_number" => "09090909090",
                ],
            ],
            [
                "username" => "CatCodes",
                "email" => "alxrdvno09@gmail.com",
                "password" => bcrypt("password123"),
                "profile" => [
                    "profile_image" => "	https://i.pinimg.com/736x/50/d4/10/50d410a1d3910c435a1b45fba23d8658.jpg",
                    "first_name" => "Alexander Miguel",
                    "last_name" => "Divino",
                    "contact_number" => "09543549694",
                ],
            ],
            [
                "username" => "Cuckoodile",
                "email" => "cuckoodile@gmail.com",
                "password" => bcrypt("password123"),
                "profile" => [
                    "profile_image" => "https://thumbs.dreamstime.com/b/funny-sleeping-crocodile-23816758.jpg",
                    "first_name" => "Lhourde Ian",
                    "last_name" => "Sube",
                    "contact_number" => "09987654321",
                ],
            ],
            [
                "username" => "Fluffy",
                "email" => "fluffluff@gmail.com",
                "password" => bcrypt("password123"),
                "profile" => [
                    "profile_image" => "https://i.pinimg.com/736x/41/3a/34/413a349d67a791c998e11c1b9a296f6a.jpg",
                    "first_name" => "Zanjoe",
                    "last_name" => "Gonzales",
                    "contact_number" => "09123456789",
                ],
            ],
            
         ];
        
        // Create roles and permissions
        $roleAdmin = Role::firstOrCreate(["name" => "admin", "guard_name" => "api"]);
        $rolePermissionAdmin = Permission::firstOrCreate(["name" => "Manage All Works", "guard_name" => "api"]);
        $roleAdmin->givePermissionTo($rolePermissionAdmin);

        $roleUser = Role::firstOrCreate(["name" => "user", "guard_name" => "api"]);
        $rolePermissionUser = Permission::firstOrCreate(["name" => "Manage Own Post", "guard_name" => "api"]);
        $roleUser->givePermissionTo($rolePermissionUser);

        // if ($roleName === 'admin') {
        //     $permission = Permission::create(['name' => 'Manage All Works', 'guard_name' => 'api']);
        //     $role->givePermissionTo($permission);
        // } else {
        //     $permission = Permission::create(['name' => 'Manage Own Post', 'guard_name' => 'api']);
        //     $role->givePermissionTo($permission);
        // }

        foreach ($users as $userData) {
            // Extract profile data separately
            $profileData = $userData['profile'];
            unset($userData['profile']);

            // Create user in the database
            $user = User::create($userData);

            // Associate the profile
            $user->profile()->create($profileData);

            // Assign role based on is_admin
            $role = $profileData['is_admin'] ?? false ? $roleAdmin : $roleUser;
            $user->assignRole($role);
        }

        $addresses = [
            [
                "user_id" => 1,
                "address" => [
                    "name" => "Home",
                    "house_address" => "Blk 7 Lot 8 Molave St, Palmera Woodlands ",
                    "region" => "Calabarzon",
                    "province" => "Rizal",
                    "city" => "Antipolo City",
                    "baranggay" => "Cupang",
                    "zip_code" => "1870",
                ],
            ],
            [
                "user_id" => 3,
                "address" => [
                    "name" => "Work",
                    "house_address" => "I don't know",
                    "region" => "Calabarzon",
                    "province" => "Rizal",
                    "city" => "Taytay City",
                    "baranggay" => "San Juan Idon'tknow",
                    "zip_code" => "1920",
                ],
            ],
            [
                "user_id" => 4,
                "address" => [
                    "name" => "School",
                    "house_address" => "I don't know",
                    "region" => "NCR",
                    "province" => "Metro Manila",
                    "city" => "Pateros City",
                    "baranggay" => "San Pedroblabla",
                    "zip_code" => "1620",
                ],
            ],
        ];
        foreach ($addresses as $addressData) {
            // Extract address data separately
            $address = $addressData['address']; 
            unset($addressData['address']); // Remove address from user array before inserting
            
            // Create address in the database
            $user = User::find($addressData['user_id']);
            if ($user) {
                $user->address()->create($address);
            }
        }

        // Seed categories
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Books'],
            ['name' => 'Clothing'],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }

        // Seed products
        $products = [
            [
                'name' => 'Smartphone',
                'price' => 699.99,
                'admin_id' => 1,
                'product_image' => json_encode(['image1.jpg', 'image2.jpg']),
                'description' => 'A high-end smartphone with great features.',
                'category_id' => 1,
            ],
            [
                'name' => 'Novel',
                'price' => 19.99,
                'admin_id' => 1,
                'product_image' => json_encode(['novel.jpg']),
                'description' => 'A bestselling novel.',
                'category_id' => 2,
            ],
        ];
        foreach ($products as $product) {
            Product::create($product);
        }

        // Seed product specifications
        $productSpecifications = [
            [
                'product_id' => 1,
                'details' => json_encode(['Brand' => 'TechCorp', 'Model' => 'X100', 'Color' => 'Black']),
            ],
            [
                'product_id' => 2,
                'details' => json_encode(['Author' => 'John Doe', 'Genre' => 'Fiction', 'Pages' => 300]),
            ],
        ];
        foreach ($productSpecifications as $specification) {
            ProductSpecification::create($specification);
        }

        // Seed product comments
        $productComments = [
            [
                'product_id' => 1,
                'user_id' => 2,
                'comment' => 'Great product!',
                'rating' => 5,
                'comment_id' => null,
            ],
            [
                'product_id' => 2,
                'user_id' => 3,
                'comment' => 'Very interesting read.',
                'rating' => 4,
                'comment_id' => null,
            ],
        ];
        foreach ($productComments as $comment) {
            ProductComment::create($comment);
        }

        // Seed carts
        $carts = [
            [
                'user_id' => 2,
                'product_id' => 1,
                'quantity' => 2,
                'total_price' => 1399.98,
            ],
            [
                'user_id' => 3,
                'product_id' => 2,
                'quantity' => 1,
                'total_price' => 19.99,
            ],
        ];
        foreach ($carts as $cart) {
            Cart::create($cart);
        }

        // Seed default payment methods
        $payment_methods = [
            ['name' => 'Cash On Delivery'],
            ['name' => 'Bank'],
            ['name' => 'Gcash'],
        ];
        foreach ($payment_methods as $method) {
            TransactionPaymentMethod::firstOrCreate($method);
        }

        // Seed default transaction status
         $statuses = [
            ['name' => 'Pending'],
            ['name' => 'Confirmed'],
            ['name' => 'Shipped'],
            ['name' => 'Delivered'],
            ['name' => 'Cancelled'],
            ['name' => 'Returned']
        ];
        foreach ($statuses as $status) {
            TransactionStatus::firstOrCreate($status);
        }
        
        // Seed default transaction types
        $types = [
            ['name' => 'Inbound'],
            ['name' => 'Outbound'],
            ['name' => 'Void'],
            ['name' => 'Returned']
        ];
        foreach ($types as $type) {
            TransactionType::firstOrCreate($type);
        }

        // Seed transactions
        $transactions = [
            [
                'user_id' => 1,
                'address_id' => 1,
                'payment_method_id' => 1,
                'type_id' => 1,
                'status_id' => 1,
                'products' => [
                    ['product_id' => 1, 'quantity' => 2],
                    ['product_id' => 2, 'quantity' => 1],
                ],
            ],
        ];

        foreach ($transactions as $transactionData) {
            // Extract products data
            $products = $transactionData['products'];
            unset($transactionData['products']);

            // Create the transaction
            $transaction = Transaction::create($transactionData);

            // Insert products into the pivot table
            $items = [];
            foreach ($products as $product) {
                $productModel = Product::find($product['product_id']);
                if ($productModel) {
                    $items[$product['product_id']] = [
                        'quantity' => $product['quantity'],
                        'price' => $productModel->price,
                        'sub_total' => $productModel->price * $product['quantity'],
                    ];
                }
            }
            $transaction->products()->sync($items);
        }
        
    }
}
