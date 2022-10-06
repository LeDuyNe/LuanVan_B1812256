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
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456789'),
            'role' => '0',
            'uuid' => Str::uuid()->toString()
        ]);

        User::create([
            'name' => 'Nguyễn Tấn Pil',
            'email' => 'pilB1812295@student.ctu.edu.vn',
            'password' => Hash::make('123456789'),
            'role' => '1',
            'uuid' => Str::uuid()->toString()
        ]);

        User::create([
            'name' => 'Lê Duy',
            'email' => 'duyB1812256@student.ctu.edu.vn',
            'password' => Hash::make('123456789'),
            'role' => '1',
            'uuid' => Str::uuid()->toString()
        ]);

        User::create([
            'name' => 'Trần Bảo Duy',
            'email' => 'duyB1812258@student.ctu.edu.vn',
            'password' => Hash::make('123456789'),
            'role' => '1',
            'uuid' => Str::uuid()->toString()
        ]);

        User::create([
            'name' => 'Dương Trung Hiền',
            'email' => 'duyB1812262@student.ctu.edu.vn',
            'password' => Hash::make('123456789'),
            'role' => '1',
            'uuid' => Str::uuid()->toString()
        ]);

        for($i = 1; $i <= 10; $i++){
            User::create([
                'name' => "student$i",
                'email' => "user$i@student.ctu.edu.vn",
                'password' => Hash::make('123456789'),
                'role' => '2',
                'uuid' => Str::uuid()->toString()
            ]);
        }
    }
}
