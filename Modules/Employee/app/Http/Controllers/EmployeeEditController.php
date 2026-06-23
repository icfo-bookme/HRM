<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Branch\Models\Branch;
use Modules\Company\Models\Company;
use Modules\Department\Models\Department;
use Modules\Designation\Models\Designation;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeePersonalInfo;
use Modules\Employee\Models\EmployeeDocument;
use Modules\Employee\Models\SkillCategory;
use Modules\Employee\Services\EmployeeEditService;
use Modules\SalaryGrade\Models\SalaryGrade;
use Modules\Shift\Models\Shift;

class EmployeeEditController extends Controller
{
    protected EmployeeEditService $editService;

    public function __construct(EmployeeEditService $editService)
    {
        $this->editService = $editService;
    }

    /**
     * Show the employee edit page with all tabs.
     */
    public function edit($id)
    {
        $employee = Employee::with([
            'personalInfo',
            'addresses',
            'banking',
            'documents',
            'educations',
            'experiences',
            'jobHistory',
            'languages',
            'skills',
            'dependents',
            'branch',
            'department',
            'designation',
            'salaryGrade',
            'shift',
            'manager',
        ])->findOrFail($id);

        $companies = Company::all();
        $branches = Branch::all()->pluck('name', 'id');
        $departments = Department::all()->pluck('name', 'id');
        $designations = Designation::all()->pluck('title', 'id');
        $grades = SalaryGrade::all()->pluck('name', 'id');
        $shifts = Shift::all()->pluck('name', 'id');
        $managers = Employee::active()->where('id', '!=', $employee->id)->get();
        $skillCategories = SkillCategory::all();

        return view('employee::edit', compact(
            'employee', 'companies', 'branches', 'departments',
            'designations', 'grades', 'shifts', 'managers', 'skillCategories'
        ));
    }

    /**
     * Update basic info (Tab 1 - Core Info).
     */
    public function updateBasic(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'employee_code'     => ['required', 'string', 'max:50'],
            'branch_id'         => ['nullable', 'exists:branches,id'],
            'department_id'     => ['nullable', 'exists:departments,id'],
            'designation_id'    => ['nullable', 'exists:designations,id'],
            'grade_id'          => ['nullable', 'exists:salary_grades,id'],
            'shift_id'          => ['nullable', 'exists:shifts,id'],
            'reports_to'        => ['nullable', 'exists:employees,id'],
            'employment_type'   => ['nullable', 'string', 'max:50'],
            'joining_date'      => ['nullable', 'date'],
            'confirmation_date' => ['nullable', 'date'],
            'probation_end_date'=> ['nullable', 'date'],
            'last_working_day'  => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date'],
            'status'            => ['nullable', 'string', 'max:50'],
            'portal_active'     => ['nullable', 'boolean'],
        ]);

        $this->editService->updateBasic($employee, $validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Basic information updated successfully.',
        ]);
    }

    /**
     * Update personal info (Tab 2 - Personal Info).
     */
    public function updatePersonal(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'first_name'      => ['required', 'string', 'max:150'],
            'last_name'       => ['required', 'string', 'max:150'],
            'full_name'       => ['required', 'string', 'max:300'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'phone_2'         => ['nullable', 'string', 'max:20'],
            'email'           => ['nullable', 'email', 'max:200'],
            'date_of_birth'   => ['nullable', 'date'],
            'gender'          => ['nullable', 'string', 'max:50'],
            'nationality'     => ['nullable', 'string', 'max:100'],
            'marital_status'  => ['nullable', 'string', 'max:50'],
            'blood_group'     => ['nullable', 'string', 'max:10'],
            'father_name'     => ['nullable', 'string', 'max:200'],
            'mother_name'     => ['nullable', 'string', 'max:200'],
            'spouse_name'     => ['nullable', 'string', 'max:200'],
            'personal_email'  => ['nullable', 'email', 'max:200'],
            'personal_mobile' => ['nullable', 'string', 'max:20'],
            'religion'        => ['nullable', 'string', 'max:80'],
        ]);

        $this->editService->updatePersonal(
            $employee,
            $validated,
            $request->file('profile_photo'),
            $request->file('signature_file')
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Personal information updated successfully.',
        ]);
    }

    /**
     * Update address info (Tab 3 - Address Info).
     */
    public function updateAddresses(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'addresses'              => ['nullable', 'array'],
            'addresses.*.id'         => ['nullable', 'exists:employee_addresses,id'],
            'addresses.*.address_type' => ['required', 'string', 'in:present,permanent'],
            'addresses.*.house_no'   => ['nullable', 'string', 'max:100'],
            'addresses.*.road_no'    => ['nullable', 'string', 'max:100'],
            'addresses.*.road_name'  => ['nullable', 'string', 'max:255'],
            'addresses.*.village'    => ['nullable', 'string', 'max:200'],
            'addresses.*.area'       => ['nullable', 'string', 'max:200'],
            'addresses.*.post_office'=> ['nullable', 'string', 'max:200'],
            'addresses.*.postal_code'=> ['nullable', 'string', 'max:20'],
            'addresses.*.city'       => ['nullable', 'string', 'max:200'],
            'addresses.*.upazila'    => ['nullable', 'string', 'max:200'],
            'addresses.*.district'   => ['nullable', 'string', 'max:200'],
            'addresses.*.division'   => ['nullable', 'string', 'max:200'],
            'addresses.*.state'      => ['nullable', 'string', 'max:200'],
            'addresses.*.country'    => ['nullable', 'string', 'max:200'],
        ]);

        $this->editService->updateAddresses($employee, $validated['addresses'] ?? []);

        return response()->json([
            'status'  => 'success',
            'message' => 'Address information updated successfully.',
        ]);
    }

    /**
     * Update banking info (Tab 4 - Banking Info).
     */
    public function updateBanking(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'bank_name'          => ['nullable', 'string', 'max:255'],
            'bank_branch'        => ['nullable', 'string', 'max:255'],
            'bank_account'       => ['nullable', 'string', 'max:100'],
            'bank_routing'       => ['nullable', 'string', 'max:50'],
            'iban'               => ['nullable', 'string', 'max:50'],
            'swift_code'         => ['nullable', 'string', 'max:20'],
            'mfs_type'           => ['nullable', 'string', 'max:50'],
            'mfs_number'         => ['nullable', 'string', 'max:50'],
            'payment_method'     => ['nullable', 'string', 'max:50'],
            'is_primary'         => ['nullable', 'boolean'],
        ]);

        $this->editService->updateBanking($employee, $validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Banking information updated successfully.',
        ]);
    }

    /**
     * Update documents (Tab 5 - Documents).
     */
    public function updateDocuments(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'documents'                    => ['nullable', 'array'],
            'documents.*.id'               => ['nullable', 'exists:employee_documents,id'],
            'documents.*.category'         => ['required', 'string'],
            'documents.*.document_name'    => ['nullable', 'string', 'max:255'],
            'documents.*.document_number'  => ['nullable', 'string', 'max:100'],
            'documents.*.issuing_authority'=> ['nullable', 'string', 'max:255'],
            'documents.*.issue_date'       => ['nullable', 'date'],
            'documents.*.expiry_date'      => ['nullable', 'date'],
            'documents.*.notes'            => ['nullable', 'string'],
        ]);

        $uploadedFiles = [];
        foreach ($validated['documents'] ?? [] as $index => $doc) {
            $fileKey = 'documents.' . $index . '.document_file';
            if ($request->hasFile($fileKey)) {
                $uploadedFiles[$index] = $request->file($fileKey);
            }
        }

        $this->editService->updateDocuments($employee, $validated['documents'] ?? [], $uploadedFiles);

        return response()->json([
            'status'  => 'success',
            'message' => 'Documents updated successfully.',
        ]);
    }

    /**
     * Update education (Tab 6 - Education).
     */
    public function updateEducation(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'educations'                 => ['nullable', 'array'],
            'educations.*.id'            => ['nullable', 'exists:employee_education,id'],
            'educations.*.degree'        => ['required', 'string', 'max:255'],
            'educations.*.major_subject' => ['nullable', 'string', 'max:255'],
            'educations.*.institution'   => ['nullable', 'string', 'max:255'],
            'educations.*.board_university' => ['nullable', 'string', 'max:255'],
            'educations.*.passing_year'  => ['nullable', 'string', 'max:20'],
            'educations.*.result_type'   => ['nullable', 'string', 'max:50'],
            'educations.*.result_value'  => ['nullable', 'string', 'max:50'],
            'educations.*.duration_from' => ['nullable', 'date'],
            'educations.*.duration_to'   => ['nullable', 'date'],
            'educations.*.country'       => ['nullable', 'string', 'max:100'],
            'educations.*.is_highest'    => ['nullable', 'boolean'],
        ]);

        $uploadedFiles = [];
        foreach ($validated['educations'] ?? [] as $index => $edu) {
            $fileKey = 'educations.' . $index . '.certificate_file';
            if ($request->hasFile($fileKey)) {
                $uploadedFiles[$index] = $request->file($fileKey);
            }
        }

        $this->editService->updateEducation($employee, $validated['educations'] ?? [], $uploadedFiles);

        return response()->json([
            'status'  => 'success',
            'message' => 'Education records updated successfully.',
        ]);
    }

    /**
     * Update experience (Tab 7 - Experience).
     */
    public function updateExperience(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'experiences'                   => ['nullable', 'array'],
            'experiences.*.id'              => ['nullable', 'exists:employee_experience,id'],
            'experiences.*.company_name'    => ['required', 'string', 'max:255'],
            'experiences.*.designation'     => ['nullable', 'string', 'max:255'],
            'experiences.*.department'      => ['nullable', 'string', 'max:255'],
            'experiences.*.from_date'       => ['nullable', 'date'],
            'experiences.*.to_date'         => ['nullable', 'date'],
            'experiences.*.is_current'      => ['nullable', 'boolean'],
            'experiences.*.responsibilities'=> ['nullable', 'string'],
            'experiences.*.achievements'    => ['nullable', 'string'],
            'experiences.*.reason_for_leaving' => ['nullable', 'string', 'max:500'],
            'experiences.*.salary_scale'    => ['nullable', 'string', 'max:100'],
            'experiences.*.reference_name'  => ['nullable', 'string', 'max:200'],
            'experiences.*.reference_phone' => ['nullable', 'string', 'max:20'],
            'experiences.*.reference_email' => ['nullable', 'email', 'max:200'],
        ]);

        $uploadedFiles = [];
        foreach ($validated['experiences'] ?? [] as $index => $exp) {
            $fileKey = 'experiences.' . $index . '.certificate_file';
            if ($request->hasFile($fileKey)) {
                $uploadedFiles[$index] = $request->file($fileKey);
            }
        }

        $this->editService->updateExperience($employee, $validated['experiences'] ?? [], $uploadedFiles);

        return response()->json([
            'status'  => 'success',
            'message' => 'Experience records updated successfully.',
        ]);
    }

    /**
     * Update job history (Tab 8 - Job History).
     */
    public function updateJobHistory(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'id'              => ['nullable', 'exists:employee_job_history,id'],
            'effective_date'  => ['required', 'date'],
            'change_type'     => ['required', 'string', 'max:100'],
            'from_desig_id'   => ['nullable', 'exists:designations,id'],
            'to_desig_id'     => ['nullable', 'exists:designations,id'],
            'from_dept_id'    => ['nullable', 'exists:departments,id'],
            'to_dept_id'      => ['nullable', 'exists:departments,id'],
            'from_branch_id'  => ['nullable', 'exists:branches,id'],
            'to_branch_id'    => ['nullable', 'exists:branches,id'],
            'from_grade_id'   => ['nullable', 'exists:salary_grades,id'],
            'to_grade_id'     => ['nullable', 'exists:salary_grades,id'],
            'from_salary'     => ['nullable', 'numeric'],
            'to_salary'       => ['nullable', 'numeric'],
            'reason'          => ['nullable', 'string', 'max:500'],
            'remarks'         => ['nullable', 'string'],
        ]);

        $this->editService->updateJobHistory($employee, $validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Job history updated successfully.',
        ]);
    }

    /**
     * Update languages (Tab 9 - Languages).
     */
    public function updateLanguages(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'languages'                => ['nullable', 'array'],
            'languages.*.id'           => ['nullable', 'exists:employee_languages,id'],
            'languages.*.language_name'=> ['required', 'string', 'max:100'],
            'languages.*.proficiency'  => ['nullable', 'string', 'max:50'],
            'languages.*.can_read'     => ['nullable', 'boolean'],
            'languages.*.can_write'    => ['nullable', 'boolean'],
            'languages.*.can_speak'    => ['nullable', 'boolean'],
        ]);

        $this->editService->updateLanguages($employee, $validated['languages'] ?? []);

        return response()->json([
            'status'  => 'success',
            'message' => 'Languages updated successfully.',
        ]);
    }

    /**
     * Update skills (Tab 10 - Skills).
     */
    public function updateSkills(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'skills'                    => ['nullable', 'array'],
            'skills.*.id'               => ['nullable', 'exists:employee_skills,id'],
            'skills.*.category_id'      => ['nullable', 'exists:skill_categories,id'],
            'skills.*.skill_name'       => ['required', 'string', 'max:200'],
            'skills.*.description'      => ['nullable', 'string'],
            'skills.*.proficiency'      => ['nullable', 'string', 'max:50'],
            'skills.*.years_of_experience' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'skills.*.last_used_date'   => ['nullable', 'date'],
            'skills.*.certification'    => ['nullable', 'string', 'max:255'],
            'skills.*.is_active'        => ['nullable', 'boolean'],
        ]);

        $this->editService->updateSkills($employee, $validated['skills'] ?? []);

        return response()->json([
            'status'  => 'success',
            'message' => 'Skills updated successfully.',
        ]);
    }

    /**
     * Update dependents (Tab 11 - Dependents).
     */
    public function updateDependents(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'dependents'                => ['nullable', 'array'],
            'dependents.*.id'           => ['nullable', 'exists:employee_dependents,id'],
            'dependents.*.full_name'    => ['required', 'string', 'max:255'],
            'dependents.*.relation'     => ['required', 'string', 'max:100'],
            'dependents.*.date_of_birth'=> ['nullable', 'date'],
            'dependents.*.nid_number'   => ['nullable', 'string', 'max:50'],
            'dependents.*.phone'        => ['nullable', 'string', 'max:20'],
            'dependents.*.email'        => ['nullable', 'email', 'max:200'],
            'dependents.*.occupation'   => ['nullable', 'string', 'max:200'],
            'dependents.*.is_nominee'   => ['nullable', 'boolean'],
            'dependents.*.nominee_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'dependents.*.priority_order'  => ['nullable', 'integer', 'min:1'],
        ]);

        $this->editService->updateDependents($employee, $validated['dependents'] ?? []);

        return response()->json([
            'status'  => 'success',
            'message' => 'Dependents updated successfully.',
        ]);
    }
}