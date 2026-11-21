<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\ExContractorRenewApplication;
use App\Models\ExElectricianRenewApplication;
use App\Models\ExSupervisorRenewApplication;
use App\Models\Notification;
use App\Models\RecordHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seed for testing and performance evaluation.
     * Safe to run multiple times (idempotent / updateOrCreate).
     *
     * CLI Usage:
     *   Docker: docker compose exec app php artisan db:seed --class=DummyDataSeeder
     *   Native: php artisan db:seed --class=DummyDataSeeder
     */
    public function run(): void
    {
        $operator = User::where('admin_type', 'data_entry_operator')->first() ?? User::first();
        $officeAssistant = User::where('admin_type', 'office_assistant')->first() ?? User::first();
        $secretary = User::where('admin_type', 'secretary')->first() ?? User::first();

        if (!$operator || !$officeAssistant || !$secretary) {
            $this->command->error('Required users (operator, office assistant, secretary) not found. Run UserSeeder first.');
            return;
        }

        $operatorId = $operator->id;
        $oaId = $officeAssistant->id;
        $secId = $secretary->id;

        $statuses = [
            'draft',
            'submitted_to_office_assistant',
            'office_assistant_rejected',
            'submitted_to_secretary',
            'secretary_approved_final',
        ];

        $classes = ['A', 'B', 'C', 'ABC', 'BC', 'AB'];
        $districts = ['Dhaka', 'Chattogram', 'Rajshahi', 'Khulna', 'Sylhet', 'Gazipur', 'Narayanganj', 'Cumilla'];
        $divisions = ['Dhaka', 'Chattogram', 'Rajshahi', 'Khulna', 'Sylhet', 'Rangpur', 'Barishal', 'Mymensingh'];

        $firstNamesEn = ['MD. ABDUR', 'MOHAMMAD', 'KAZI', 'SYED', 'MD. RASHEDUL', 'MD. KAMRUL', 'MD. SHAHIN', 'ANWAR', 'JAHANGIR', 'TANVIR'];
        $lastNamesEn = ['RAHMAN', 'ISLAM', 'HOSSAIN', 'ALAM', 'HASAN', 'AHMED', 'CHOWDHURY', 'SHEIKH', 'BHUIYAN', 'MIAH'];

        $namesBn = [
            'মোঃ আব্দুর রহমান', 'মোহাম্মদ আলী', 'কাজী কামরুল হাসান', 'সৈয়দ আনোয়ারুল ইসলাম',
            'মোঃ রাশেদুল হক', 'মোঃ শাহিনুল আলম', 'জাহাঙ্গীর আলম', 'তানভীর আহমেদ চৌধুরী',
            'মোঃ তারেক মাহমুদ', 'মোঃ ফখরুল ইসলাম'
        ];

        $reasons = [
            'Scanned register page is partially unreadable. Please re-upload at 300 DPI.',
            'Applicant Bengali name spelling does not match column 2 of register book.',
            'Father name typo detected. Please verify against physical book page.',
            'Class endorsement stamp is faint. Please attach secondary certificate scan.',
        ];

        DB::beginTransaction();
        try {
            // ── 1. Seed 60 Electrician Applications ───────────────────────────
            for ($i = 1; $i <= 60; $i++) {
                $status = $statuses[$i % count($statuses)];
                $fn = $firstNamesEn[$i % count($firstNamesEn)];
                $ln = $lastNamesEn[($i + 2) % count($lastNamesEn)];
                $nameEn = "{$fn} {$ln}";
                $nameBn = $namesBn[$i % count($namesBn)];
                $bookNo = 'BK-' . str_pad((int)(($i / 10) + 1), 3, '0', STR_PAD_LEFT);
                $certNo = 'EL-' . (10000 + $i);
                $cls = $classes[$i % count($classes)];
                $dist = $districts[$i % count($districts)];
                $div = $divisions[$i % count($divisions)];

                $app = ExElectricianRenewApplication::updateOrCreate(
                    ['old_certificate_number' => $certNo],
                    [
                        'book_number'            => $bookNo,
                        'class'                  => $cls,
                        'applicant_name_en'      => $nameEn,
                        'applicant_name_bn'      => $nameBn,
                        'father_name'            => 'MD. LATE ' . $ln,
                        'mother_name'            => 'MRS. ' . $ln . ' BEGUM',
                        'mobile_no'              => '0171' . str_pad($i, 7, '0', STR_PAD_LEFT),
                        'email'                  => 'electrician' . $i . '@example.test',
                        'date_of_birth'          => '1990-05-15',
                        'nid_number'             => '1990' . str_pad($i, 13, '0', STR_PAD_LEFT),
                        'village'                => 'Holding #' . ($i * 12) . ', Ward 04',
                        'post_office'            => $dist . ' Sadar',
                        'postcode'               => 1000 + $i,
                        'upazilla'               => $dist . ' Sadar',
                        'district'               => $dist,
                        'division'               => $div,
                        'status'                 => $status,
                        'entry_by'               => $operatorId,
                        'entry_at'               => now()->subDays(60 - $i),
                        'last_updated_by'        => $operatorId,
                        'last_updated_at'        => now()->subDays(60 - $i),
                        'verified_by_office_assistant' => in_array($status, ['submitted_to_secretary', 'secretary_approved_final']) ? $oaId : null,
                        'verified_at_office_assistant' => in_array($status, ['submitted_to_secretary', 'secretary_approved_final']) ? now()->subDays(50 - $i) : null,
                        'rejected_by'            => ($status === 'office_assistant_rejected') ? $oaId : null,
                        'rejected_at'            => ($status === 'office_assistant_rejected') ? now()->subDays(40 - $i) : null,
                        'reject_reason'          => ($status === 'office_assistant_rejected') ? $reasons[$i % count($reasons)] : null,
                        'approved_by_secretary'  => ($status === 'secretary_approved_final') ? $secId : null,
                        'approved_at_secretary'  => ($status === 'secretary_approved_final') ? now()->subDays(30 - $i) : null,
                        'created_at'             => now()->subDays(60 - $i),
                        'updated_at'             => now()->subDays(max(0, 30 - $i)),
                    ]
                );

                // Create scanned document attachment for applications beyond draft
                if ($status !== 'draft' && $app->attachments()->count() === 0) {
                    $app->attachments()->create([
                        'file_path'     => 'sample/dummy_electrician_proof.jpg',
                        'file_name'     => "scan_{$certNo}.jpg",
                        'original_name' => "Register_Page_{$bookNo}.jpg",
                        'mime_type'     => 'image/jpeg',
                        'file_size'     => 1024 * rand(800, 2400),
                        'uploaded_by'   => $operatorId,
                        'uploaded_at'   => $app->created_at,
                    ]);
                }
            }

            // ── 2. Seed 40 Supervisor Applications ────────────────────────────
            for ($j = 1; $j <= 40; $j++) {
                $status = $statuses[$j % count($statuses)];
                $fn = $firstNamesEn[($j + 1) % count($firstNamesEn)];
                $ln = $lastNamesEn[($j + 3) % count($lastNamesEn)];
                $nameEn = "ENGR. {$fn} {$ln}";
                $bookNo = 'BK-SUP-' . str_pad((int)(($j / 8) + 1), 3, '0', STR_PAD_LEFT);
                $certNo = 'SUP-' . (20000 + $j);
                $dist = $districts[$j % count($districts)];
                $div = $divisions[$j % count($divisions)];

                $app = ExSupervisorRenewApplication::updateOrCreate(
                    ['old_certificate_number' => $certNo],
                    [
                        'book_number'            => $bookNo,
                        'class'                  => 'ABC',
                        'applicant_name_en'      => $nameEn,
                        'applicant_name_bn'      => 'প্রকৌঃ ' . $namesBn[$j % count($namesBn)],
                        'father_name'            => 'MD. ' . $ln,
                        'mobile_no'              => '0181' . str_pad($j, 7, '0', STR_PAD_LEFT),
                        'email'                  => 'supervisor' . $j . '@example.test',
                        'degree'                 => ($j % 2 == 0) ? 'Diploma in Electrical Eng.' : 'BSc in Electrical & Electronics Eng.',
                        'board'                  => 'BTEB / Engineering University',
                        'passing_year'           => (string)(2005 + ($j % 15)),
                        'village'                => 'Sector 07, Road 14',
                        'district'               => $dist,
                        'division'               => $div,
                        'status'                 => $status,
                        'entry_by'               => $operatorId,
                        'entry_at'               => now()->subDays(40 - $j),
                        'last_updated_by'        => $operatorId,
                        'last_updated_at'        => now()->subDays(40 - $j),
                        'verified_by_office_assistant' => in_array($status, ['submitted_to_secretary', 'secretary_approved_final']) ? $oaId : null,
                        'verified_at_office_assistant' => in_array($status, ['submitted_to_secretary', 'secretary_approved_final']) ? now()->subDays(30 - $j) : null,
                        'rejected_by'            => ($status === 'office_assistant_rejected') ? $oaId : null,
                        'rejected_at'            => ($status === 'office_assistant_rejected') ? now()->subDays(20 - $j) : null,
                        'reject_reason'          => ($status === 'office_assistant_rejected') ? 'Diploma engineering transcript photocopy is required.' : null,
                        'approved_by_secretary'  => ($status === 'secretary_approved_final') ? $secId : null,
                        'approved_at_secretary'  => ($status === 'secretary_approved_final') ? now()->subDays(10 - $j) : null,
                        'created_at'             => now()->subDays(40 - $j),
                        'updated_at'             => now()->subDays(max(0, 10 - $j)),
                    ]
                );

                if ($status !== 'draft' && $app->attachments()->count() === 0) {
                    $app->attachments()->create([
                        'file_path'     => 'sample/dummy_supervisor_proof.jpg',
                        'file_name'     => "scan_{$certNo}.jpg",
                        'original_name' => "Supervisor_Ledger_{$bookNo}.jpg",
                        'mime_type'     => 'image/jpeg',
                        'file_size'     => 1024 * rand(900, 3000),
                        'uploaded_by'   => $operatorId,
                        'uploaded_at'   => $app->created_at,
                    ]);
                }
            }

            // ── 3. Seed 30 Contractor Applications ────────────────────────────
            $companyPrefixes = ['DELTA', 'APEX', 'BENGAL', 'PRIME', 'HORIZON', 'UNITED', 'MEGHA', 'NATIONAL'];
            $companyTypes = ['Private Limited', 'Proprietorship', 'Partnership'];

            for ($k = 1; $k <= 30; $k++) {
                $status = $statuses[$k % count($statuses)];
                $prefix = $companyPrefixes[$k % count($companyPrefixes)];
                $compNameEn = "{$prefix} POWER & ELECTRICAL ENGINEERING LTD.";
                $compNameBn = "{$prefix} পাওয়ার এন্ড ইলেকট্রিক্যাল ইঞ্জিনিয়ারিং লিঃ";
                $bookNo = 'BK-CONT-' . str_pad((int)(($k / 6) + 1), 3, '0', STR_PAD_LEFT);
                $certNo = 'CONT-' . (30000 + $k);
                $tradeLic = 'TRAD/DNCC/' . (202000 + $k);
                $dist = $districts[$k % count($districts)];
                $div = $divisions[$k % count($divisions)];

                $app = ExContractorRenewApplication::updateOrCreate(
                    ['old_certificate_number' => $certNo],
                    [
                        'book_number'            => $bookNo,
                        'class'                  => 'ABC',
                        'company_name_en'        => $compNameEn,
                        'company_name_bn'        => $compNameBn,
                        'company_type'           => $companyTypes[$k % count($companyTypes)],
                        'trade_license_number'   => $tradeLic,
                        'mobile_no'              => '0191' . str_pad($k, 7, '0', STR_PAD_LEFT),
                        'email'                  => 'info@contractor' . $k . '.test',
                        'elb_certified_supervisor_no' => 'SUP-' . (20000 + $k),
                        'boa_village'            => 'Plot #' . ($k * 5) . ', Tejgaon Industrial Area',
                        'boa_district'           => $dist,
                        'boa_division'           => $div,
                        'status'                 => $status,
                        'entry_by'               => $operatorId,
                        'entry_at'               => now()->subDays(30 - $k),
                        'last_updated_by'        => $operatorId,
                        'last_updated_at'        => now()->subDays(30 - $k),
                        'verified_by_office_assistant' => in_array($status, ['submitted_to_secretary', 'secretary_approved_final']) ? $oaId : null,
                        'verified_at_office_assistant' => in_array($status, ['submitted_to_secretary', 'secretary_approved_final']) ? now()->subDays(20 - $k) : null,
                        'rejected_by'            => ($status === 'office_assistant_rejected') ? $oaId : null,
                        'rejected_at'            => ($status === 'office_assistant_rejected') ? now()->subDays(15 - $k) : null,
                        'reject_reason'          => ($status === 'office_assistant_rejected') ? 'Trade license renewal receipt for current fiscal year is required.' : null,
                        'approved_by_secretary'  => ($status === 'secretary_approved_final') ? $secId : null,
                        'approved_at_secretary'  => ($status === 'secretary_approved_final') ? now()->subDays(5 - $k) : null,
                        'created_at'             => now()->subDays(30 - $k),
                        'updated_at'             => now()->subDays(max(0, 5 - $k)),
                    ]
                );

                if ($status !== 'draft' && $app->attachments()->count() === 0) {
                    $app->attachments()->create([
                        'file_path'     => 'sample/dummy_contractor_proof.jpg',
                        'file_name'     => "scan_{$certNo}.jpg",
                        'original_name' => "Contractor_Trade_Lic_{$certNo}.jpg",
                        'mime_type'     => 'image/jpeg',
                        'file_size'     => 1024 * rand(1200, 4500),
                        'uploaded_by'   => $operatorId,
                        'uploaded_at'   => $app->created_at,
                    ]);
                }
            }

            // ── 4. Seed Audit Trail & Notifications ───────────────────────────
            for ($n = 1; $n <= 15; $n++) {
                $notifMsg = ($n % 2 == 0)
                    ? "Application EL-" . (10000 + $n) . " has received final statutory approval."
                    : "Application EL-" . (10010 + $n) . " was rejected by Office Assistant. Reason: Please verify candidate father name.";

                Notification::firstOrCreate(
                    [
                        'user_id' => $operatorId,
                        'message' => $notifMsg,
                    ],
                    [
                        'title'   => ($n % 2 == 0) ? 'Application Approved by Secretary' : 'Application Returned for Revision',
                        'type'    => ($n % 2 == 0) ? 'success' : 'warning',
                        'read_at' => ($n <= 5) ? now() : null, // 10 unread, 5 read
                        'created_at' => now()->subHours($n * 2),
                    ]
                );
            }

            DB::commit();
            $this->command->info('Dummy data successfully seeded: 60 Electricians, 40 Supervisors, 30 Contractors, 15 Notifications.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Dummy data seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
