<?php

namespace Database\Seeders;

use App\Models\Pac;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PacsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $pac = new Pac();
            $pac->name = 'Nombre' . $i;
            $pac->rfc = 'RFCCAAFFAFAF' . $i;
            $pac->tones = 200 . $i;
            $pac->save();
        }
    }
}
