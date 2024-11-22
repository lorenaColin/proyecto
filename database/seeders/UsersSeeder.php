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
        $user->email = 'daniel.gutierrez@easysweb.com.mx';
        $user->email_verified_at = '2024-06-17 10:19:11';
        $user->password = 'Daniel#2024';
        $user->multi_rfc = false;
        $user->save();

        $user = new User();
        $user->email = 'capa.2901@gmail.com';
        $user->email_verified_at = '2024-06-17 10:19:11';
        $user->password = 'Jesus#2024';
        $user->multi_rfc = true;
        $user->save();

        $user = new User();
        $user->email = 'programador2@easysweb.com.mx';
        $user->email_verified_at = '2024-06-17 10:19:11';
        $user->password = 'Lorena#2024';
        $user->multi_rfc = false;
        $user->save();

        $user = new User();
        $user->email = 'programador3@easysweb.com.mx';
        $user->email_verified_at = '2024-06-17 10:19:11';
        $user->password = 'Luis#2024';
        $user->multi_rfc = false;
        $user->save();
    }
}
