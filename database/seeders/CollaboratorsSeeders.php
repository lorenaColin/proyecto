<?php

namespace Database\Seeders;

use App\Models\Collaborator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CollaboratorsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ( $i =1; $i <=5; $i++) {
            $collaborator = new Collaborator();
            $collaborator->id_customer = $i;
            $collaborator->estatus = 1;
            $collaborator->actions = 'rw';
            $collaborator->save();
        }

    }
}
