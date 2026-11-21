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
        $defaultPassword = Hash::make('password');

        $users = [
            // Standard system users (.local)
            [
                'user_id'      => 'SA-0001',
                'full_name'    => 'Super Admin',
                'email'        => 'superadmin@ocei.local',
                'mobile_no'    => '01700000001',
                'admin_type'   => 'super_admin',
                'role'         => 'super_admin',
            ],
            [
                'user_id'      => 'OA-0001',
                'full_name'    => 'Office Assistant',
                'email'        => 'office.assistant@ocei.local',
                'mobile_no'    => '01700000002',
                'admin_type'   => 'office_assistant',
                'role'         => 'office_assistant',
            ],
            [
                'user_id'      => 'SEC-0001',
                'full_name'    => 'Secretary',
                'email'        => 'secretary@ocei.local',
                'mobile_no'    => '01700000003',
                'admin_type'   => 'secretary',
                'role'         => 'secretary',
            ],
            [
                'user_id'      => 'CH-0001',
                'full_name'    => 'Chairman',
                'email'        => 'chairman@ocei.local',
                'mobile_no'    => '01700000004',
                'admin_type'   => 'chairman',
                'role'         => 'chairman',
            ],
            [
                'user_id'      => 'INSP-0001',
                'full_name'    => 'Inspector',
                'email'        => 'inspector@ocei.local',
                'mobile_no'    => '01700000005',
                'admin_type'   => 'inspector',
                'role'         => 'inspector',
            ],
            [
                'user_id'      => 'DEO-0001',
                'full_name'    => 'Data Entry Operator',
                'email'        => 'deo@ocei.local',
                'mobile_no'    => '01700000006',
                'admin_type'   => 'data_entry_operator',
                'role'         => 'data_entry_operator',
            ],

            // Government domain aliases (.gov.bd)
            [
                'user_id'      => 'ADM-0002',
                'full_name'    => 'System Admin',
                'email'        => 'admin@ocei.gov.bd',
                'mobile_no'    => '01700000011',
                'admin_type'   => 'super_admin',
                'role'         => 'super_admin',
            ],
            [
                'user_id'      => 'OA-0002',
                'full_name'    => 'Office Assistant (Gov)',
                'email'        => 'oa@ocei.gov.bd',
                'mobile_no'    => '01700000012',
                'admin_type'   => 'office_assistant',
                'role'         => 'office_assistant',
            ],
            [
                'user_id'      => 'SEC-0002',
                'full_name'    => 'Secretary (Gov)',
                'email'        => 'secretary@ocei.gov.bd',
                'mobile_no'    => '01700000013',
                'admin_type'   => 'secretary',
                'role'         => 'secretary',
            ],
            [
                'user_id'      => 'CH-0002',
                'full_name'    => 'Chairman (Gov)',
                'email'        => 'chairman@ocei.gov.bd',
                'mobile_no'    => '01700000014',
                'admin_type'   => 'chairman',
                'role'         => 'chairman',
            ],
            [
                'user_id'      => 'DEO-0002',
                'full_name'    => 'Operator (Gov)',
                'email'        => 'operator@ocei.gov.bd',
                'mobile_no'    => '01700000015',
                'admin_type'   => 'data_entry_operator',
                'role'         => 'data_entry_operator',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password'          => $defaultPassword,
                    'otp_status'        => 'verified',
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
