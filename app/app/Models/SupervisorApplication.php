<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasWorkflow;

class SupervisorApplication extends Model
{
    use SoftDeletes, HasWorkflow;
    protected $guarded = ['id'];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'last_renewal_date' => 'date',
        // date_of_birth is string in SQL provided, keeping dynamic cast might be safer or fix in migration
        'entry_at' => 'datetime',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];
}
