<?php

namespace Database\Seeders;

use App\Models\Related;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RelacionadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for($contador=1;$contador <=  5;$contador++){
            $relaciona=new Related();
            $relaciona->type_relation='hola'.$contador;
            $relaciona->uuid=uniqid();
            $relaciona->invoice_id= $contador;
            $relaciona->save();
        }
    }
}
