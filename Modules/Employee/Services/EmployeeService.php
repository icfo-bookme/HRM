<?php

namespace Modules\Employee\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeePersonalInfo;
use Modules\Employee\Models\EmployeeAddress;
use Modules\Employee\Models\EmployeeBanking;
use Modules\Employee\Models\EmployeeDependent;
use Modules\Employee\Models\EmployeeDocument;
use Modules\Employee\Models\EmployeeEducation;
use Modules\Employee\Models\EmployeeExperience;
use Modules\Employee\Models\EmployeeJobHistory;
use Modules\Employee\Models\EmployeeLanguage;
use Modules\Employee\Models\EmployeeSkill;
use Illuminate\Support\Collection;
use Yajra\DataTables\DataTables;

class EmployeeService
{
    public function saveWizardStep(array $data, string $step): array
    {
        session()->put("employee_creation.{$step}", $data);

        return [
            'status' => 'success',
            'message' => ucfirst($step) . ' saved successfully.',
            'step' => $step,
        ];
    }

    public function getWizardSummary(): array
    {
        return session('employee_creation', []);
    }

    public function clearWizardData(): void
    {
        session()->forget('employee_creation');
    }

    public function getEmployeeDataTable($request)
    {
        $query = Employee::with(['personalInfo', 'department', 'designation'])
            ->select('employees.*')->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)

            // Index column
            ->addIndexColumn()

            // Employee Name (from personalInfo relation) - clickable to profile
            ->addColumn('employee', function ($employee) {
                $name = e($employee->full_name);
                $profileUrl = route('employee.profile', $employee->id);
                return '<a href="' . $profileUrl . '" class="text-indigo-600 hover:text-indigo-900 font-medium hover:underline">' . $name . '</a>';
            })

            // Department safe
            ->addColumn('department', function ($employee) {
                return $employee->department->name ?? 'N/A';
            })

            // Designation safe
            ->addColumn('designation', function ($employee) {
                return $employee->designation->title ?? 'N/A';
            })

            // Status badge
            ->editColumn('status', function ($employee) {
                $statusColors = [
                    'Active' => 'text-green-700 bg-green-50',
                    'Inactive' => 'text-slate-700 bg-slate-100',
                    'On Leave' => 'text-amber-700 bg-amber-50',
                    'Suspended' => 'text-red-700 bg-red-50',
                    'Terminated' => 'text-red-700 bg-red-50',
                    'Resigned' => 'text-orange-700 bg-orange-50',
                    'Retired' => 'text-purple-700 bg-purple-50',
                ];
                $color = $statusColors[$employee->status] ?? 'text-slate-700 bg-slate-100';
                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ' . $color . '">' . e($employee->status) . '</span>';
            })

            // Email from personalInfo relation
            ->addColumn('email', function ($employee) {
                return $employee->personalInfo->email ?? $employee->personalInfo->personal_email ?? '';
            })

            // Action buttons
            ->addColumn('action', function ($employee) {
                $user = Auth::user();
                $html = '';

                // Always show profile view button if user has view permission
                if ($user && $user->hasPermission('employees.view')) {
                    $profileUrl = route('employee.profile', $employee->id);
                    $html .= '<a href="' . $profileUrl . '"
                       class="text-blue-600 hover:text-blue-900 font-medium mx-1" title="View Profile">
                       <i class="fa-regular fa-eye"></i>
                    </a>';
                }

                // Edit/Delete buttons - only if user has action access permission
                if ($user && $user->hasPermission('employee-list-action-access')) {
                    $editUrl = route('employee.edit', $employee->id);
                    $html .= '<a href="' . $editUrl . '"
                       class="text-indigo-600 hover:text-indigo-900 font-medium mx-1" title="Edit">
                       <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <button onclick="employeeDelete(' . $employee->id . ')"
                       class="text-red-600 hover:text-red-900 font-medium mx-1" title="Delete">
                       <i class="fa-solid fa-trash"></i>
                    </button>';
                }

                return $html ? '<div class="flex items-center gap-1">' . $html . '</div>' : '';
            })

            // Allow HTML rendering
            ->rawColumns(['employee', 'status', 'action'])

            // Final response (IMPORTANT)
            ->make(true);
    }

    public function finalizeEmployee(array $wizardData): array
    {
        try {
            return DB::transaction(function () use ($wizardData) {
                if (empty($wizardData['step1'])) {
                    throw new \Exception('Employee core data is missing.');
                }

                // Step 1: Create employee
                $employee = Employee::create($wizardData['step1']);

                // Step 2: Personal Info
                if (!empty($wizardData['step2'])) {
                    $personalInfoData = $wizardData['step2'];

                    EmployeePersonalInfo::create(array_merge($personalInfoData, [
                        'employee_id' => $employee->id,
                    ]));
                }

                // Step 3: Addresses
                if (!empty($wizardData['step3']['addresses'])) {
                    foreach ($wizardData['step3']['addresses'] as $address) {
                        EmployeeAddress::create(array_merge($address, [
                            'employee_id' => $employee->id,
                        ]));
                    }
                }

                // Step 4: Banking
                if (!empty($wizardData['step4']) && is_array($wizardData['step4'])) {
                    // Banking is stored as a single record (not array of records)
                    $bankingData = $wizardData['step4'];
                    if (!empty($bankingData['bank_name']) || !empty($bankingData['mfs_type'])) {
                        EmployeeBanking::create(array_merge($bankingData, [
                            'employee_id' => $employee->id,
                        ]));
                    }
                }

                // Step 5: Documents (stored as array of documents under ['documents'])
                if (!empty($wizardData['step5']['documents']) && is_array($wizardData['step5']['documents'])) {
                    foreach ($wizardData['step5']['documents'] as $document) {
                        if (!empty($document['category']) || !empty($document['file_path'])) {
                            EmployeeDocument::create(array_merge($document, [
                                'employee_id' => $employee->id,
                            ]));
                        }
                    }
                }

                // Step 6: Education (multiple entries)
                if (!empty($wizardData['step6']['educations']) && is_array($wizardData['step6']['educations'])) {
                    foreach ($wizardData['step6']['educations'] as $education) {
                        if (!empty($education['degree'])) {
                            EmployeeEducation::create(array_merge($education, [
                                'employee_id' => $employee->id,
                            ]));
                        }
                    }
                }

                // Step 7: Experience (multiple entries)
                if (!empty($wizardData['step7']['experiences']) && is_array($wizardData['step7']['experiences'])) {
                    foreach ($wizardData['step7']['experiences'] as $experience) {
                        if (!empty($experience['company_name'])) {
                            EmployeeExperience::create(array_merge($experience, [
                                'employee_id' => $employee->id,
                            ]));
                        }
                    }
                }

                // Step 8: Job History
                if (!empty($wizardData['step8']) && !empty($wizardData['step8']['effective_date']) && !empty($wizardData['step8']['change_type'])) {
                    EmployeeJobHistory::create(array_merge($wizardData['step8'], [
                        'employee_id' => $employee->id,
                    ]));
                }

                // Step 9: Languages (multiple entries)
                if (!empty($wizardData['step9']['languages']) && is_array($wizardData['step9']['languages'])) {
                    foreach ($wizardData['step9']['languages'] as $language) {
                        if (!empty($language['language_name'])) {
                            EmployeeLanguage::create(array_merge($language, [
                                'employee_id' => $employee->id,
                            ]));
                        }
                    }
                }

                // Step 10: Skill
                if (!empty($wizardData['step10']) && !empty($wizardData['step10']['skill_name'])) {
                    EmployeeSkill::create(array_merge($wizardData['step10'], [
                        'employee_id' => $employee->id,
                    ]));
                }

                // Step 11: Dependent
                if (!empty($wizardData['step11']) && !empty($wizardData['step11']['full_name']) && !empty($wizardData['step11']['relation'])) {
                    EmployeeDependent::create(array_merge($wizardData['step11'], [
                        'employee_id' => $employee->id,
                    ]));
                }

                $this->clearWizardData();

                return [
                    'status' => 'success',
                    'message' => 'Employee created successfully.',
                    'employee' => $employee->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error saving employee: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get employees with upcoming birthdays within the next X days.
     */
    public function getUpcomingBirthdays(int $days = 7): Collection
    {
        $today = \Carbon\Carbon::today();
        $endDate = \Carbon\Carbon::today()->addDays($days);

        // Get month-day range (handle year-end crossing)
        $startMD = (int) $today->format('md');
        $endMD = (int) $endDate->format('md');

        $employees = Employee::whereHas('personalInfo', function ($q) use ($startMD, $endMD, $today, $endDate) {
            $q->whereNotNull('date_of_birth');

            if ($startMD <= $endMD) {
                // Normal range (no year crossing)
                $q->whereRaw("DATE_FORMAT(date_of_birth, '%m%d') BETWEEN ? AND ?", [$startMD, $endMD]);
            } else {
                // Year-end crossing (e.g., Dec 25 - Jan 3)
                $q->whereRaw("DATE_FORMAT(date_of_birth, '%m%d') BETWEEN ? AND 1231", [$startMD])
                    ->orWhereRaw("DATE_FORMAT(date_of_birth, '%m%d') BETWEEN 0101 AND ?", [$endMD]);
            }
        })
            ->active()
            ->with('personalInfo', 'department', 'designation')
            ->get();

        // Map to add computed fields: birthday_this_year, days_until
        return $employees->map(function ($employee) {
            $dob = $employee->personalInfo?->date_of_birth;
            if (!$dob) return null;

            // Create a Carbon date for this year's birthday
            $birthdayThisYear = \Carbon\Carbon::createFromDate(
                now()->year,
                $dob->month,
                $dob->day
            );

            // If it's already passed this year, use next year
            if ($birthdayThisYear->isPast()) {
                $birthdayThisYear->addYear();
            }

            $daysUntil = now()->startOfDay()->diffInDays($birthdayThisYear, false);

            // Only include if within the specified window
            if ($daysUntil < 0 || $daysUntil > 7) {
                return null;
            }

            return (object) [
                'employee'          => $employee,
                'employee_name'     => $employee->full_name,
                'employee_id'       => $employee->id,
                'profile_photo'     => $employee->profile_photo,
                'department'        => $employee->department?->name ?? '',
                'designation'       => $employee->designation?->title ?? '',
                'date_of_birth'     => $dob,
                'birthday_date'     => $birthdayThisYear,
                'days_until'        => $daysUntil,
            ];
        })->filter()->sortBy('days_until')->values();
    }

    public function getEmployeeById(int $id): array
    {
        try {
            $employee = Employee::with(['personalInfo', 'addresses', 'banking'])->findOrFail($id);

            return [
                'status' => 'success',
                'employee' => $employee,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Employee not found.',
            ];
        }
    }
}
