<?php

namespace App\Policies;

use App\Models\ExElectricianRenewApplication;
use App\Models\User;

class ExElectricianRenewApplicationPolicy extends BasePermitPolicy
{
    /**
     * This policy extends the BasePermitPolicy which contains
     * all the authorization logic for the permit workflow.
     * 
     * Add any Electrician-specific authorization rules here if needed.
     */
}
