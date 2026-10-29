<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $kaprodi = Role::firstOrCreate([
            'name' => 'kaprodi',
            'guard_name' => 'web',
        ]);

        $mahasiswa = Role::firstOrCreate([
            'name' => 'mahasiswa',
            'guard_name' => 'web',
        ]);

        $userKaprodi = User::firstOrCreate(
            ['email' => 'kaprodi@gmail.com'],
            [
                'name' => 'Kaprodi',
                'password' => Hash::make('Kaprodi123!'),
            ]
        );

        $userKaprodi->syncRoles([$kaprodi]);

        $userMahasiswa = User::firstOrCreate(
            ['email' => 'mahasiswa@gmail.com'],
            [
                'name' => 'Mahasiswa Demo',
                'password' => Hash::make('Mahasiswa123!'),
            ]
        );

        $userMahasiswa->syncRoles([$mahasiswa]);
    }
}