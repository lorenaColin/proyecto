<?php

namespace Database\Seeders;

use App\Models\Purchase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchasesSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $purchase = new Purchase();
            $purchase->cost_tones = $i;
            $purchase->price_tones = ($i + 46.20);
            $purchase->ganancia = ($i + 95.20);
            $purchase->tones_adq = $i;
            $purchase->estatus = 1;
            $purchase->pac_id = $i;
            $purchase->save();
        }
    }
}
