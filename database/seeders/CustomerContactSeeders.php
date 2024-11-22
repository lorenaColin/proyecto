<?php

namespace Database\Seeders;

use App\Models\CustomerContact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerContactSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {

            $customer = new CustomerContact();
            $customer->email_contact = 'email.' . $i . "@gmail.com";
            $customer->phone_office = '2984821' . $i;
            $customer->phone_movil =  '7223366950';
            $customer->name_contact = 'Prueba' . $i;
            $customer->description = 'AAAAAAAAAAA';
            $customer->customer_id = $i;
            $customer->save();

        }
    }
}
