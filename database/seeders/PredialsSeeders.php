<?php

namespace Database\Seeders;

use App\Models\Predial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PredialsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $pac = new Predial();
            $pac->account = 'ABCD243' . $i;
            $pac->save();
        }
    }
}
