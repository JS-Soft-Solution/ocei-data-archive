<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Common default password for all seeded admin users
        $defaultPassword = Hash::make('password'); // Change this in production

        $users = [
            [
                'user_id'      => 'SA-0001',
                'full_name'    => 'Super Admin',
                'email'        => 'superadmin@ocei.local',
                'mobile_no'    => '01700000001',
                'admin_type'   => 'super_admin',
            ],
            [
                'user_id'      => 'OA-0001',
                'full_name'    => 'Office Assistant',
                'email'        => 'office.assistant@ocei.local',
                'mobile_no'    => '01700000002',
                'admin_type'   => 'office_assistant',
            ],
            [
                'user_id'      => 'SEC-0001',
                'full_name'    => 'Secretary',
                'email'        => 'secretary@ocei.local',
                'mobile_no'    => '01700000003',
                'admin_type'   => 'secretary',
            ],
            [
                'user_id'      => 'CH-0001',
                'full_name'    => 'Chairman',
                'email'        => 'chairman@ocei.local',
                'mobile_no'    => '01700000004',
                'admin_type'   => 'chairman',
            ],
            [
                'user_id'      => 'INSP-0001',
                'full_name'    => 'Inspector',
                'email'        => 'inspector@ocei.local',
                'mobile_no'    => '01700000005',
                'admin_type'   => 'inspector',
            ],
            [
                'user_id'      => 'DEO-0001',
                'full_name'    => 'Data Entry Operator',
                'email'        => 'deo@ocei.local',
                'mobile_no'    => '01700000006',
                'admin_type'   => 'data_entry_operator',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                [
                    // Unique constraint for “identity” – prevents duplicate seeding
                    'email' => $data['email'],
                ],
                array_merge($data, [
                    'password'          => $defaultPassword,
                    'otp_status'        => 'verified',
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
