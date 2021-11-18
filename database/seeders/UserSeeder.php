<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => 'yaz.learner@gmail.com',
            'name' => 'Yazz',
            'password' => \Hash::make('12345678'),
            'status' => 'aktif',
        ]);
    }
}
