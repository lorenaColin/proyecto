<?php

namespace Database\Seeders;

use App\Models\Sale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $purchase = new Sale();
            $purchase->id_usr = $i;
            $purchase->date = '2024-05-24';
            $purchase->amount = ($i + 95.20);
            $purchase->description = 'AAAAAAA' . $i;
            $purchase->receiver_id = $i;
            $purchase->payment_method = 0 . $i;
            $purchase->save();
        }
    }
}
