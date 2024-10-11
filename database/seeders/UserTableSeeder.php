<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //KETIGA DATA INI AKAN DIJADIKAN DUMMY USER DENGAN MASING-MASING ROLE YANG DIMILIKINYA
        User::create([
            'name' => 'Arikun',
            'email' => 'arikun@gmail.com',
            'password' => bcrypt('adminku123'),
            'role' => 'admin'
        ]);
      
        User::create([
            'name' => 'niisan',
            'email' => 'arikun2@gmail.com',
            'password' => bcrypt('adminku123'),
            'role' => 'user'
        ]);
    }
}