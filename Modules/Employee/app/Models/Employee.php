<?php

namespace Modules\Employee\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CustomSoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes, CustomSoftDeletes;

    protected $table = 'employees';

    protected $fillable = [
        'employee_code',
        'branch_id',
        'department_id',
        'designation_id',
        'grade_id',
        'shift_id', 
        'reports_to',
        'employment_type',
        'joining_date',
        'confirmation_date',
        'probation_end_date',
        'last_working_day',
        'contract_end_date',
        'status',
        'portal_active',
        'created_by',
        'count_late_for_payroll',
        'count_overtime_for_payroll',
    ];

    protected $casts = [
        'probation_end_date' => 'date',
        'last_working_day' => 'date',
        'contract_end_date' => 'date',
        'portal_active' => 'boolean',
        'portal_last_login' => 'datetime',
        'count_late_for_payroll' => 'boolean',
        'count_overtime_for_payroll' => 'boolean',
    ];

    // Relationships
    public function personalInfo()
    {
        return $this->hasOne(EmployeePersonalInfo::class, 'employee_id');
    }

    public function addresses()
    {
        return $this->hasMany(EmployeeAddress::class, 'employee_id');
    }

    public function banking()
    {
        return $this->hasMany(EmployeeBanking::class, 'employee_id');
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    public function jobHistory()
    {
        return $this->hasMany(EmployeeJobHistory::class, 'employee_id');
    }

    public function educations()
    {
        return $this->hasMany(EmployeeEducation::class, 'employee_id');
    }

    public function experiences()
    {
        return $this->hasMany(EmployeeExperience::class, 'employee_id');
    }

    public function skills()
    {
        return $this->hasMany(EmployeeSkill::class, 'employee_id');
    }

    public function languages()
    {
        return $this->hasMany(EmployeeLanguage::class, 'employee_id');
    }

    public function awards()
    {
        return $this->hasMany(EmployeeAward::class, 'employee_id');
    }

    public function dependents()
    {
        return $this->hasMany(EmployeeDependent::class, 'employee_id');
    }

    public function rosters()
    {
        return $this->hasMany(EmployeeRoster::class, 'employee_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(EmployeeLeaveBalance::class, 'employee_id');
    }

    public function salaryStructure()
    {
        return $this->hasMany(EmployeeSalaryStructure::class, 'employee_id');
    }

    public function kpiScores()
    {
        return $this->hasMany(EmployeeKpiScore::class);
    }

    public function branch()
    {
        return $this->belongsTo('Modules\Branch\Models\Branch', 'branch_id');
    }

    public function department()
    {
        return $this->belongsTo('Modules\Department\Models\Department', 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo('Modules\Designation\Models\Designation', 'designation_id');
    }

    public function salaryGrade()
    {
        return $this->belongsTo('Modules\SalaryGrade\Models\SalaryGrade', 'grade_id');
    }

    public function shift()
    {
        return $this->belongsTo('Modules\Shift\Models\Shift', 'shift_id');
    }

    public function weekend()
    {
        return $this->hasOne('Modules\Employee\Models\EmployeeWeekend', 'employee_id', 'id');
    }

    public function attendanceRule()
    {
        return $this->hasOne('Modules\Employee\Models\EmployeeAttendanceRule', 'employee_id', 'id');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'reports_to');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'reports_to');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'employee_id');
    }


    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->personalInfo?->full_name ?? '';
    }

    public function getAgeAttribute()
    {
        if (!$this->personalInfo?->date_of_birth) {
            return null;
        }
        return \Carbon\Carbon::parse($this->personalInfo->date_of_birth)->age;
    }

    public function getFirstNameAttribute()
    {
        return $this->personalInfo?->first_name ?? '';
    }

    public function getLastNameAttribute()
    {
        return $this->personalInfo?->last_name ?? '';
    }

    public function getPhoneAttribute()
    {
        return $this->personalInfo?->phone ?? '';
    }

    public function getEmailAttribute()
    {
        return $this->personalInfo?->email ?? '';
    }

    public function getProfilePhotoAttribute()
    {
        return $this->personalInfo?->profile_photo ?? '';
    }

    public function getGenderAttribute()
    {
        return $this->personalInfo?->gender ?? '';
    }

    public function getDateOfBirthAttribute()
    {
        return $this->personalInfo?->date_of_birth ?? null;
    }

    public function getNationalityAttribute()
    {
        return $this->personalInfo?->nationality ?? '';
    }
}
