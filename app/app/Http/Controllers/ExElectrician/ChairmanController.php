<?php
// app/Http/Controllers/ExElectrician/ChairmanController.php

namespace App\Http\Controllers\ExElectrician;

use App\Http\Controllers\Controller;
use App\Models\ExElectricianRenewApplication;
use Illuminate\Http\Request;

class ChairmanController extends Controller
{
    public function approved(Request $request)
    {
        $query = ExElectricianRenewApplication::with(['secretaryApprover', 'entryBy'])
            ->approved(); // Only final

        // Filters: district, dates, etc.
        return view('ex-electrician.chairman.approved', ['applications' => $query->paginate(20)]);
    }
}
