<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if doesn't exist
        $admin = User::where('email', 'admin@yopmail.com')->first();

        if (!$admin) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin_metrixsmate@yopmail.com',
                'password' => Hash::make('Test@123'),
                'mobile_no' => '+91-9999999999',
                'role' => 'admin',
                'is_active' => true,
                'admin_notes' => 'Initial admin account created by seeder',
                'free_ai_searches' => 100,
                'free_assessment_searches' => 100,
                'paid_search_tokens' => 50,
                'is_ocean_assessment_completed' => false,
                'is_riasec_assessment_completed' => false,
                'is_cognitive_assessment_completed' => false,
                'email_verified_at' => now(),
            ]);

            $this->command->info('✓ Admin user created successfully!');
            $this->command->line('');
            $this->command->info('Admin Account Credentials:');
            $this->command->line('Email: admin_metrixsmate@yopmail.com');
            $this->command->line('Password: Test@123');
            $this->command->line('');
            $this->command->warn('⚠ IMPORTANT: Change the password immediately after first login!');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
