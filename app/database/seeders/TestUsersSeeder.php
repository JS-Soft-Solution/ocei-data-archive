<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Data Entry Operator',
                'email' => 'operator@ocei.gov.bd',
                'password' => Hash::make('password'),
                'role' => 'data_entry_operator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Assistant',
                'email' => 'oa@ocei.gov.bd',
                'password' => Hash::make('password'),
                'role' => 'office_assistant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Secretary',
                'email' => 'secretary@ocei.gov.bd',
                'password' => Hash::make('password'),
                'role' => 'secretary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chairman',
                'email' => 'chairman@ocei.gov.bd',
                'password' => Hash::make('password'),
                'role' => 'chairman',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Super Admin',
                'email' => 'admin@ocei.gov.bd',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);

        $this->command->info('✅ Created 5 test users with default password: password');
        $this->command->info('   - operator@ocei.gov.bd (Data Entry Operator)');
        $this->command->info('   - oa@ocei.gov.bd (Office Assistant)');
        $this->command->info('   - secretary@ocei.gov.bd (Secretary)');
        $this->command->info('   - chairman@ocei.gov.bd (Chairman)');
        $this->command->info('   - admin@ocei.gov.bd (Super Admin)');
    }
}
