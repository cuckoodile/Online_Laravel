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
                "username" => "DEVSIX",
                "email" => "devsix@gmail.com",
                "password" => bcrypt("devsixtech"),
                "profile" => [
                    "profile_image" => "assets/media/users/TeamLogo.webp",
                    "first_name" => "Developer",
                    "last_name" => "Six",
                    "contact_number" => "09090909090",
                    "is_admin" => 1,
                ],
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
                    [
                        "details" => "Made with 100% wool"
                    ],
                    [
                        "details" => "Available in multiple colors"
                    ],
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
                    [
                        "details" => "Made with 100% premium wool for superior warmth."
                    ],
                    [
                        "details" => "Available in a variety of colors to match any style."
                    ],
                    [
                        "details" => "Lightweight yet warm, ideal for chilly days."
                    ],
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
                    [
                        "details" => "Made with high-quality cotton and wool blend for durability and comfort."
                    ],
                    [
                        "details" => "Available in multiple colors to suit different styles."
                    ],
                    [
                        "details" => "Lightweight and breathable, great for all seasons."
                    ],
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
                    [
                        "details" => "Made with durable cotton for a soft yet sturdy feel."
                    ],
                    [
                        "details" => "Adjustable strap for a customized fit."
                    ],
                    [
                        "details" => "Available in various colors to suit any look."
                    ],
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
                    [
                        "details" => "Made with high-quality felt for a premium look."
                    ],
                    [
                        "details" => "Classic structured design with a soft inner lining."
                    ],
                    [
                        "details" => "Available in neutral tones for versatile styling."
                    ],
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
                    [
                        "details" => "Made with high-quality cotton blend for durability."
                    ],
                    [
                        "details" => "Multiple pockets for practical storage."
                    ],
                    [
                        "details" => "Available in classic neutral tones."
                    ],
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
                    [
                        "details" => "Made from premium denim for durability."
                    ],
                    [
                        "details" => "Available in slim, straight, and relaxed fits."
                    ],
                    [
                        "details" => "Classic washes ranging from light to dark denim."
                    ],
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
                    [
                        "details" => "Made with soft and breathable fabric."
                    ],
                    [
                        "details" => "Elastic waistband with drawstring for a secure fit."
                    ],
                    [
                        "details" => "Available in various sporty colors."
                    ],
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
                    [
                        "details" => "Made with stretchy fabric for comfort."
                    ],
                    [
                        "details" => "High-waisted and mid-rise options available."
                    ],
                    [
                        "details" => "Available in classic neutral colors."
                    ],
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
                    [
                        "details" => "Moisture-wicking fabric for maximum comfort."
                    ],
                    [
                        "details" => "Elastic cuffs for a secure fit."
                    ],
                    [
                        "details" => "Available in sporty designs and colors."
                    ],
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
                    [
                        "details" => "Made with breathable cotton blend."
                    ],
                    [
                        "details" => "Available in solid and patterned designs."
                    ],
                    [
                        "details" => "Classic button-down collar for a polished look."
                    ],
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
                    [
                        "details" => "Made with lightweight and breathable fabric."
                    ],
                    [
                        "details" => "Available in bold and subtle floral prints."
                    ],
                    [
                        "details" => "Designed for casual and vacation wear."
                    ],
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
                    [
                        "details" => "Made with soft and breathable cotton."
                    ],
                    [
                        "details" => "Available in solid colors and striped designs."
                    ],
                    [
                        "details" => "Features a ribbed collar for a refined look."
                    ],
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
                    [
                        "details" => "Made with high-quality fabric for warmth and comfort."
                    ],
                    [
                        "details" => "Available in solid and textured patterns."
                    ],
                    [
                        "details" => "Designed for a sleek and modern style."
                    ],
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
                    [
                        "details" => "Made with breathable fabric for all-day comfort."
                    ],
                    [
                        "details" => "Available in neutral and bold colors."
                    ],
                    [
                        "details" => "Lightweight sole for easy movement."
                    ],
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
                    [
                        "details" => "Made with reinforced rubber soles for better traction."
                    ],
                    [
                        "details" => "Water-resistant design for all-weather hiking."
                    ],
                    [
                        "details" => "Padded interior for maximum comfort."
                    ],
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
                    [
                        "details" => "Genuine leather for long-lasting durability."
                    ],
                    [
                        "details" => "Available in polished black and brown shades."
                    ],
                    [
                        "details" => "Slip-resistant sole for added stability."
                    ],
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
                    [
                        "details" => "Made with high-quality rubber for flexibility."
                    ],
                    [
                        "details" => "Available in various sporty designs."
                    ],
                    [
                        "details" => "Shock-absorbing soles for extra comfort."
                    ],
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
                    [
                        "details" => "Made with soft mesh material for breathability."
                    ],
                    [
                        "details" => "Available in various trendy color combinations."
                    ],
                    [
                        "details" => "Cushioned soles for all-day comfort."
                    ],
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
                    [
                        "details" => "Textured sole for enhanced ball control."
                    ],
                    [
                        "details" => "Available in vibrant team colors."
                    ],
                    [
                        "details" => "Lightweight and durable synthetic material."
                    ],
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
                    [
                        "details" => "Made with breathable and water-resistant fabric."
                    ],
                    [
                        "details" => "Elastic waistband with adjustable drawstring."
                    ],
                    [
                        "details" => "Available in vibrant summer patterns."
                    ],
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
                    [
                        "details" => "Made with high-quality cotton blend."
                    ],
                    [
                        "details" => "Available in neutral and vibrant colors."
                    ],
                    [
                        "details" => "Designed for casual and semi-formal wear."
                    ],
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
                    [
                        "details" => "Made with sturdy cotton twill fabric."
                    ],
                    [
                        "details" => "Multiple pockets for practical storage."
                    ],
                    [
                        "details" => "Available in earthy and military tones."
                    ],
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
                    [
                        "details" => "Made with lightweight cotton blend."
                    ],
                    [
                        "details" => "Designed for maximum comfort."
                    ],
                    [
                        "details" => "Available in solid colors and striped designs."
                    ],
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
                    [
                        "details" => "Made with moisture-wicking fabric."
                    ],
                    [
                        "details" => "Designed for aerodynamics and comfort."
                    ],
                    [
                        "details" => "Available in professional-grade materials."
                    ],
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
                    [
                        "details" => "Made with premium denim fabric."
                    ],
                    [
                        "details" => "Available in faded, ripped, and classic designs."
                    ],
                    [
                        "details" => "Structured stitching for durability."
                    ],
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
                    [
                        "details" => "Made with moisture-wicking fabric for sweat control."
                    ],
                    [
                        "details" => "Elastic waistband with adjustable drawstring."
                    ],
                    [
                        "details" => "Available in various sporty designs and colors."
                    ],
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
                    [
                        "details" => "Made with ultra-breathable mesh fabric."
                    ],
                    [
                        "details" => "Compression lining for enhanced support."
                    ],
                    [
                        "details" => "Available in premium athletic colors and styles."
                    ],
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
                    [
                        "details" => "Made from premium cotton-blend fabric."
                    ],
                    [
                        "details" => "Available in neutral and formal colors."
                    ],
                    [
                        "details" => "Designed for a sleek and structured look."
                    ],
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
                    [
                        "details" => "Made with soft and breathable fabric."
                    ],
                    [
                        "details" => "Flexible elastic waistband for added comfort."
                    ],
                    [
                        "details" => "Available in various professional colors."
                    ],
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
                    [
                        "details" => "Made from high-quality wrinkle-resistant fabric."
                    ],
                    [
                        "details" => "Designed for a slim and structured appearance."
                    ],
                    [
                        "details" => "Available in standard formal colors."
                    ],
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
                    [
                        "details" => "Made with structured fabric for a sharp look."
                    ],
                    [
                        "details" => "Designed with classic front pleats."
                    ],
                    [
                        "details" => "Available in business and formal shades."
                    ],
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
                    [
                        "details" => "Made with soft and breathable fabric."
                    ],
                    [
                        "details" => "Wide-leg cut for relaxed movement."
                    ],
                    [
                        "details" => "Available in chic and contemporary colors."
                    ],
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
                    [
                        "details" => "Made with breathable cotton blend."
                    ],
                    [
                        "details" => "Available in solid colors and patterns."
                    ],
                    [
                        "details" => "Reinforced toe and heel for durability."
                    ],
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
                    [
                        "details" => "Soft and stretchable fabric for snug comfort."
                    ],
                    [
                        "details" => "Designed for casual and athletic use."
                    ],
                    [
                        "details" => "Available in multiple color options."
                    ],
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
                    [
                        "details" => "Made with lightweight and breathable material."
                    ],
                    [
                        "details" => "No-show design for a sleek look."
                    ],
                    [
                        "details" => "Available in basic and colorful styles."
                    ],
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
                    [
                        "details" => "Made with high-quality metal frame for durability."
                    ],
                    [
                        "details" => "UV protection lenses for maximum eye safety."
                    ],
                    [
                        "details" => "Available in gold, silver, and black frames."
                    ],
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
                    [
                        "details" => "Made with lightweight, impact-resistant lenses."
                    ],
                    [
                        "details" => "Anti-fog and UV protection features."
                    ],
                    [
                        "details" => "Adjustable nose pads for comfortable wear."
                    ],
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
                    [
                        "details" => "Made with premium polarized lenses."
                    ],
                    [
                        "details" => "Designed for maximum UV protection."
                    ],
                    [
                        "details" => "Available in various stylish frame designs."
                    ],
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
                    [
                        "details" => "Made with scratch-resistant protective lenses."
                    ],
                    [
                        "details" => "Wraparound design for complete eye coverage."
                    ],
                    [
                        "details" => "Available in rugged, sports-friendly styles."
                    ],
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
                    [
                        "details" => "Made with scratch-resistant lenses."
                    ],
                    [
                        "details" => "Available in various magnification levels."
                    ],
                    [
                        "details" => "Comfortable fit for prolonged reading sessions."
                    ],
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
                    [
                        "details" => "Made with UV protection lenses."
                    ],
                    [
                        "details" => "Available in vintage gold and black frames."
                    ],
                    [
                        "details" => "Designed for a fashionable and bold look."
                    ],
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
                    [
                        "details" => "Made with intelligent transition lenses."
                    ],
                    [
                        "details" => "UV protection and anti-glare coating."
                    ],
                    [
                        "details" => "Available in modern stylish frames."
                    ],
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
                    [
                        "details" => "Made with breathable cotton fabric."
                    ],
                    [
                        "details" => "Available in solid and printed designs."
                    ],
                    [
                        "details" => "Designed for a relaxed and comfortable fit."
                    ],
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
                    [
                        "details" => "Made with lightweight and moisture-wicking fabric."
                    ],
                    [
                        "details" => "Designed for sports and active wear."
                    ],
                    [
                        "details" => "Available in sleek athletic colors."
                    ],
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
                    [
                        "details" => "Made with soft and stretchy performance fabric."
                    ],
                    [
                        "details" => "Designed for a slim and athletic silhouette."
                    ],
                    [
                        "details" => "Available in multiple versatile colors."
                    ],
                ],
            ]

        ];

        foreach ($products as $productData) {
            $comments = $productData['product_comments'] ?? [];
            $specs = $productData['product_specifications'] ?? [];
            unset($productData['product_comments'], $productData['product_specifications']);

            $product = Product::create($productData);

            // Create comments
            foreach ($comments as $comment) {
                $comment['product_id'] = $product->id;
                ProductComment::create($comment);
            }

            // Create specifications
            foreach ($specs as $spec) {
                $spec['product_id'] = $product->id;
                ProductSpecification::create($spec);
            }
        }


//         // Seed carts
//         $carts = [
//             [
//                 'user_id' => 1,
//                 'product_id' => 1,
//                 'quantity' => 2,
//                 'total_price' => 1399.98,
//             ],
//             [
//                 'user_id' => 3,
//                 'product_id' => 2,
//                 'quantity' => 1,
//                 'total_price' => 19.99,
//             ],
//         ];
//         foreach ($carts as $cart) {
//             Cart::create($cart);
//         }

//         // Seed default payment methods
//         $payment_methods = [
//             ['name' => 'Cash On Delivery'],
//             ['name' => 'Bank'],
//             ['name' => 'Gcash'],
//         ];
//         foreach ($payment_methods as $method) {
//             TransactionPaymentMethod::firstOrCreate($method);
//         }

//         // Seed default transaction status
//          $statuses = [
//             ['name' => 'Pending'],
//             ['name' => 'Confirmed'],
//             ['name' => 'Shipped'],
//             ['name' => 'Delivered'],
//             ['name' => 'Cancelled'],
//             ['name' => 'Returned']
//         ];
//         foreach ($statuses as $status) {
//             TransactionStatus::firstOrCreate($status);
//         }
        
//         // Seed default transaction types
//         $types = [
//             ['name' => 'Inbound'],
//             ['name' => 'Outbound'],
//             ['name' => 'Void'],
//             ['name' => 'Returned']
//         ];
//         foreach ($types as $type) {
//             TransactionType::firstOrCreate($type);
//         }

//         // Seed transactions
//         $transactions = [
//             [
//                 'user_id' => 1,
//                 'address_id' => 1,
//                 'payment_method_id' => 1,
//                 'type_id' => 1,
//                 'status_id' => 1,
//                 'products' => [
//                     ['product_id' => 1, 'quantity' => 2],
//                     ['product_id' => 2, 'quantity' => 1],
//                 ],
//             ],
//         ];

//         foreach ($transactions as $transactionData) {
//             // Extract products data
//             $products = $transactionData['products'];
//             unset($transactionData['products']);

//             // Create the transaction
//             $transaction = Transaction::create($transactionData);

//             // Insert products into the pivot table
//             $items = [];
//             foreach ($products as $product) {
//                 $productModel = Product::find($product['product_id']);
//                 if ($productModel) {
//                     $items[$product['product_id']] = [
//                         'quantity' => $product['quantity'],
//                         'price' => $productModel->price,
//                         'sub_total' => $productModel->price * $product['quantity'],
//                     ];
//                 }
//             }
//             $transaction->products()->sync($items);
//         }
        
    }
}
