<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {

            $customer = new Payment();
            $customer->type = 'T';
            $customer->description = 'description' . $i;
            $customer->tones =  150;
            $customer->amount = 1500;
            $customer->date = '2024-04-04';
            $customer->id_usr =  $i;
            $customer->customer_id = $i;
            $customer->save();
        }
    }
}

