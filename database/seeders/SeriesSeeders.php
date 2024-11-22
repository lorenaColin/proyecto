<?php

namespace Database\Seeders;

use App\Models\Serie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeriesSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $pac = new Serie();
            $pac->serie =  $i;
            $pac->folio = 'AA' . $i;
            $pac->save();
        }
    }
}
