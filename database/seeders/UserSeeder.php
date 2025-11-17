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
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@investpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('superadmin123'),
            'user_type' => 'super_admin',
            'phone' => '+1 (555) 010-0001',
            'is_active' => true,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Administrators
        $admins = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@investpro.com',
                'password' => 'admin123',
                'phone' => '+1 (555) 010-0002',
            ],
            [
                'name' => 'Financial Manager',
                'email' => 'finance@investpro.com',
                'password' => 'finance123',
                'phone' => '+1 (555) 010-0003',
            ],
            [
                'name' => 'Operations Manager',
                'email' => 'operations@investpro.com',
                'password' => 'operations123',
                'phone' => '+1 (555) 010-0004',
            ],
        ];

        foreach ($admins as $admin) {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($admin['password']),
                'user_type' => 'admin',
                'phone' => $admin['phone'],
                'is_active' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Sample Investors
        $investors = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1001',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1002',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1003',
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1004',
            ],
            [
                'name' => 'Robert Wilson',
                'email' => 'robert.wilson@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1005',
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1006',
            ],
            [
                'name' => 'David Miller',
                'email' => 'david.miller@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1007',
            ],
            [
                'name' => 'Jennifer Taylor',
                'email' => 'jennifer.taylor@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1008',
            ],
            [
                'name' => 'Christopher Lee',
                'email' => 'christopher.lee@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1009',
            ],
            [
                'name' => 'Amanda White',
                'email' => 'amanda.white@example.com',
                'password' => 'investor123',
                'phone' => '+1 (555) 010-1010',
            ],
        ];

        foreach ($investors as $investor) {
            User::create([
                'name' => $investor['name'],
                'email' => $investor['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($investor['password']),
                'user_type' => 'investor',
                'phone' => $investor['phone'],
                'is_active' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create some inactive users for testing
        User::create([
            'name' => 'Inactive Investor',
            'email' => 'inactive@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('inactive123'),
            'user_type' => 'investor',
            'phone' => '+1 (555) 010-9999',
            'is_active' => false,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Successfully seeded users:');
        $this->command->info('- 1 Super Admin (superadmin@investpro.com / superadmin123)');
        $this->command->info('- 3 Administrators');
        $this->command->info('- 10 Active Investors (all using password: investor123)');
        $this->command->info('- 1 Inactive Investor');
    }
}