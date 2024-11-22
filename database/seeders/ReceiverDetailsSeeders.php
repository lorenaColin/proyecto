<?php

namespace Database\Seeders;

use App\Models\ReceiverDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReceiverDetailsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $purchase = new ReceiverDetail();
            $purchase->email = 'email' . $i . '@gmail.com';
            $purchase->phone = 722 .$i . 789658;
            $purchase->receiver_id = $i;
            $purchase->save();
        }
    }
}
