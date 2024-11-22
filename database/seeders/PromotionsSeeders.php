<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotion = new Promotion();
        $promotion->quantity = 10;
        $promotion->date_init = '2024-05-14';
        $promotion->date_end = '2024-05-29';
        $promotion->save();
    }
}
