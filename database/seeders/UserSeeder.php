<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => '0',
        ]);

        User::create([
            'name' => 'Nguyễn Tấn Pil',
            'email' => 'pilB1812295@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => '1',
        ]);

        User::create([
            'name' => 'Lê Duy',
            'email' => 'duyB1812256@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => '1',
        ]);

        User::create([
            'name' => 'Trần Bảo Duy',
            'email' => 'duyB1812257@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => '1',
        ]);

        User::create([
            'name' => 'Dương Trung Hiền',
            'email' => 'duyB1812262@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => '1',
        ]);

        for($i = 1; $i <= 5; $i++){
            User::create([
                'name' => "student$i",
                'email' => "user$i@gmail.com",
                'password' => Hash::make('123456789'),
                'role' => '2',
            ]);
        }
    }
}
