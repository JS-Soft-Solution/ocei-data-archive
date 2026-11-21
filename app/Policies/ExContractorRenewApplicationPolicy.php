<?php

namespace App\Policies;

use App\Models\ExContractorRenewApplication;
use App\Models\User;

class ExContractorRenewApplicationPolicy extends BasePermitPolicy
{
    /**
     * This policy extends the BasePermitPolicy which contains
     * all the authorization logic for the permit workflow.
     * 
     * Add any Contractor-specific authorization rules here if needed.
     */
}
