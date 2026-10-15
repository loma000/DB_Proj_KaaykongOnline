<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Review;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Seeder;

class KitchenwareSeeder extends Seeder
{
    /**
     * Seed sample kitchenware products, categories, stock, and discounts.
     */
    public function run(): void
    {
        $categories = [];

        foreach ([
            'หม้อและกระทะ' => 'Pots and Pans',
            'มีดและอุปกรณ์เตรียมอาหาร' => 'Knives and Food Preparation',
            'เครื่องครัวไม้' => 'Wooden Utensils',
            'อุปกรณ์อบขนม' => 'Bakeware',
            'ภาชนะจัดเก็บอาหาร' => 'Food Storage',
        ] as $oldName => $categoryName) {
            $category = Category::whereIn('category_name', [$oldName, $categoryName])->first();
            $categories[$categoryName] = $category
                ? tap($category)->update(['category_name' => $categoryName])
                : Category::create(['category_name' => $categoryName]);
        }

        $products = [
            [
                'legacy_name' => 'กระทะเหล็กหล่อ 10 นิ้ว',
                'product_name' => '10-Inch Cast Iron Skillet',
                'price' => 890,
                'description' => 'A heat-retaining cast iron skillet, ideal for frying and searing.',
                'image' => 'products/cast-iron-skillets.jpg',
                'quantity' => 24,
                'categories' => ['Pots and Pans'],
                'review' => ['score' => 5, 'comment' => 'Heats evenly and holds its temperature well. Great for searing.'],
            ],
            [
                'legacy_name' => 'กระทะเคลือบเซรามิก 28 ซม.',
                'product_name' => '28 cm Ceramic Nonstick Frying Pan',
                'price' => 690,
                'description' => 'A ceramic nonstick pan compatible with gas and electric stovetops.',
                'image' => 'products/ceramic-frying-pan.jpg',
                'quantity' => 32,
                'categories' => ['Pots and Pans'],
                'review' => ['score' => 4, 'comment' => 'Food releases easily and cleanup is quick.'],
            ],
            [
                'legacy_name' => 'หม้อสเตนเลสพร้อมฝา 24 ซม.',
                'product_name' => '24 cm Stainless Steel Pot with Lid',
                'price' => 790,
                'description' => 'A durable, heavy-gauge stainless steel pot with a glass lid, easy to clean.',
                'image' => 'products/stainless-stockpot.jpg',
                'quantity' => 18,
                'categories' => ['Pots and Pans'],
                'review' => ['score' => 5, 'comment' => 'Solid construction and the glass lid is very useful.'],
            ],
            [
                'legacy_name' => 'หม้อด้ามสเตนเลส 18 ซม.',
                'product_name' => '18 cm Stainless Steel Saucepan',
                'price' => 450,
                'description' => 'A compact saucepan for reheating food and making soup.',
                'image' => 'products/stainless-pans.jpg',
                'quantity' => 27,
                'categories' => ['Pots and Pans'],
                'review' => ['score' => 4, 'comment' => 'A handy size for sauces and small portions.'],
            ],
            [
                'legacy_name' => 'ชุดมีดทำครัว 5 ชิ้น',
                'product_name' => '5-Piece Kitchen Knife Set',
                'price' => 1200,
                'description' => 'A stainless steel knife set for slicing, chopping, and peeling, with a storage block.',
                'image' => 'products/kitchen-knife-set.jpg',
                'quantity' => 12,
                'categories' => ['Knives and Food Preparation'],
                'review' => ['score' => 5, 'comment' => 'Sharp, comfortable to hold, and the block keeps everything tidy.'],
            ],
            [
                'legacy_name' => 'เขียงไม้ยางพารา',
                'product_name' => 'Rubberwood Cutting Board',
                'price' => 320,
                'description' => 'A sturdy rubberwood cutting board with a smooth surface for food preparation.',
                'image' => 'products/wooden-cutting-board.jpg',
                'quantity' => 40,
                'categories' => ['Knives and Food Preparation', 'Wooden Utensils'],
                'review' => ['score' => 4, 'comment' => 'Sturdy board with plenty of room for everyday prep.'],
            ],
            [
                'legacy_name' => 'ตะหลิวไม้สัก',
                'product_name' => 'Teak Wood Spatula',
                'price' => 150,
                'description' => 'A lightweight teak spatula that will not scratch cookware.',
                'image' => 'products/wooden-utensils.jpg',
                'quantity' => 55,
                'categories' => ['Wooden Utensils'],
                'review' => ['score' => 5, 'comment' => 'Lightweight and gentle on my nonstick pans.'],
            ],
            [
                'legacy_name' => 'ทัพพีไม้ไผ่',
                'product_name' => 'Bamboo Ladle',
                'price' => 95,
                'description' => 'An easy-to-grip bamboo ladle for serving and stirring.',
                'image' => 'products/wooden-utensils.jpg',
                'quantity' => 48,
                'categories' => ['Wooden Utensils'],
                'review' => ['score' => 4, 'comment' => 'Comfortable to use and a nice simple kitchen staple.'],
            ],
            [
                'legacy_name' => 'ถาดอบขนม non-stick',
                'product_name' => 'Nonstick Baking Tray',
                'price' => 280,
                'description' => 'A nonstick baking tray that releases baked goods easily and cleans up quickly.',
                'image' => 'products/baking-tray.jpg',
                'quantity' => 30,
                'categories' => ['Bakeware'],
                'review' => ['score' => 5, 'comment' => 'Bakes evenly and is easy to clean after use.'],
            ],
            [
                'legacy_name' => 'พิมพ์เค้กกลม 8 นิ้ว',
                'product_name' => '8-Inch Round Cake Pan',
                'price' => 220,
                'description' => 'An aluminum cake pan that distributes heat evenly.',
                'image' => 'products/baking-tray.jpg',
                'quantity' => 26,
                'categories' => ['Bakeware'],
                'review' => ['score' => 4, 'comment' => 'Good size for a small cake and heats evenly.'],
            ],
            [
                'legacy_name' => 'ชุดชามผสมสเตนเลส 3 ใบ',
                'product_name' => '3-Piece Stainless Steel Mixing Bowl Set',
                'price' => 390,
                'description' => 'A set of three mixing bowls in different sizes that nest for compact storage.',
                'image' => 'products/stainless-pans.jpg',
                'quantity' => 21,
                'categories' => ['Bakeware', 'Food Storage'],
                'review' => ['score' => 5, 'comment' => 'Useful range of sizes and they stack neatly in the cupboard.'],
            ],
            [
                'legacy_name' => 'กล่องถนอมอาหารแก้ว 1 ลิตร',
                'product_name' => '1 L Glass Food Storage Container',
                'price' => 180,
                'description' => 'A glass container with a lid for food storage and microwave reheating.',
                'image' => 'products/glass-food-jars.jpg',
                'quantity' => 60,
                'categories' => ['Food Storage'],
                'review' => ['score' => 4, 'comment' => 'Convenient for leftovers and easy to reheat in the microwave.'],
            ],
        ];

        $seededProducts = [];
        foreach ($products as $productData) {
            $legacyName = $productData['legacy_name'];
            $categoryNames = $productData['categories'];
            $quantity = $productData['quantity'];
            $reviewData = $productData['review'];
            unset($productData['legacy_name'], $productData['categories'], $productData['quantity'], $productData['review']);

            $product = Product::whereIn('product_name', [$legacyName, $productData['product_name']])->first();
            if ($product) {
                $product->update($productData);
            } else {
                $product = Product::create($productData);
            }

            $product->categories()->sync(
                collect($categoryNames)
                    ->map(fn (string $name): int => $categories[$name]->id)
                    ->all(),
            );

            Stock::updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => $quantity],
            );

            $seededProducts[] = [$product, $reviewData];
        }

        $reviewers = User::query()->orderBy('id')->limit(5)->get();
        $additionalReviews = [
            ['score' => 5, 'comment' => 'Great quality and works just as expected.'],
            ['score' => 4, 'comment' => 'Good value and a useful addition to my kitchen.'],
            ['score' => 5, 'comment' => 'Very happy with this purchase. I use it often.'],
            ['score' => 4, 'comment' => 'Practical, well made, and easy to use.'],
        ];

        if ($reviewers->isNotEmpty()) {
            foreach ($seededProducts as $productIndex => [$product, $reviewData]) {
                foreach ($reviewers as $reviewerIndex => $reviewer) {
                    $data = $reviewerIndex === 0
                        ? $reviewData
                        : $additionalReviews[($productIndex + $reviewerIndex - 1) % count($additionalReviews)];

                    Review::firstOrCreate(
                        ['user_id' => $reviewer->id, 'product_id' => $product->id],
                        $data,
                    );
                }
            }
        } else {
            $this->command?->warn('Mock reviews were skipped because no users exist. Create users, then run php artisan db:seed again.');
        }

        $today = now()->toDateString();

        foreach ([
            [
                'coupon_name' => 'KITCHEN10',
                'discount_rate' => 10,
                'start_date' => $today,
                'end_date' => now()->addMonths(3)->toDateString(),
            ],
            [
                'coupon_name' => 'COOK15',
                'discount_rate' => 15,
                'start_date' => $today,
                'end_date' => now()->addMonths(1)->toDateString(),
            ],
            [
                'coupon_name' => 'NEWYEAR20',
                'discount_rate' => 20,
                'start_date' => now()->addMonths(1)->toDateString(),
                'end_date' => now()->addMonths(2)->toDateString(),
            ],
        ] as $discountData) {
            Discount::updateOrCreate(
                ['coupon_name' => $discountData['coupon_name']],
                $discountData,
            );
        }
    }
}
