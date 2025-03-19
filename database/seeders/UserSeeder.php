<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nama_lengkap' => 'Admin Satu',
                'NIM' => '123456',
                'email' => 'admin1@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'no_tlp' => '081234567890',
                'unit_kerja' => 'IT Support',
                'alamat' => 'Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'User Dua',
                'NIM' => '654321',
                'email' => 'user2@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'no_tlp' => '081234567891',
                'unit_kerja' => 'Keuangan',
                'alamat' => 'Bandung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Fakultas Tiga',
                'NIM' => '112233',
                'email' => 'fakultas3@example.com',
                'password' => Hash::make('password123'),
                'role' => 'fakultas',
                'no_tlp' => '081234567892',
                'unit_kerja' => 'Akademik',
                'alamat' => 'Surabaya',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'User Empat',
                'NIM' => '445566',
                'email' => 'user4@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'no_tlp' => '081234567893',
                'unit_kerja' => 'HRD',
                'alamat' => 'Yogyakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'anak anj',
                'NIM' => '778899',
                'email' => 'anakanj@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'no_tlp' => '081234567894',
                'unit_kerja' => 'Teknik',
                'alamat' => 'Medan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
