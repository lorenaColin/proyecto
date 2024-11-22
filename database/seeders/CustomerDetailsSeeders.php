<?php

namespace Database\Seeders;

use App\Models\CustomerDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerDetailsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {

            $customer = new CustomerDetail();
            $customer->tones_incluide = 1500;
            $customer->pac_id =  $i;
            $customer->certificate = 'certificate' . $i;
            $customer->private_key = 'private' . $i;
            $customer->password_key = 'password' . $i;
            $customer->contcert = 'password' . $i;
            $customer->expiration_date_cert = '2024-04-04';
            $customer->start_date_cert = '2024-04-04';
            $customer->customer_id = $i;
            $customer->save();
        }
    }
}
