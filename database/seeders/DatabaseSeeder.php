<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductSpecification;
use App\Models\ProductComment;
use App\Models\BannerImage;
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
                    "profile_image" => "assets/media/users/TeamLogo.webp",
                    "first_name" => "Developer",
                    "last_name" => "Six",
                    "contact_number" => "09090909090",
                    "is_admin" => 1,
                ],
                "address" => [ // I tried to create but it fails
                    "name" => "I Don't know what's this??", //please remove if it doesn't need
                    "house_address" => "MFI Building, Ortigas Avenue, Metro Manila",
                    "region" => "NCR",
                    "province" => "null",
                    "city" => "Pasig",
                    "baranggay" => "Ugong",
                    "zip_code" => 1604,
                ]
            ],
            [
                "username" => "CatCodes",
                "email" => "alxrdvno09@gmail.com",
                "password" => bcrypt("password123"),
                "profile" => [
                    "profile_image" => "assets/media/users/Cat_Codes5.png",
                    "first_name" => "Alexander Miguel",
                    "last_name" => "Divino",
                    "contact_number" => "09543549694",
                ],
                "address" => [
                    "name" => "mimimimeeww",
                    "house_address" => "Station Eos-9, Module 42-B, Lagrange Point Alpha (L1) ",
                    "region" => "Orion Arm, Milky Way Galaxy",
                    "province" => "Outer Exosphere",
                    "city" => "Earth Deep Space Zone",
                    "baranggay" => "Interstellar Zone 7",
                    "zip_code" => "OS-00042",
                ],
                //  "transactions" => [
                //     [
                //         "payment_method_id" => 1, // Assuming payment method ID 1 is 'Cash On Delivery'
                //         "type_id" => 1,           // Assuming transaction type ID 1 is 'Inbound'
                //         "status_id" => 1,         // Assuming status ID 1 is 'Pending'
                //         "products" => [
                //             ["product_id" => 5, "quantity" => 1],
                //             ["product_id" => 21, "quantity" => 1],
                //         ],
                //     ]
                // ]

            ],
            [
                "username" => "Cuckoodile",
                "email" => "cuckoodile@gmail.com",
                "password" => bcrypt("password123"),
                "profile" => [
                    "profile_image" => "assets/media/users/cuckoodile.webp",
                    "first_name" => "Lhourde Ian",
                    "last_name" => "Sube",
                    "contact_number" => "09987654321",
                ],
                "address" => [
                    "name" => "Rawr",
                    "house_address" => "Void Citadel, Sector 12-X, Command Tower Zeta",
                    "region" => "Celestial Gateway",
                    "province" => "42.7°N, 118.5°E (Deep Space Quadrant)",
                    "city" => "Andromeda Expanse",
                    "baranggay" => "Deep Space Quadrant Zone 1000",
                    "zip_code" => "VDX-4444",
                ]
            ],

        ];

        // Create roles and permissions
        $roleAdmin = Role::firstOrCreate(["name" => "admin", "guard_name" => "api"]);
        $rolePermissionAdmin = Permission::firstOrCreate(["name" => "Manage All Works", "guard_name" => "api"]);
        $roleAdmin->givePermissionTo($rolePermissionAdmin);

        $roleUser = Role::firstOrCreate(["name" => "user", "guard_name" => "api"]);
        $rolePermissionUser = Permission::firstOrCreate(["name" => "Manage Own Post", "guard_name" => "api"]);
        $roleUser->givePermissionTo($rolePermissionUser);

        foreach ($users as $userData) {
            // Extract profile data separately
            $profileData = $userData['profile'];
            unset($userData['profile']);

            // first is to remove address from user data
            $addressData = $userData['address'];
            unset($userData['address']);

            // Remove transaction too first
            $transactions = $userData['transactions'] ?? [];
            unset($userData['transactions']);

            // Create user in the database
            $user = User::create($userData);

            // Associate the profile
            $user->profile()->create($profileData);

            // Assign role based on is_admin
            $role = $profileData['is_admin'] ?? false ? $roleAdmin : $roleUser;
            $user->assignRole($role);

            $addressData['user_id'] = $user->id;
            Address::create($addressData);
        }

        // Seed categories
        $categories = [
            ['name' => 'Hats'],
            ['name' => 'Necklace'],
            ['name' => 'Pants'],
            ['name' => 'Shirt_2'],
            ['name' => 'Shoes'],
            ['name' => 'Shorts'],
            ['name' => 'Slacks'],
            ['name' => 'Socks'],
            ['name' => 'Sunglasses'],
            ['name' => 'Tshirt_2'],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }


        $banner_images = [
            ['image' => 'assets/media/Hats/hat-beanie-img1.webp'],
            ['image' => 'assets/media/Necklace/necklace-arrow-img1.webp'],
            ['image' => 'assets/media/Pants/pants-cargo-img1.webp'],
            ['image' => 'assets/media/Shirts/shirt-button-img1.webp'],
            ['image' => 'assets/media/Shoes/shoe-casual-img1.webp'],
            ['image' => 'assets/media/Shorts/shorts-beach-img1.webp'],
            ['image' => 'assets/media/Slacks/slacks-classic-img1.webp'],
            ['image' => 'assets/media/Socks/sock-long-img1.webp'],
            ['image' => 'assets/media/Sunglasses/sunglasses-aviator-img1.webp'],
            ['image' => 'assets/media/Tshirt_2/tshirt-crew-img1.webp']
        ];
        foreach ($banner_images as $images) {
            BannerImage::create($images);
        }

        // Seed New Products with Category
        
        $products = [
            [
                "name" => 'Beanie',
                "price" => 99.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Hats/hat-beanie-img1.webp",
                    "assets/media/Hats/hat-beanie-img2.webp",
                    "assets/media/Hats/hat-beanie-img3.webp",
                    "assets/media/Hats/hat-beanie-img4.webp"
                ],
                "description" => 'A stylish knitted beanie to keep you warm.',
                "category_id" => 1,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Nice and cozy!",
                        "rating" => 4,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good quality!",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    "product_id" => 1,
                    ["colors" => "black"],
                    ["style" => "bucket"],
                    ["material" => "wool"],
                ],
            ],
            [
                "name" => 'Bucket',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Hats/hat-bucket-img1.webp",
                    "assets/media/Hats/hat-bucket-img2.webp",
                    "assets/media/Hats/hat-bucket-img3.webp",
                    "assets/media/Hats/hat-bucket-img4.webp",
                    "assets/media/Hats/hat-bucket-img5.webp",
                ],
                "description" => 'A fashionable and cozy knitted bucket hat, perfect for staying warm while looking stylish.',
                "category_id" => 1,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Very comfortable and stylish! I love the texture.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Great quality, but runs a little small for me.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["style" => "bucket"],
                    ["material" => "wool"],
                ],
            ],
            [
                "name" => 'Cabbie Cap',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Hats/cabbie-cap-img1.webp",
                    "assets/media/Hats/cabbie-cap-img2.webp",
                    "assets/media/Hats/cabbie-cap-img3.webp",
                    "assets/media/Hats/cabbie-cap-img4.webp",
                    "assets/media/Hats/cabbie-cap-img5.webp",
                ],
                "description" => 'A classic cabbie cap with a stylish vintage design. Perfect for adding a touch of sophistication to your outfit.',
                "category_id" => 1,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Very stylish and comfortable. Looks great with casual and formal outfits.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Great craftsmanship, though I wish there were more size options.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["style" => "cabbie"],
                    ["material" => "cotton-wool blend"],
                ],
            ],
            [
                "name" => 'Classic Cap',
                "price" => 79.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Hats/hat-cap-img1.webp",
                    "assets/media/Hats/hat-cap-img2.webp",
                    "assets/media/Hats/hat-cap-img3.webp",
                    "assets/media/Hats/hat-cap-img4.webp",
                ],
                "description" => 'A timeless classic cap that combines style and comfort. Perfect for casual outfits and daily wear.',
                "category_id" => 1,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Very comfortable and fits perfectly!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good quality but wish it had more size options.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "blue"],
                    ["style" => "classic cap"],
                    ["material" => "cotton"],
                ],
            ],
            [
                "name" => 'Elegant Fedora',
                "price" => 99.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Hats/hat-fedora-img1.webp",
                    "assets/media/Hats/hat-fedora-img2.webp",
                    "assets/media/Hats/hat-fedora-img3.webp",
                    "assets/media/Hats/hat-fedora-img4.webp",
                    "assets/media/Hats/hat-fedora-img5.webp",
                ],
                "description" => 'A stylish and sophisticated fedora that enhances any outfit. Perfect for formal or casual occasions.',
                "category_id" => 1,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Absolutely love this hat! Great design.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good quality but I prefer a wider brim.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "brown"],
                    ["style" => "fedora"],
                    ["material" => "felt"],
                ],
            ],
            [
                "name" => 'Vintage Newsboy Cap',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Hats/hat-newsboy-cap-img1.webp",
                    "assets/media/Hats/hat-newsboy-cap-img2.webp",
                    "assets/media/Hats/hat-newsboy-cap-img3.webp",
                    "assets/media/Hats/hat-newsboy-cap-img4.webp",
                    "assets/media/Hats/hat-newsboy-cap-img5.webp",
                ],
                "description" => 'A classic newsboy cap with a vintage charm. Adds a touch of old-school elegance to any outfit.',
                "category_id" => 1,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Really stylish and fits great!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good quality but slightly snug for me.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    [
                        "details" => "Made with premium wool blend for a warm and refined feel."
                    ],
                    [
                        "details" => "Classic eight-panel design with a sturdy brim."
                    ],
                    [
                        "details" => "Available in traditional patterns for a vintage aesthetic."
                    ],
                ],
            ],
            [
                "name" => 'Arrow Pendant Necklace',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Necklace/necklace-arrow-img1.webp",
                    "assets/media/Necklace/necklace-arrow-img2.webp",
                    "assets/media/Necklace/necklace-arrow-img3.webp",
                ],
                "description" => 'A sleek and stylish arrow pendant necklace, symbolizing direction and strength. Perfect for everyday wear or as a meaningful gift.',
                "category_id" => 2,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Really elegant and well-crafted. I wear it every day!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice quality! The chain is sturdy and the pendant has a great shine.",
                        "rating" => 5,
                    ],
                ],
                "product_specifications" => [
                    [
                        "details" => "Made with high-quality stainless steel for durability."
                    ],
                    [
                        "details" => "Available in gold, silver, and black finishes."
                    ],
                    [
                        "details" => "Designed with a minimalist arrow symbol for a modern look."
                    ],
                ],
            ],
            [
                "name" => 'Cross Pendant Necklace',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Necklace/necklace-cross-img1.webp",
                    "assets/media/Necklace/necklace-cross-img2.webp",
                    "assets/media/Necklace/necklace-cross-img3.webp",
                ],
                "description" => 'A timeless cross pendant necklace that symbolizes faith and strength. Crafted with precision for a refined and elegant look.',
                "category_id" => 2,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Beautiful design! It feels very meaningful to wear.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice quality! The chain is sturdy, and the pendant has a great shine.",
                        "rating" => 5,
                    ],
                ],
                "product_specifications" => [
                    [
                        "details" => "Made with high-quality stainless steel for durability."
                    ],
                    [
                        "details" => "Available in gold, silver, and black finishes."
                    ],
                    [
                        "details" => "Elegant design with a polished surface for a premium feel."
                    ],
                ],
            ],
            [
                "name" => 'Engraved-Name Necklace',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Necklace/necklace-engraving-img1.webp",
                    "assets/media/Necklace/necklace-engraving-img2.webp",
                    "assets/media/Necklace/necklace-engraving-img3.webp",
                    "assets/media/Necklace/necklace-engraving-img4.webp",
                ],
                "description" => 'A personalized engraved-name necklace, crafted to make a meaningful statement. Perfect as a gift or a stylish keepsake.',
                "category_id" => 2,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Absolutely love the customization! The engraving is clean and well done.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Great quality, but I wish there were more font choices for the engraving.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    [
                        "details" => "Crafted from high-quality stainless steel for long-lasting durability."
                    ],
                    [
                        "details" => "Available in gold, silver, and rose gold finishes."
                    ],
                    [
                        "details" => "Custom engravings available with various fonts and styles."
                    ],
                ],
            ],
            [
                "name" => 'Jade Pendant Necklace',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Necklace/necklace-jade-img1.webp",
                    "assets/media/Necklace/necklace-jade-img2.webp",
                ],
                "description" => 'A beautiful jade pendant necklace, symbolizing serenity and wisdom. Crafted with precision for a refined and elegant look.',
                "category_id" => 2,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Absolutely stunning! The jade color is deep and vibrant.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Feels premium and has a great weight to it.",
                        "rating" => 5,
                    ],
                ],
                "product_specifications" => [
                    [
                        "details" => "Genuine jade stone, carefully polished for a smooth finish."
                    ],
                    [
                        "details" => "Available with gold or silver chain options."
                    ],
                    [
                        "details" => "Designed for everyday elegance or a meaningful gift."
                    ],
                ],
            ],
            [
                "name" => 'Wood Carved Necklace',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Necklace/necklace-wood-img1.webp",
                    "assets/media/Necklace/necklace-wood-img2.webp",
                    "assets/media/Necklace/necklace-wood-img3.webp",
                    "assets/media/Necklace/necklace-wood-img4.webp",
                ],
                "description" => 'A beautifully handcrafted wood carved necklace, blending natural aesthetics with artisan craftsmanship. A perfect accessory for a rustic yet elegant look.',
                "category_id" => 2,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "The craftsmanship is incredible! Love the natural wood texture.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Lightweight and stylish—great for everyday wear.",
                        "rating" => 5,
                    ],
                ],
                "product_specifications" => [
                    [
                        "details" => "Hand-carved from high-quality natural wood."
                    ],
                    [
                        "details" => "Available with adjustable leather or metal chain options."
                    ],
                    [
                        "details" => "Designed for a unique and earthy aesthetic."
                    ],
                ],
            ],
            [
                "name" => 'Cargo Pants',
                "price" => 129.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Pants/pants-cargo-img1.webp",
                    "assets/media/Pants/pants-cargo-img2.webp",
                    "assets/media/Pants/pants-cargo-img3.webp",
                    "assets/media/Pants/pants-cargo-img4.webp",
                ],
                "description" => 'Durable and stylish cargo pants featuring multiple pockets for practicality and a rugged look.',
                "category_id" => 3,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Great fit and plenty of storage space!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Comfortable, but runs slightly big.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "navy"],
                    ["type" => "track"],
                    ["feature" => "breathable"],
                ],
            ],
            [
                "name" => 'Denim Jeans',
                "price" => 99.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Pants/pants-jeans-img1.webp",
                    "assets/media/Pants/pants-jeans-img2.webp",
                    "assets/media/Pants/pants-jeans-img3.webp",
                    "assets/media/Pants/pants-jeans-img4.webp",
                ],
                "description" => 'Classic denim jeans designed for durability and timeless style. Perfect for casual or semi-formal occasions.',
                "category_id" => 3,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "The material is thick and comfortable!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good stretch, fits well.",
                        "rating" => 5,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "blue"],
                    ["type" => "denim"],
                    ["feature" => "distressed"],
                ],
            ],
            [
                "name" => 'Jogger Pants',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Pants/pants-jogger-img1.webp",
                    "assets/media/Pants/pants-jogger-img2.webp",
                    "assets/media/Pants/pants-jogger-img3.webp",
                    "assets/media/Pants/pants-jogger-img4.webp",

                ],
                "description" => 'Versatile jogger pants designed for comfort and an athletic look. Great for workouts or casual lounging.',
                "category_id" => 3,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Super comfortable and easy to move in!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice fabric, though I wish it had more pocket space.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "cycling"],
                    ["feature" => "moisture-wicking"],
                ],
            ],
            [
                "name" => 'Skinny Pants',
                "price" => 109.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Pants/pants-skinny-img1.webp",
                    "assets/media/Pants/pants-skinny-img2.webp",
                ],
                "description" => 'Sleek and modern skinny pants that offer a snug, stylish fit for a trendy look.',
                "category_id" => 3,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Perfect fit! Feels great and looks great.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice stretch, though a bit tight around the calves.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "athletic fit"],
                    ["feature" => "stretchy"],
                ],
            ],
            [
                "name" => 'Track Pants',
                "price" => 79.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Pants/pants-track-img1.webp",
                    "assets/media/Pants/pants-track-img2.webp",
                    "assets/media/Pants/pants-track-img3.webp",
                    "assets/media/Pants/pants-track-img4.webp",
                ],
                "description" => 'Lightweight and breathable track pants designed for performance and style. Perfect for sports or casual wear.',
                "category_id" => 3,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Great for workouts! Super flexible and breathable.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice lightweight material, but could use more pockets.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["type" => "advanced sports"],
                    ["feature" => "compression lining"],
                ],
            ],
            [
                "name" => 'Button-Up Shirt',
                "price" => 79.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shirts/shirt-button-img1.webp",
                    "assets/media/Shirts/shirt-button-img2.webp",
                    "assets/media/Shirts/shirt-button-img3.webp",
                    "assets/media/Shirts/shirt-button-img4.webp",

                ],
                "description" => 'A stylish button-up shirt designed for versatility. Perfect for both casual and formal occasions.',
                "category_id" => 4,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Great fit and the fabric feels premium!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice quality, but could use more color options.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "white"],
                    ["style" => "button-up"],
                    ["material" => "cotton"],
                ],
            ],
            [
                "name" => 'Floral Shirt',
                "price" => 69.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shirts/shirt-floral-img1.webp",
                    "assets/media/Shirts/shirt-floral-img2.webp",
                    "assets/media/Shirts/shirt-floral-img3.webp",
                    "assets/media/Shirts/shirt-floral-img4.webp",
                ],
                "description" => 'A vibrant floral shirt that brings a fresh and stylish touch to any wardrobe. Perfect for a relaxed, tropical vibe.',
                "category_id" => 4,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Love the colors and design! Feels great for summer.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good quality, but I wish the fit was slightly looser.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "floral"],
                    ["pattern" => "floral print"],
                    ["material" => "lightweight cotton"],
                ],
            ],
            [
                "name" => 'Polo Shirt',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shirts/shirt-polo-img1.webp",
                    "assets/media/Shirts/shirt-polo-img2.webp",
                    "assets/media/Shirts/shirt-polo-img3.webp",
                    "assets/media/Shirts/shirt-polo-img4.webp",
                ],
                "description" => 'A classic polo shirt that combines comfort and elegance. Ideal for casual and semi-formal settings.',
                "category_id" => 4,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Super comfortable and stylish!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice material, but a little snug on the shoulders.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "blue"],
                    ["type" => "polo"],
                    ["material" => "pique cotton"],
                ],
            ],
            [
                "name" => 'Long Sleeve Shirt',
                "price" => 99.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shirts/shirt-sleeve-img1.webp",
                    "assets/media/Shirts/shirt-sleeve-img2.webp",
                    "assets/media/Shirts/shirt-sleeve-img3.webp",
                    "assets/media/Shirts/shirt-sleeve-img4.webp",
                ],
                "description" => 'A sleek long sleeve shirt designed for a sophisticated and modern appeal. Perfect for layering or standalone styling.',
                "category_id" => 4,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Great for cooler days! The fit is perfect.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice material, though I prefer a slimmer cut.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["sleeve" => "long"],
                    ["material" => "cotton blend"],
                ],
            ],
            [
                "name" => 'Casual Shoes',
                "price" => 79.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shoes/shoe-casual-img1.webp",
                    "assets/media/Shoes/shoe-casual-img2.webp",
                    "assets/media/Shoes/shoe-casual-img3.webp",
                ],
                "description" => 'Comfortable and stylish casual shoes designed for everyday wear. Perfect for work, outings, or relaxed occasions.',
                "category_id" => 5,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Very comfortable and looks great with jeans!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good quality, but could use more color options.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["type" => "casual"],
                    ["material" => "canvas"],
                ],
            ],
            [
                "name" => 'Hiking Shoes',
                "price" => 129.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shoes/shoe-hiking-img1.webp",
                    "assets/media/Shoes/shoe-hiking-img2.webp",
                ],
                "description" => 'Durable hiking shoes designed for rugged terrain. Provides superior grip and protection for outdoor adventures.',
                "category_id" => 5,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Great grip and waterproof! Perfect for hikes.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Very sturdy, though a bit heavy.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "brown"],
                    ["type" => "hiking"],
                    ["feature" => "water-resistant"],
                ],
            ],
            [
                "name" => 'Leather Shoes',
                "price" => 149.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shoes/shoe-leather-img1.webp",
                    "assets/media/Shoes/shoe-leather-img2.webp",
                ],
                "description" => 'Elegant leather shoes crafted for sophistication. Ideal for formal occasions and professional wear.',
                "category_id" => 5,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Fantastic quality! Looks classy and feels premium.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice shine, but takes time to break in.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["material" => "leather"],
                    ["feature" => "slip-resistant"],
                ],
            ],
            [
                "name" => 'Rubber Shoes',
                "price" => 99.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shoes/shoe-rubber-img1.webp",
                    "assets/media/Shoes/shoe-rubber-img2.webp",
                    "assets/media/Shoes/shoe-rubber-img3.webp",
                ],
                "description" => 'Lightweight and flexible rubber shoes designed for active use. Ideal for workouts or casual wear.',
                "category_id" => 5,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Super comfortable and great for running!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice grip, but I prefer more arch support.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "blue"],
                    ["type" => "rubber"],
                    ["feature" => "shock-absorbing"],
                ],
            ],
            [
                "name" => 'Sneakers',
                "price" => 119.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shoes/shoe-sneakers-img1.webp",
                    "assets/media/Shoes/shoe-sneakers-img2.webp",
                    "assets/media/Shoes/shoe-sneakers-img3.webp",
                    "assets/media/Shoes/shoe-sneakers-img4.webp",
                ],
                "description" => 'Trendy and stylish sneakers designed for casual and sporty fashion. Perfect for everyday wear.',
                "category_id" => 5,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Looks amazing! Very lightweight and breathable.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice style, but runs a little small.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "white"],
                    ["type" => "sneakers"],
                    ["feature" => "cushioned"],
                ],
            ],
            [
                "name" => 'Soccer Shoes',
                "price" => 139.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shoes/shoe-soccer-img1.webp",
                    "assets/media/Shoes/shoe-soccer-img2.webp",
                    "assets/media/Shoes/shoe-soccer-img3.webp",
                ],
                "description" => 'High-performance soccer shoes built for speed and precision. Engineered for maximum grip and control on the field.',
                "category_id" => 5,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Excellent grip! Feels great during matches.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice design, but needs more cushioning.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "red"],
                    ["type" => "soccer"],
                    ["feature" => "textured sole"],
                ],
            ],
            [
                "name" => 'Beach Shorts',
                "price" => 59.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shorts/shorts-beach-img1.webp",
                    "assets/media/Shorts/shorts-beach-img2.webp",
                    "assets/media/Shorts/shorts-beach-img3.webp",
                    "assets/media/Shorts/shorts-beach-img4.webp",
                ],
                "description" => 'Lightweight and quick-drying beach shorts designed for maximum comfort while enjoying the sun and waves.',
                "category_id" => 6,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Perfect for the beach! Comfortable and stylish.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Dries fast, but wish it had more pocket space.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "blue"],
                    ["type" => "beach"],
                    ["feature" => "quick-dry"],
                ],
            ],
            [
                "name" => 'Bermuda Shorts',
                "price" => 69.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shorts/shorts-bermuda-img1.webp",
                    "assets/media/Shorts/shorts-bermuda-img2.webp",
                ],
                "description" => 'Classic Bermuda shorts with a smart, tailored design. Perfect for casual yet refined outfits.',
                "category_id" => 6,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Great fit and very comfortable!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Stylish, but could use more color options.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "khaki"],
                    ["type" => "bermuda"],
                    ["feature" => "tailored"],
                ],
            ],
            [
                "name" => 'Cargo Shorts',
                "price" => 79.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shorts/shorts-cargo-img1.webp",
                    "assets/media/Shorts/shorts-cargo-img2.webp",
                    "assets/media/Shorts/shorts-cargo-img3.webp",
                    "assets/media/Shorts/shorts-cargo-img4.webp",
                ],
                "description" => 'Durable and practical cargo shorts with multiple pockets for functionality and a rugged look.',
                "category_id" => 6,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Super useful and comfortable!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice design, but a bit bulky.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "green"],
                    ["type" => "cargo"],
                    ["feature" => "multi-pocket"],
                ],
            ],
            [
                "name" => 'Casual Shorts',
                "price" => 49.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shorts/shorts-casual-img1.webp",
                    "assets/media/Shorts/shorts-casual-img2.webp",
                    "assets/media/Shorts/shorts-casual-img3.webp",
                    "assets/media/Shorts/shorts-casual-img4.webp",
                ],
                "description" => 'Versatile and stylish casual shorts, perfect for relaxed outings or everyday wear.',
                "category_id" => 6,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Nice fit and very breathable!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Soft fabric, but runs a little small.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["type" => "casual"],
                    ["feature" => "breathable"],
                ],
            ],
            [
                "name" => 'Cycling Shorts',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shorts/shorts-cycling-img1.webp",
                    "assets/media/Shorts/shorts-cycling-img2.webp",
                    "assets/media/Shorts/shorts-cycling-img3.webp",
                    "assets/media/Shorts/shorts-cycling-img4.webp",
                ],
                "description" => 'Performance cycling shorts designed for flexibility and comfort during intense rides.',
                "category_id" => 6,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Perfect for long rides! Great compression.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice stretch, but could use more padding.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "cycling"],
                    ["feature" => "moisture-wicking"],
                ],
            ],
            [
                "name" => 'Denim Shorts',
                "price" => 69.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shorts/shorts-denim-img1.webp",
                    "assets/media/Shorts/shorts-denim-img2.webp",
                ],
                "description" => 'Classic denim shorts with a stylish and casual appeal, perfect for everyday wear.',
                "category_id" => 6,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Love the distressed look! Feels comfortable.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice fit, but could be stretchier.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "blue"],
                    ["type" => "denim"],
                    ["feature" => "distressed"],
                ],
            ],
            [
                "name" => 'Sports Shorts',
                "price" => 59.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shorts/shorts-sports-img1.webp",
                    "assets/media/Shorts/shorts-sports-img2.webp",
                    "assets/media/Shorts/shorts-sports-img3.webp",
                    "assets/media/Shorts/shorts-sports-img4.webp",
                ],
                "description" => 'Lightweight and breathable sports shorts designed for performance and comfort during workouts and training sessions.',
                "category_id" => 6,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Super comfortable and great for running!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice fit, but could use more pocket space.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "sports"],
                    ["feature" => "sweat control"],
                ],
            ],
            [
                "name" => 'Advanced Sports Shorts',
                "price" => 69.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Shorts/shorts-sports2-img1.webp",
                    "assets/media/Shorts/shorts-sports2-img2.webp",
                    "assets/media/Shorts/shorts-sports2-img3.webp",
                    "assets/media/Shorts/shorts-sports2-img4.webp",
                ],
                "description" => 'High-performance sports shorts optimized for professional training and intense physical activity.',
                "category_id" => 6,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Perfect for intense workouts! Feels super lightweight.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good stretch and mobility, but I prefer a slightly longer fit.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["type" => "advanced sports"],
                    ["feature" => "compression lining"],
                ],
            ],
            [
                "name" => 'Classic Slacks',
                "price" => 99.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Slacks/slacks-classic-img1.webp",
                    "assets/media/Slacks/slacks-classic-img2.webp",
                    "assets/media/Slacks/slacks-classic-img3.webp",
                ],
                "description" => 'Timeless classic slacks with a sleek and polished design, perfect for professional and formal occasions.',
                "category_id" => 7,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Super stylish and comfortable! Perfect for office wear.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice fit, but I prefer a slightly slimmer cut.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "navy"],
                    ["type" => "classic"],
                    ["feature" => "cotton-blend"],
                ],
            ],
            [
                "name" => 'Elastic-Waist Slacks',
                "price" => 89.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Slacks/slacks-elastic-img1.webp",
                    "assets/media/Slacks/slacks-elastic-img2.webp",
                    "assets/media/Slacks/slacks-elastic-img3.webp",
                    "assets/media/Slacks/slacks-elastic-img4.webp",
                ],
                "description" => 'Comfortable elastic-waist slacks designed for easy wear and movement, ideal for casual and office settings.',
                "category_id" => 7,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Very comfortable! Great for all-day wear.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice stretch, but I prefer a more structured waistband.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "elastic-waist"],
                    ["feature" => "stretch"],
                ],
            ],
            [
                "name" => 'Flat-Front Slacks',
                "price" => 109.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Slacks/slacks-flat-img1.webp",
                    "assets/media/Slacks/slacks-flat-img2.webp",
                    "assets/media/Slacks/slacks-flat-img3.webp",
                    "assets/media/Slacks/slacks-flat-img4.webp",
                ],
                "description" => 'Modern flat-front slacks with a sleek and tailored finish. Ideal for sharp, sophisticated dressing.',
                "category_id" => 7,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Love the clean look! Feels premium.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice material, but could use a little more stretch.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["type" => "flat-front"],
                    ["feature" => "wrinkle-resistant"],
                ],
            ],
            [
                "name" => 'Pleated Slacks',
                "price" => 119.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Slacks/slacks-pleated-img1.webp",
                    "assets/media/Slacks/slacks-pleated-img2.webp",
                    "assets/media/Slacks/slacks-pleated-img3.webp",
                    "assets/media/Slacks/slacks-pleated-img4.webp",
                ],
                "description" => 'Elegant pleated slacks designed for a classic and refined look. Provides extra flexibility for comfort.',
                "category_id" => 7,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Perfect for formal occasions! Great fit.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice design, but runs slightly loose for me.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "pleated"],
                    ["feature" => "classic"],
                ],
            ],
            [
                "name" => 'Wide-Leg Slacks',
                "price" => 129.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Slacks/slacks-wide-img1.webp",
                    "assets/media/Slacks/slacks-wide-img2.webp",
                    "assets/media/Slacks/slacks-wide-img3.webp",
                    "assets/media/Slacks/slacks-wide-img4.webp",
                ],
                "description" => 'Stylish wide-leg slacks designed for a relaxed yet sophisticated look. Ideal for casual elegance.',
                "category_id" => 7,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Super comfortable! Love the wide-leg flow.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice design, but I prefer a more tapered cut.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "beige"],
                    ["type" => "wide-leg"],
                    ["feature" => "relaxed"],
                ],
            ],
            [
                "name" => 'Long Socks',
                "price" => 19.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Socks/sock-long-img1.webp",
                    "assets/media/Socks/sock-long-img2.webp",
                ],
                "description" => 'Comfortable and stylish long socks designed for extra coverage and warmth. Great for casual and formal wear.',
                "category_id" => 8,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Super soft and keeps my legs warm!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice fit, but could use more stretch.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["length" => "long"],
                    ["feature" => "reinforced toe"],
                ],
            ],
            [
                "name" => 'Medium Socks',
                "price" => 14.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Socks/sock-medium-img1.webp",
                    "assets/media/Socks/sock-medium-img2.webp",
                ],
                "description" => 'Versatile medium-length socks that provide a balance of comfort and style. Ideal for everyday wear.',
                "category_id" => 8,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Perfect length! Feels great for casual wear.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice material, but I prefer a tighter fit.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "white"],
                    ["length" => "medium"],
                    ["feature" => "stretchable"],
                ],
            ],
            [
                "name" => 'Short Socks',
                "price" => 12.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Socks/sock-short-img1.webp",
                    "assets/media/Socks/sock-short-img2.webp",
                ],
                "description" => 'Low-cut short socks designed for comfort and minimal visibility. Perfect for sneakers and casual shoes.',
                "category_id" => 8,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Great for sneakers! Stays in place all day.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice quality, but could be softer.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["length" => "short"],
                    ["feature" => "no-show"],
                ],
            ],
            [
                "name" => 'Aviator Sunglasses',
                "price" => 129.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Sunglasses/sunglasses-aviator-img1.webp",
                    "assets/media/Sunglasses/sunglasses-aviator-img2.webp",
                    "assets/media/Sunglasses/sunglasses-aviator-img3.webp",
                    "assets/media/Sunglasses/sunglasses-aviator-img4.webp",
                ],
                "description" => 'Classic aviator sunglasses with a timeless design. Perfect for stylish protection under the sun.',
                "category_id" => 9,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Great quality! Love the sleek design.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Fits well, but wish it had more tint options.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gold"],
                    ["frame" => "metal"],
                    ["feature" => "uv protection"],
                ],
            ],
            [
                "name" => 'Cyclist Sunglasses',
                "price" => 149.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Sunglasses/sunglasses-cyclist-img1.webp",
                    "assets/media/Sunglasses/sunglasses-cyclist-img2.webp",
                    "assets/media/Sunglasses/sunglasses-cyclist-img3.webp",
                    "assets/media/Sunglasses/sunglasses-cyclist-img4.webp",
                ],
                "description" => 'High-performance cyclist sunglasses with aerodynamic design and anti-glare protection.',
                "category_id" => 9,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Perfect for cycling! Keeps glare away.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice wraparound design, but could be lighter.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "cyclist"],
                    ["feature" => "anti-fog"],
                ],
            ],
            [
                "name" => 'Polarized Sunglasses',
                "price" => 159.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Sunglasses/sunglasses-polarized-img1.webp",
                    "assets/media/Sunglasses/sunglasses-polarized-img2.webp",
                    "assets/media/Sunglasses/sunglasses-polarized-img3.webp",
                    "assets/media/Sunglasses/sunglasses-polarized-img4.webp",
                ],
                "description" => 'Polarized sunglasses designed to reduce glare and improve vision clarity in bright conditions.',
                "category_id" => 9,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Best sunglasses for reducing glare!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Love the clarity, but the fit is slightly snug.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["type" => "polarized"],
                    ["feature" => "uv protection"],
                ],
            ],
            [
                "name" => 'Protective Sunglasses',
                "price" => 139.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Sunglasses/sunglasses-protective-img1.webp",
                    "assets/media/Sunglasses/sunglasses-protective-img2.webp",
                    "assets/media/Sunglasses/sunglasses-protective-img3.webp",
                    "assets/media/Sunglasses/sunglasses-protective-img4.webp",
                ],
                "description" => 'High-protection sunglasses designed for extreme outdoor conditions, shielding against dust and UV rays.',
                "category_id" => 9,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Great for outdoor sports! Feels sturdy.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice coverage, but could use more ventilation.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "protective"],
                    ["feature" => "scratch-resistant"],
                ],
            ],
            [
                "name" => 'Reading Sunglasses',
                "price" => 119.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Sunglasses/sunglasses-reading-img1.webp",
                    "assets/media/Sunglasses/sunglasses-reading-img2.webp",
                    "assets/media/Sunglasses/sunglasses-reading-img3.webp",
                    "assets/media/Sunglasses/sunglasses-reading-img4.webp",
                ],
                "description" => 'Stylish reading sunglasses with magnified lenses for clear outdoor reading.',
                "category_id" => 9,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Very useful for reading outdoors!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice lens clarity, but wish there were more magnification options.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "brown"],
                    ["type" => "reading"],
                    ["feature" => "magnified"],
                ],
            ],
            [
                "name" => 'Round Sunglasses',
                "price" => 109.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Sunglasses/sunglasses-round-img1.webp",
                    "assets/media/Sunglasses/sunglasses-round-img2.webp",
                    "assets/media/Sunglasses/sunglasses-round-img3.webp",
                    "assets/media/Sunglasses/sunglasses-round-img4.webp",
                ],
                "description" => 'Trendy round sunglasses with a vintage-inspired design. Perfect for a stylish statement.',
                "category_id" => 9,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Love the retro look! Fits great.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Nice lightweight frame, but could be sturdier.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "round"],
                    ["feature" => "uv protection"],
                ],
            ],
            [
                "name" => 'Transition Sunglasses',
                "price" => 169.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Sunglasses/sunglasses-transition-img1.webp",
                    "assets/media/Sunglasses/sunglasses-transition-img2.webp",
                    "assets/media/Sunglasses/sunglasses-transition-img3.webp",
                    "assets/media/Sunglasses/sunglasses-transition-img4.webp",
                ],
                "description" => 'Adaptive transition sunglasses that adjust lens tint based on lighting conditions for optimal visibility.',
                "category_id" => 9,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Amazing technology! Adjusts quickly.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Works great, but takes a second to transition.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["type" => "transition"],
                    ["feature" => "adaptive tint"],
                ],
            ],
            [
                "name" => 'Crew Neck T-shirt',
                "price" => 39.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Tshirt/tshirt-crew-img1.webp",
                    "assets/media/Tshirt/tshirt-crew-img2.webp",
                    "assets/media/Tshirt/tshirt-crew-img3.webp",
                    "assets/media/Tshirt/tshirt-crew-img4.webp",
                ],
                "description" => 'A classic crew neck T-shirt designed for everyday comfort and effortless style.',
                "category_id" => 10,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Super soft and fits perfectly!",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good quality, but runs slightly large.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "white"],
                    ["type" => "crew neck"],
                    ["feature" => "cotton"],
                ],
            ],
            [
                "name" => 'Dry-Fit T-shirt',
                "price" => 49.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Tshirt/tshirt-dry-img1.webp",
                    "assets/media/Tshirt/tshirt-dry-img2.webp",
                    "assets/media/Tshirt/tshirt-dry-img3.webp",
                    "assets/media/Tshirt/tshirt-dry-img4.webp",
                ],
                "description" => 'Moisture-wicking dry-fit T-shirt designed to keep you cool and dry during workouts and outdoor activities.',
                "category_id" => 10,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Perfect for workouts! Keeps sweat under control.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good material, but wish it had more color options.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "gray"],
                    ["type" => "dry-fit"],
                    ["feature" => "moisture-wicking"],
                ],
            ],
            [
                "name" => 'Athletic Fit T-shirt',
                "price" => 44.99,
                "admin_id" => 1,
                "product_image" => [
                    "assets/media/Tshirt/tshirt-fit-img1.webp",
                    "assets/media/Tshirt/tshirt-fit-img2.webp",
                    "assets/media/Tshirt/tshirt-fit-img3.webp",
                    "assets/media/Tshirt/tshirt-fit-img4.webp",
                ],
                "description" => 'A form-fitting athletic T-shirt tailored for a sleek and sculpted look, perfect for casual or sportswear.',
                "category_id" => 10,
                "product_comments" => [
                    [
                        "user_id" => 2,
                        "comment" => "Love the fit! Makes me look great.",
                        "rating" => 5,
                    ],
                    [
                        "user_id" => 3,
                        "comment" => "Good stretch, but could be a bit longer.",
                        "rating" => 4,
                    ],
                ],
                "product_specifications" => [
                    ["color" => "black"],
                    ["type" => "athletic fit"],
                    ["feature" => "stretchy"],
                ],
            ]
        ];

        foreach ($products as $index => $productData) {
            $comments = $productData['product_comments'] ?? [];
            $specs = $productData['product_specifications'] ?? [];
            unset($productData['product_comments'], $productData['product_specifications']);

            $product = Product::create($productData);

            // Create comments
            foreach ($comments as $comment) {
                $comment['product_id'] = $product->id;
                ProductComment::create($comment);
            }

            // Create specifications (add wrapper as requested)
            if (!empty($specs)) {
                ProductSpecification::create([
                    'product_id' => $product->id,
                    'details' => $specs,
                ]);
            }
        }


        // Seed carts
        $carts = [
            [
                'user_id' => 2,
                'product_id' => 21,
                'quantity' => 1,
            ],
            [
                'user_id' => 1,
                'product_id' => 13,
                'quantity' => 10,
            ],
            [
                'user_id' => 1,
                'product_id' => 21,
                'quantity' => 26,
            ],
        ];
        foreach ($carts as $cart) {
            Cart::create($cart);
        }

        // Seed default payment methods
        $payment_methods = [
            ['name' => 'Cash On Delivery'],
            ['name' => 'Gcash'],
            ['name' => 'PayMaya'],
            ['name' => 'BDO'],
            ['name' => 'Cebuana'],
        ];
        foreach ($payment_methods as $method) {
            TransactionPaymentMethod::create($method);
        }

        //  Seed default transaction status
        $statuses = [
            ['name' => 'Pending'],
            ['name' => 'Shipping'],
            ['name' => 'Shipped'],
            ['name' => 'On local hub'],
            ['name' => 'Received'],
            ['name' => 'Cancelled'],
            ['name' => 'Returned']
        ];
        foreach ($statuses as $status) {
            TransactionStatus::create($status);
        }

        // Seed default transaction types
        $types = [
            ['name' => 'Inbound'],
            ['name' => 'Outbound'],
            ['name' => 'Void'],
            ['name' => 'Returned']
        ];
        foreach ($types as $type) {
            TransactionType::create($type);
        }

        // In-memory stock tracking for each product
        $productStocks = Product::all()->pluck('id')->mapWithKeys(function ($id) {
            return [$id => 0];
        })->toArray();

        //  Seed transactions using factory
        $startDate = '2023-06-22 00:00:00';
        $endDate = now();
        Transaction::factory(1000)->make()->each(function ($transaction) use (&$productStocks, $startDate, $endDate) {
            // Generate random created_at/updated_at within range
            $createdAt = fake()->dateTimeBetween($startDate, $endDate);
            $transaction->created_at = $createdAt;
            $transaction->updated_at = $createdAt;
            $transaction->save();

            $products = Product::inRandomOrder()->limit(rand(2, 4))->get();
            foreach ($products as $product) {
                $productId = $product->id;
                $currentStock = $productStocks[$productId] ?? 0;

                // Only allow outbound if stock is available, otherwise always inbound
                $typeId = 1; // Default to inbound
                // Increase inbound quantity range for higher stock
                $inboundMin = 20;
                $inboundMax = 100;
                $outboundMax = 15;
                $maxQuantity = $inboundMax;
                if ($currentStock > 0) {
                    $typeId = [1, 2][rand(0, 1)];
                    if ($typeId === 2) {
                        $maxQuantity = min($currentStock, $outboundMax);
                        if ($maxQuantity <= 0) {
                            $typeId = 1;
                            $maxQuantity = $inboundMax;
                        }
                    } else {
                        $maxQuantity = $inboundMax;
                    }
                }
                $quantity = $typeId === 1 ? rand($inboundMin, $maxQuantity) : rand(1, $maxQuantity);
                $price = $product->price;
                $subTotal = $quantity * $price;

                // Update in-memory stock
                if ($typeId === 1) {
                    $productStocks[$productId] += $quantity;
                } else {
                    $productStocks[$productId] -= $quantity;
                }

                // Create the transaction-product relation with the correct type
                $transaction->type_id = $typeId;
                $transaction->save();
                $transaction->products()->attach($productId, [
                    'quantity' => $quantity,
                    'price' => $price,
                    'sub_total' => $subTotal,
                ]);
            }
        });
    }
}
