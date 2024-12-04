<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(UsersSeeder::class);
        // $this->call(InvoiceSeeder::class);
        // $this->call(DetailsInvoiceSeeder::class);
        // $this->call(RelacionadosSeeder::class);
        // // $this->call(CustomersSeeders::class);
        // // $this->call(CustomerDetailsSeeders::class);
        // // $this->call(CustomerContactSeeders::class);
        // // $this->call(PaymentsSeeders::class);
        // $this->call(CollaboratorsSeeders::class);
        // $this->call(SalesSeeders::class);
        // $this->call(TaxesSeeders::class);
        // // $this->call(ProductsSeeders::class);
        // // $this->call(ReceiversSeeders::class);
        // //$this->call(ReceiverDetailsSeeders::class);
        // $this->call(PacsSeeders::class);
        // $this->call(PurchasesSeeders::class);
        // $this->call(SeriesSeeders::class);
        // $this->call(PredialsSeeders::class);
        //$this->call(PromotionsSeeders::class);
    }
}
