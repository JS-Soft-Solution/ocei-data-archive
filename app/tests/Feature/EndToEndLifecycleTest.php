<?php

namespace Tests\Feature;

use App\Models\ExContractorRenewApplication;
use App\Models\ExElectricianRenewApplication;
use App\Models\ExSupervisorRenewApplication;
use App\Models\Notification;
use App\Models\RecordHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EndToEndLifecycleTest extends TestCase
{
    use WithFaker;

    protected User $admin;
    protected User $operator;
    protected User $officeAssistant;
    protected User $secretary;
    protected User $chairman;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('admin_type', 'super_admin')->first()
            ?? User::factory()->create(['admin_type' => 'super_admin', 'role' => 'super_admin']);

        $this->operator = User::where('admin_type', 'data_entry_operator')->first()
            ?? User::factory()->create(['admin_type' => 'data_entry_operator', 'role' => 'data_entry_operator']);

        $this->officeAssistant = User::where('admin_type', 'office_assistant')->first()
            ?? User::factory()->create(['admin_type' => 'office_assistant', 'role' => 'office_assistant']);

        $this->secretary = User::where('admin_type', 'secretary')->first()
            ?? User::factory()->create(['admin_type' => 'secretary', 'role' => 'secretary']);

        $this->chairman = User::where('admin_type', 'chairman')->first()
            ?? User::factory()->create(['admin_type' => 'chairman', 'role' => 'chairman']);
    }

    /**
     * Test 1: Authentication & Role Boundary Protection
     */
    public function test_authentication_and_role_boundaries(): void
    {
        // Guests must be redirected to login
        $guestResponse = $this->get('/ex-electrician/operator/pending');
        $guestResponse->assertRedirect('/login');

        // Operator can access operator pending queue
        $this->actingAs($this->operator)
            ->get('/ex-electrician/operator/pending')
            ->assertStatus(200);

        // Office Assistant can access verification queue
        $this->actingAs($this->officeAssistant)
            ->get('/ex-electrician/office-assistant/pending')
            ->assertStatus(200);

        // Secretary can access approval queue
        $this->actingAs($this->secretary)
            ->get('/ex-electrician/secretary/pending')
            ->assertStatus(200);

        // Chairman can access Chairman view
        $this->actingAs($this->chairman)
            ->get('/ex-electrician/chairman')
            ->assertStatus(200);
    }

    /**
     * Test 2: Complete Electrician Application Lifecycle (Entry -> Verify -> Approve)
     */
    public function test_electrician_complete_approval_lifecycle(): void
    {
        $certNo = 'TEST-EL-' . uniqid();

        // 1. Operator creates application
        $application = ExElectricianRenewApplication::create([
            'old_certificate_number' => $certNo,
            'book_number'            => 'BK-2026-01',
            'class'                  => 'ABC',
            'applicant_name_en'      => 'MD RASHEDUL ISLAM',
            'applicant_name_bn'      => 'মোঃ রাশেদুল ইসলাম',
            'father_name'            => 'ABDUL KARIM',
            'mobile_no'              => '01711000001',
            'status'                 => 'draft',
            'entry_by'               => $this->operator->id,
            'entry_at'               => now(),
            'last_updated_by'        => $this->operator->id,
            'last_updated_at'        => now(),
        ]);

        $this->assertDatabaseHas('ex_electrician_renew_applications', [
            'id' => $application->id,
            'status' => 'draft',
        ]);

        // Attach scanned proof document (required before submission)
        $application->attachments()->create([
            'file_path' => 'sample/electrician_proof.jpg',
            'file_name' => 'electrician_proof.jpg',
            'original_name' => 'electrician_proof.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 2048,
            'uploaded_by' => $this->operator->id,
        ]);

        // 2. Operator submits to Office Assistant
        $submitResponse = $this->actingAs($this->operator)
            ->post(route('ex-electrician.operator.submit', $application->id));
        $submitResponse->assertRedirect();

        $application->refresh();
        $this->assertEquals('submitted_to_office_assistant', $application->status);

        // 3. Office Assistant approves and forwards to Secretary
        $oaApproveResponse = $this->actingAs($this->officeAssistant)
            ->post(route('ex-electrician.office-assistant.approve', $application->id));
        $oaApproveResponse->assertRedirect();

        $application->refresh();
        $this->assertEquals('submitted_to_secretary', $application->status);
        $this->assertEquals($this->officeAssistant->id, $application->verified_by_office_assistant);

        // 4. Secretary gives final approval
        $secApproveResponse = $this->actingAs($this->secretary)
            ->post(route('ex-electrician.secretary.approve', $application->id));
        $secApproveResponse->assertRedirect();

        $application->refresh();
        $this->assertEquals('secretary_approved_final', $application->status);
        $this->assertEquals($this->secretary->id, $application->approved_by_secretary);
    }

    /**
     * Test 3: Supervisor Lifecycle with Rejection, Audit Trail, and Resubmission
     */
    public function test_supervisor_rejection_and_resubmission_lifecycle(): void
    {
        $certNo = 'TEST-SUP-' . uniqid();

        // 1. Create and submit Supervisor application
        $application = ExSupervisorRenewApplication::create([
            'old_certificate_number' => $certNo,
            'book_number'            => 'BK-SUP-99',
            'class'                  => 'B',
            'applicant_name_en'      => 'KABIR HOSSAIN',
            'father_name'            => 'MOHAMMAD ALI',
            'mobile_no'              => '01811000002',
            'status'                 => 'submitted_to_office_assistant',
            'entry_by'               => $this->operator->id,
            'entry_at'               => now(),
            'last_updated_by'        => $this->operator->id,
            'last_updated_at'        => now(),
        ]);

        $application->attachments()->create([
            'file_path' => 'sample/supervisor_proof.jpg',
            'file_name' => 'supervisor_proof.jpg',
            'original_name' => 'supervisor_proof.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 2048,
            'uploaded_by' => $this->operator->id,
        ]);

        // 2. Office Assistant rejects with reason
        $rejectReason = 'Scanned copy blurry, missing electrical engineering diploma seal.';
        $oaRejectResponse = $this->actingAs($this->officeAssistant)
            ->post(route('ex-supervisor.office-assistant.reject', $application->id), [
                'reject_reason' => $rejectReason,
            ]);
        $oaRejectResponse->assertRedirect();

        $application->refresh();
        $this->assertEquals('office_assistant_rejected', $application->status);
        $this->assertEquals($rejectReason, $application->reject_reason);
        $this->assertEquals($this->officeAssistant->id, $application->rejected_by);

        // Verify rejection is visible in operator rejected queue
        $this->actingAs($this->operator)
            ->get(route('ex-supervisor.operator.rejected'))
            ->assertStatus(200)
            ->assertSee($certNo);

        // 3. Operator resubmits
        $resubmitResponse = $this->actingAs($this->operator)
            ->post(route('ex-supervisor.operator.submit', $application->id));
        $resubmitResponse->assertRedirect();

        $application->refresh();
        $this->assertEquals('submitted_to_office_assistant', $application->status);

        // 4. Office Assistant approves after revision
        $this->actingAs($this->officeAssistant)
            ->post(route('ex-supervisor.office-assistant.approve', $application->id))
            ->assertRedirect();

        $application->refresh();
        $this->assertEquals('submitted_to_secretary', $application->status);

        // 5. Secretary approves
        $this->actingAs($this->secretary)
            ->post(route('ex-supervisor.secretary.approve', $application->id))
            ->assertRedirect();

        $application->refresh();
        $this->assertEquals('secretary_approved_final', $application->status);
    }

    /**
     * Test 4: Complete Contractor Lifecycle (Company digitization & approval)
     */
    public function test_contractor_complete_approval_lifecycle(): void
    {
        $certNo = 'TEST-CONT-' . uniqid();
        $tradeLicense = 'TL-' . rand(100000, 999999);

        // 1. Create and submit Contractor application
        $application = ExContractorRenewApplication::create([
            'old_certificate_number' => $certNo,
            'book_number'            => 'BK-CONT-05',
            'class'                  => 'ABC',
            'company_name_en'        => 'BANGLADESH POWER TECH LTD',
            'company_name_bn'        => 'বাংলাদেশ পাওয়ার টেক লিঃ',
            'trade_license_number'   => $tradeLicense,
            'status'                 => 'submitted_to_office_assistant',
            'entry_by'               => $this->operator->id,
            'entry_at'               => now(),
            'last_updated_by'        => $this->operator->id,
            'last_updated_at'        => now(),
        ]);

        // 2. Office Assistant approves
        $this->actingAs($this->officeAssistant)
            ->post(route('ex-contractor.office-assistant.approve', $application->id))
            ->assertRedirect();

        $application->refresh();
        $this->assertEquals('submitted_to_secretary', $application->status);

        // 3. Secretary approves
        $this->actingAs($this->secretary)
            ->post(route('ex-contractor.secretary.approve', $application->id))
            ->assertRedirect();

        $application->refresh();
        $this->assertEquals('secretary_approved_final', $application->status);
    }

    /**
     * Test 5: Search & Large Scale Multi-Column Indexing Queries
     */
    public function test_search_and_indexing_queries(): void
    {
        $uniqueName = 'INDEX_TEST_CANDIDATE_' . uniqid();
        $bookNum = 'BOOK_IDX_' . rand(100, 999);

        ExElectricianRenewApplication::create([
            'old_certificate_number' => 'EL-IDX-' . uniqid(),
            'book_number'            => $bookNum,
            'class'                  => 'A',
            'applicant_name_en'      => $uniqueName,
            'status'                 => 'secretary_approved_final',
            'entry_by'               => $this->operator->id,
            'approved_by_secretary'  => $this->secretary->id,
            'approved_at_secretary'  => now(),
        ]);

        // Query by indexed applicant name and class
        $searchResponse = $this->actingAs($this->chairman)
            ->get(route('ex-electrician.chairman.index', [
                'search' => $uniqueName,
                'class'  => 'A',
                'book_number' => $bookNum,
            ]));

        $searchResponse->assertStatus(200);
        $searchResponse->assertSee($uniqueName);
    }

    /**
     * Test 6: Cross-Tab Notification System & Unread Counters
     */
    public function test_notifications_endpoints(): void
    {
        // Create sample notification for operator
        Notification::create([
            'user_id' => $this->operator->id,
            'title'   => 'Test Audit Notice',
            'message' => 'Application has been forwarded for review.',
            'type'    => 'info',
            'read_at' => null,
        ]);

        // Unread count endpoint
        $countResponse = $this->actingAs($this->operator)
            ->get(route('notifications.unread-count'));

        $countResponse->assertStatus(200);
        $countResponse->assertJsonStructure(['count']);
        $this->assertGreaterThanOrEqual(1, $countResponse->json('count'));

        // Mark all as read
        $markResponse = $this->actingAs($this->operator)
            ->postJson(route('notifications.mark-all-read'));

        $markResponse->assertStatus(200);

        // Verify unread count is now 0
        $countAfter = $this->actingAs($this->operator)
            ->get(route('notifications.unread-count'));
        $this->assertEquals(0, $countAfter->json('count'));
    }

    /**
     * Test 7: Embedded HTML User Guide & Documentation Rendering
     */
    public function test_embedded_user_guide_accessible(): void
    {
        $response = $this->actingAs($this->operator)
            ->get(route('user-guide.index'));

        $response->assertStatus(200);
        $response->assertSee('User Guide', false);
        $response->assertSee('Role & Permission Governance', false);
        $response->assertSee('Electrician Permit Lifecycle Flowchart', false);
        $response->assertSee('Supervisor Certificate Flow with Audit Rejection Loop', false);
        $response->assertSee('Annotated Ledger Scans', false);
        $response->assertSee('assets/img/guide/media_1793421353799_e7be040e.jpg', false);
    }
}
