<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = new User();
        $user->name = 'Daniel';
        $user->email = 'daniel.gutierrez@easysweb.com.mx';
        $user->email_verified_at = '2024-06-17 10:19:11';
        $user->password = '12345678';
        $user->code = 'ABCD';
        $user->save();

        $user = new User();
        $user->name = 'Jesus';
        $user->email = 'capa.2901@gmail.com';
        $user->email_verified_at = '2024-06-17 10:19:11';
        $user->password = '12345678';
        $user->code = 'ABCD';
        $user->save();

        $user = new User();
        $user->name = 'Lorena';
        $user->email = 'programador2@easysweb.com.mx';
        $user->email_verified_at = '2024-06-17 10:19:11';
        $user->password = '12345678';
        $user->code = 'ABCD';
        $user->save();

        $user = new User();
        $user->name = 'Luis';
        $user->email = 'programador3@easysweb.com.mx';
        $user->email_verified_at = '2024-06-17 10:19:11';
        $user->password = '12345678';
        $user->code = 'ABCD';
        $user->save();
    }
}
