<?php
// app/Http/Controllers/ExElectrician/AdminController.php

namespace App\Http\Controllers\ExElectrician;

use App\Http\Controllers\Controller;
use App\Models\ExElectricianRenewApplication;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function update(Request $request, ExElectricianRenewApplication $record)
    {
        $this->authorize('update', $record); // Allows override
        $record->update($request->validated());

        return redirect()->back()->with('success', 'Updated (override logged)');
    }

    public function overrideStatus(Request $request, ExElectricianRenewApplication $record)
    {
        $request->validate(['status' => 'required|in:draft,submitted_to_office_assistant,...']); // All states
        $record->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status overridden');
    }
}
