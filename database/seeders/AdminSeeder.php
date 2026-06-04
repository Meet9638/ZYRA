<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@zyra.com',
            'password' => Hash::make('zyra@123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }
}
