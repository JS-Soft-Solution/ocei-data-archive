<?php

namespace App\Policies;

use App\Models\ExSupervisorRenewApplication;
use App\Models\User;

class ExSupervisorRenewApplicationPolicy extends BasePermitPolicy
{
    /**
     * This policy extends the BasePermitPolicy which contains
     * all the authorization logic for the permit workflow.
     * 
     * Add any Supervisor-specific authorization rules here if needed.
     */
}
