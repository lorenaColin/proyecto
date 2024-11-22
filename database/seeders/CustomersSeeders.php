<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {

            $customer = new Customer();
            $customer->name = 'Cliente ' . $i;
            $customer->street = 'Calle ' . $i;
            $customer->ext_num = 'Ext ' . $i;
            $customer->int_num = 'Int ' . $i;
            $customer->colony = 'Colonia ' . $i;
            $customer->municipality = 'Municipio ' . $i;
            $customer->cp = 'CP' . $i;
            $customer->estatus = 1;
            $customer->rfc = 'RFC' . $i;
            $customer->country = 'País ' . $i;
            $customer->state = 'Estado ' . $i;
            $customer->regime = 'Régimen ' . $i;
            $customer->type = ($i % 2 == 0) ? 'P' : 'H';
            $customer->id_usr_create = $i;
            $customer->save();
        }
    }
}
