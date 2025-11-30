<?php
// app/Http/Controllers/ExElectrician/OperatorController.php

namespace App\Http\Controllers\ExElectrician;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreElectricianRequest; // Per-tab/full validation
use App\Models\ExElectricianRenewApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OperatorController extends Controller
{
    public function index(Request $request)
    {
        $query = ExElectricianRenewApplication::with(['entryBy', 'attachments'])
            ->forOperator(Auth::user())
            ->when($request->status, fn($q) => $q->byStatus($request->status));

        return view('ex-electrician.operator.index', ['applications' => $query->paginate(20)]);
    }

    public function create()
    {
        return view('ex-electrician.operator.form', ['tabs' => $this->getTabs(), 'record' => null]);
    }

    public function store(StoreElectricianRequest $request)
    {
        $record = ExElectricianRenewApplication::create($request->validated() + ['status' => 'draft']);
        return redirect()->route('ex-electrician.operator.applications.edit', $record)->with('success', 'Draft saved');
    }

    public function edit(ExElectricianRenewApplication $record)
    {
        $this->authorize('update', $record);
        session(['current_tab' => $request->tab ?? 1]);

        return view('ex-electrician.operator.form', [
            'record' => $record->load(['attachments']),
            'tabs' => $this->getTabs(),
            'currentTab' => session('current_tab', 1)
        ]);
    }

    public function saveTab(Request $request, ExElectricianRenewApplication $record, int $tab)
    {
        $this->authorize('update', $record);
        $validator = $this->getTabValidator($tab, $request->all(), $record); // Per-tab rules
        $validator->validate();

        $record->update($request->only($this->getTabFields($tab)));
        session(['current_tab' => $tab + 1]);

        if ($request->has('save_next') && $tab < 5) {
            return redirect()->route('ex-electrician.operator.applications.edit', $record)
                ->with('tab', $tab + 1)->with('success', 'Tab saved & next');
        }

        return redirect()->back()->with('success', 'Tab saved as draft');
    }

    public function submit(ExElectricianRenewApplication $record)
    {
        $this->authorize('submit', $record);
        $fullValidator = $this->getFullValidator($record); // All tabs required
        $fullValidator->validate();

        $nextStatus = $record->status === 'secretary_rejected' ? 'submitted_to_secretary' : 'submitted_to_office_assistant';
        $record->update(['status' => $nextStatus]);

        return redirect()->route('ex-electrician.operator.applications.index')->with('success', 'Submitted');
    }

    public function bulkSubmit(Request $request)
    {
        $ids = $request->input('selected', []);
        $validIds = [];
        foreach ($ids as $id) {
            $record = ExElectricianRenewApplication::findOrFail($id);
            $this->authorize('submit', $record);
            if ($this->isReadyForSubmit($record)) { // Custom check: all required filled
                $record->update(['status' => 'submitted_to_office_assistant']);
                $validIds[] = $id;
            }
        }

        return redirect()->back()->with('success', "Submitted " . count($validIds) . " records");
    }

    // Helpers
    private function getTabs(): array
    {
        return [
            1 => 'Basic Information',
            2 => 'Address & Contact',
            3 => 'Certificate & Permit Details',
            4 => 'Employment Details',
            5 => 'Attachments & Review'
        ];
    }

    public function search(Request $request)
    {
        // Your search logic here
        $query = ExElectricianRenewApplication::whereNull('entry_by'); // Legacy unclaimed

        if ($request->filled('old_certificate_number')) {
            $query->where('old_certificate_number', 'like', '%' . $request->old_certificate_number . '%');
        }

        $results = $query->with('attachments')->paginate(20);

        return view('ex-electrician.operator.search-legacy', compact('results'));
    }

    private function getTabFields(int $tab): array { /* Return fields per tab */ }
    private function getTabValidator(int $tab, array $data, $record) { /* Validator per tab */ }
    private function getFullValidator($record) { /* Full form validator */ }
    private function isReadyForSubmit($record): bool { /* Check all required */ }
}
