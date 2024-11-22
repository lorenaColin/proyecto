<?php

namespace Database\Seeders;

use App\Models\Taxe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxesSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $purchase = new Taxe();
            $purchase->tax_type = 0 . $i;
            $purchase->rate_quota = .16;
            $purchase->factor_type = $i;
            $purchase->withheld = 1;
            $purchase->invoice_detail_id = $i;
            $purchase->save();
        }
    }
}
