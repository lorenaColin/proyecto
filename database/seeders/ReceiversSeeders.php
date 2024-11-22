<?php

namespace Database\Seeders;

use App\Models\Receiver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReceiversSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $purchase = new Receiver();
            $purchase->name = 'name' . $i;
            $purchase->street = 'street' . $i;
            $purchase->ext_num = 'ext_num' . $i;
            $purchase->int_num = 'int_num' . $i;
            $purchase->colony = 'colony' . $i;
            $purchase->municipality = 'municipality' . $i;
            $purchase->cp =  5025 . $i;
            $purchase->regime = 60 . $i;
            $purchase->estatus = 1;
            $purchase->rfc = 'CAJJ0' . $i . '0529191';
            $purchase->country = 'PAIS' . $i;
            $purchase->state = 'STATE' . $i;
            $purchase->save();
        }
    }
}
