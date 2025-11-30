<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'otp',
        'otp_status',

        'full_name',
        'business_organization_name',
        'full_name_bn',

        'father_name',
        'spouse_name',
        'father_name_bn',
        'spouse_name_bn',

        'mother_name',
        'mother_name_bn',

        'nid_no',
        'dob',

        'email',
        'password',
        'mobile_no',
        'gender',

        'per_village',
        'per_road',
        'per_division',
        'per_district',
        'per_upozila',
        'per_post_code',
        'per_post_office',

        'pre_village',
        'pre_road',
        'pre_division',
        'pre_district',
        'pre_upozila',
        'pre_post_code',
        'pre_post_office',

        'nid_image',
        'applicant_image',
        'applicant_sign',

        'admin_type',
        'admin_designation',
        'admin_designation_bn',

        'temporary_pass',
        'otp_password',
        'temp_password',

        'mobile_no_err_letter',
        'email_err_letter',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'temporary_pass',
        'temp_password',
        'otp',
        'otp_password',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'dob' => 'date',
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',

        // Laravel 10+ will auto-hash when assigning:
        // $user->password = 'plain';
        'password' => 'hashed',
    ]
    ;

    /**
     * Get name attribute (accessor for full_name).
     */
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    public function isSuperAdmin()
    {
        return $this->admin_type === 'super_admin' || $this->admin_type === 'system_admin';
    }

    public function hasRole($role)
    {
        if ($this->isSuperAdmin())
            return true; // Super admin accesses everything

        if (is_array($role)) {
            return in_array($this->admin_type, $role);
        }
        return $this->admin_type === $role;
    }
}
