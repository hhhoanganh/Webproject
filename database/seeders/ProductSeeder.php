<?php

namespace Database\Seeders;

use App\Models\Product\Images;
use App\Models\Product\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Nette\Utils\Random;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Thêm dữ liệu mẫu
        for ($x = 0; $x <= 10; $x++) {
            for ($u = 0; $u <= 10; $u++) {
                $product = Product::create([
                'name' => Str::random(10),
                'description' => Str::random(10),
                'thumbnail' => Str::random(10),
                'price' =>random_int(0,10),
                ]);
                $image =  Images::create([
                    'name' => Str::random(10),
                    'product_id'=> $product->id
                ]);
            }
        }
        $this->command->info('Products seeded successfully!');
    }
}
