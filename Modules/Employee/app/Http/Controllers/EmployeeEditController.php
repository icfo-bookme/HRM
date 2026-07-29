<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateAddressesRequest;
use App\Http\Requests\Employee\UpdateBankingRequest;
use App\Http\Requests\Employee\UpdateBasicRequest;
use App\Http\Requests\Employee\UpdateDependentsRequest;
use App\Http\Requests\Employee\UpdateDocumentsRequest;
use App\Http\Requests\Employee\UpdateEducationRequest;
use App\Http\Requests\Employee\UpdateExperienceRequest;
use App\Http\Requests\Employee\UpdateJobHistoryRequest;
use App\Http\Requests\Employee\UpdateLanguagesRequest;
use App\Http\Requests\Employee\UpdatePersonalRequest;
use App\Http\Requests\Employee\UpdateSkillsRequest;
use Modules\Branch\Models\Branch;
use Modules\Company\Models\Company;
use Modules\Department\Models\Department;
use Modules\Designation\Models\Designation;
use Modules\Employee\Models\Employee;
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
    public function updateBasic(UpdateBasicRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $this->editService->updateBasic($employee, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Basic information updated successfully.',
        ]);
    }

    /**
     * Update personal info (Tab 2 - Personal Info).
     */
    public function updatePersonal(UpdatePersonalRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $this->editService->updatePersonal(
            $employee,
            $request->validated(),
            $request->file('profile_photo'),
            $request->file('signature_file')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Personal information updated successfully.',
        ]);
    }

    /**
     * Update address info (Tab 3 - Address Info).
     */
    public function updateAddresses(UpdateAddressesRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $this->editService->updateAddresses($employee, $request->validated()['addresses'] ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Address information updated successfully.',
        ]);
    }

    /**
     * Update banking info (Tab 4 - Banking Info).
     */
    public function updateBanking(UpdateBankingRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $this->editService->updateBanking($employee, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Banking information updated successfully.',
        ]);
    }

    /**
     * Update documents (Tab 5 - Documents).
     */
    public function updateDocuments(UpdateDocumentsRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $uploadedFiles = [];
        $validated = $request->validated();
        foreach ($validated['documents'] ?? [] as $index => $doc) {
            $fileKey = 'documents.'.$index.'.document_file';
            if ($request->hasFile($fileKey)) {
                $uploadedFiles[$index] = $request->file($fileKey);
            }
        }

        $this->editService->updateDocuments($employee, $validated['documents'] ?? [], $uploadedFiles);

        return response()->json([
            'status' => 'success',
            'message' => 'Documents updated successfully.',
        ]);
    }

    /**
     * Update education (Tab 6 - Education).
     */
    public function updateEducation(UpdateEducationRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $uploadedFiles = [];
        $validated = $request->validated();
        foreach ($validated['educations'] ?? [] as $index => $edu) {
            $fileKey = 'educations.'.$index.'.certificate_file';
            if ($request->hasFile($fileKey)) {
                $uploadedFiles[$index] = $request->file($fileKey);
            }
        }

        $this->editService->updateEducation($employee, $validated['educations'] ?? [], $uploadedFiles);

        return response()->json([
            'status' => 'success',
            'message' => 'Education records updated successfully.',
        ]);
    }

    /**
     * Update experience (Tab 7 - Experience).
     */
    public function updateExperience(UpdateExperienceRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $uploadedFiles = [];
        $validated = $request->validated();
        foreach ($validated['experiences'] ?? [] as $index => $exp) {
            $fileKey = 'experiences.'.$index.'.certificate_file';
            if ($request->hasFile($fileKey)) {
                $uploadedFiles[$index] = $request->file($fileKey);
            }
        }

        $this->editService->updateExperience($employee, $validated['experiences'] ?? [], $uploadedFiles);

        return response()->json([
            'status' => 'success',
            'message' => 'Experience records updated successfully.',
        ]);
    }

    /**
     * Update job history (Tab 8 - Job History).
     */
    public function updateJobHistory(UpdateJobHistoryRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $this->editService->updateJobHistory($employee, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Job history updated successfully.',
        ]);
    }

    /**
     * Update languages (Tab 9 - Languages).
     */
    public function updateLanguages(UpdateLanguagesRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $this->editService->updateLanguages($employee, $request->validated()['languages'] ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Languages updated successfully.',
        ]);
    }

    /**
     * Update skills (Tab 10 - Skills).
     */
    public function updateSkills(UpdateSkillsRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $this->editService->updateSkills($employee, $request->validated()['skills'] ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Skills updated successfully.',
        ]);
    }

    /**
     * Update dependents (Tab 11 - Dependents).
     */
    public function updateDependents(UpdateDependentsRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $this->editService->updateDependents($employee, $request->validated()['dependents'] ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Dependents updated successfully.',
        ]);
    }
}
