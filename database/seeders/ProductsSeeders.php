<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $product = new Product();
            $product->product_key = 'AG35' . $i . 'BFS';
            $product->unit = 'KGM';
            $product->description = 'Descripcion';
            $product->unit_description = 'KILOGRAMO';
            $product->quantity = $i + 45800;
            $product->unit_price =  $i;
            $product->identifier_number = $i;
            $product->save();
        }
    }
}
