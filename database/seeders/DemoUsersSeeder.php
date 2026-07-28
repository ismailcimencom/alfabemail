<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (App::environment('production')) {
            $this->command->warn('DemoUsersSeeder: Production ortamında atlanıyor.');
            return;
        }

        $users = [
            ['name' => 'Admin',        'email' => 'admin@alfabe.co',         'role' => 'admin'],
            ['name' => 'Demo Öğretmen','email' => 'ogretmen@alfabe.co',      'role' => 'ogretmen'],
            ['name' => 'Demo Veli',    'email' => 'veli@alfabe.co',          'role' => 'veli'],
            ['name' => 'Demo Öğrenci', 'email' => 'ogrenci@alfabe.co',       'role' => 'ogrenci'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('Demo123!'),
                    'is_active'=> true,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$data['role']]);
        }
    }
}
