<?php

namespace App\Http\Controllers\Permits\Contractor;

use App\Http\Controllers\Controller;
use App\Models\ExContractorRenewApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractorOperatorController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ExContractorRenewApplication::class);

        $query = ExContractorRenewApplication::query()
            ->where('entry_by', Auth::id())
            ->with(['attachments', 'entryBy']);

        if ($search = $request->get('search')) {
            $query->search($search);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $applications = $query->latest()->paginate(20);

        return view('permits.contractor.operator.index', compact('applications'));
    }

    public function create()
    {
        $this->authorize('create', ExContractorRenewApplication::class);
        return view('permits.contractor.operator.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', ExContractorRenewApplication::class);

        $validated = $this->validateApplication($request);

        DB::beginTransaction();
        try {
            $application = ExContractorRenewApplication::create([
                ...$validated,
                'status' => 'draft',
                'entry_by' => Auth::id(),
                'entry_at' => now(),
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('ex-contractor.operator.edit', $application)
                ->with('success', 'Contractor application created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create application: ' . $e->getMessage());
        }
    }

    public function edit(ExContractorRenewApplication $application)
    {
        $this->authorize('update', $application);
        $application->load('attachments');
        return view('permits.contractor.operator.edit', compact('application'));
    }

    public function update(Request $request, ExContractorRenewApplication $application)
    {
        $this->authorize('update', $application);

        $validated = $this->validateApplication($request, $application);

        DB::beginTransaction();
        try {
            $application->update([
                ...$validated,
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Application updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    public function submit(ExContractorRenewApplication $application)
    {
        $this->authorize('submitToOfficeAssistant', $application);

        if ($application->attachments()->count() === 0) {
            return back()->with('error', 'Please upload at least one attachment.');
        }

        DB::beginTransaction();
        try {
            $newStatus = ($application->status === 'secretary_rejected')
                ? 'submitted_to_secretary'
                : 'submitted_to_office_assistant';

            $application->update([
                'status' => $newStatus,
                'last_updated_by' => Auth::id(),
                'last_updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('ex-contractor.operator.index')->with('success', 'Application submitted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit: ' . $e->getMessage());
        }
    }

    protected function validateApplication(Request $request, ?ExContractorRenewApplication $application = null): array
    {
        $uniqueRule = $application
            ? 'unique:ex_contractor_renew_applications,old_certificate_number,' . $application->id
            : 'unique:ex_contractor_renew_applications,old_certificate_number';

        return $request->validate([
            'old_certificate_number' => 'required|string|max:100|' . $uniqueRule,
            'company_name_bn' => 'nullable|string|max:255',
            'company_name_en' => 'nullable|string|max:255',
            'owner_shareholder_name' => 'nullable|string|max:255',
            'company_type' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'representative_name' => 'nullable|string|max:255',
            'representative_designation' => 'nullable|string|max:255',
            'elb_certified_supervisor_no' => 'nullable|string|max:255',
            'supervisor_acknowledgement' => 'nullable|integer',
            'boa_village' => 'nullable|string|max:150',
            'boa_road' => 'nullable|string|max:150',
            'boa_post_office' => 'nullable|string|max:255',
            'boa_post_code' => 'nullable|integer',
            'boa_upozila' => 'nullable|string|max:150',
            'boa_district' => 'nullable|string|max:150',
            'boa_division' => 'nullable|string|max:150',
            'bra_village' => 'nullable|string|max:150',
            'bra_road' => 'nullable|string|max:150',
            'bra_post_office' => 'nullable|string|max:255',
            'bra_post_code' => 'nullable|integer',
            'bra_upozila' => 'nullable|string|max:150',
            'bra_district' => 'nullable|string|max:150',
            'bra_division' => 'nullable|string|max:150',
            'company_registration_number' => 'nullable|string|max:255',
            'trade_license_number' => 'nullable|string|max:255',
            'et_serial_no' => 'nullable|string|max:255',
            'et_manufacturer_name' => 'nullable|string|max:255',
            'et_country_origin' => 'nullable|string|max:255',
            'mg_serial_no' => 'nullable|string|max:255',
            'mg_manufacturer_name' => 'nullable|string|max:255',
            'mg_country_origin' => 'nullable|string|max:255',
            'cm_serial_no' => 'nullable|string|max:255',
            'cm_manufacturer_name' => 'nullable|string|max:255',
            'cm_country_origin' => 'nullable|string|max:255',
            'certificate_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'renewal_period' => 'nullable|integer',
            'last_renewal_date' => 'nullable|date',
            'result' => 'nullable|string|max:15',
        ]);
    }
}
