<?php

namespace Database\Seeders;

use App\Models\InvoiceDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailsInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $descripcion=1;

        for ($contador=1; $contador<=5;$contador++) {

            $DetailsInvoice = new InvoiceDetail();
            $DetailsInvoice->total_product=($descripcion+12);
            $DetailsInvoice->product_service_code=('xxx'.$contador);
            $DetailsInvoice->description='Hola'.$contador;
            $DetailsInvoice->quantity='12.3'.$contador;
            $DetailsInvoice->unit_value='32.1'.$contador;
            $DetailsInvoice->unit_key= 'ffdf';
            $DetailsInvoice->identification_number= 'fsdf';
            $DetailsInvoice->tax_object= 'f'.$contador;
            $DetailsInvoice->discount='34.5'.$contador;
            $DetailsInvoice->discount_percentage= '12.5';
            $DetailsInvoice->base='12.5'.$contador;
            $DetailsInvoice->invoice_id=$contador;
            $DetailsInvoice->save();

        }
}

}
