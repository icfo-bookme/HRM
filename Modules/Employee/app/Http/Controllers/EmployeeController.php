<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Employee\Http\Requests\StoreEmployeeStepFiveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Branch\Models\Branch;
use Modules\Company\Models\Company;
use Modules\Department\Models\Department;
use Modules\Designation\Models\Designation;
use Modules\Employee\Http\Requests\StoreEmployeeStepOneRequest;
use Modules\Employee\Http\Requests\StoreEmployeeStepTwoRequest;
use Modules\Employee\Http\Requests\StoreEmployeeStepThreeRequest;
use Modules\Employee\Http\Requests\StoreEmployeeStepFourRequest;
use Modules\Employee\Http\Requests\StoreEmployeeStepSixRequest;
use Modules\Employee\Http\Requests\StoreEmployeeStepSevenRequest;
use Modules\Employee\Http\Requests\StoreEmployeeStepEightRequest;
use Modules\Employee\Http\Requests\StoreEmployeeStepNineRequest;
use Modules\Employee\Http\Requests\StoreEmployeeStepTenRequest;
use Modules\Employee\Http\Requests\StoreEmployeeStepElevenRequest;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\SkillCategory;
use Modules\Employee\Services\EmployeeService;
use Modules\SalaryGrade\Models\SalaryGrade;
use Modules\Shift\Models\Shift;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $departments = Department::all()->pluck('name', 'id')->toArray();
        $designations = Designation::all()->pluck('title', 'id')->toArray();

        return view('employee::index', compact('departments', 'designations'));
    }

    public function getEmployeesDataTable(Request $request)
    {
        return $this->employeeService->getEmployeeDataTable($request);
    }

    public function create()
    {
        return redirect()->route('employee.create.step1');
    }

    public function createStepOne()
    {
        $companies = Company::all();
        $branches = Branch::all()->pluck('name', 'id');
        $departments = Department::all()->pluck('name', 'id');
        $designations = Designation::all()->pluck('title', 'id');
        $grades = SalaryGrade::all()->pluck('name', 'id');
        $shifts = Shift::all()->pluck('name', 'id');
        $employeeCode = Employee::max('id') + 1;
        $employeeCode = 'EMP-' . str_pad($employeeCode, 4, '0', STR_PAD_LEFT);
        $employee = Employee::active()->get();

        $data = session('employee_creation.step1', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step1', [
            'data' => $data,
            'companies' => $companies,
            'branches' => $branches,
            'departments' => $departments,
            'designations' => $designations,
            'grades' => $grades,
            'shifts' => $shifts,
            'employeeCode' => $employeeCode,
            'employee' => $employee,
        ]);
    }
    public function storeStepOne(StoreEmployeeStepOneRequest $request)
    {
        $validated = $request->validated();
        $existingData = session('employee_creation.step1', []);

        if ($request->hasFile('profile_photo')) {
            if (!empty($existingData['profile_photo'])) {
                Storage::disk('public')->delete($existingData['profile_photo']);
            }

            $validated['profile_photo'] = $request->file('profile_photo')->store('employee/profile-photos', 'public');
        } elseif (!empty($existingData['profile_photo'])) {
            $validated['profile_photo'] = $existingData['profile_photo'];
        }

        // full_name will be set properly from step2 (personal info) or default to employee_code
        $validated['full_name'] = $validated['full_name'] ?? $validated['employee_code'];

        $this->employeeService->saveWizardStep($validated, 'step1');

        return redirect()->route('employee.create.step2');
    }

    public function createStepTwo()
    {
        $data = session('employee_creation.step2', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step2', [
            'data' => $data,
        ]);
    }

    public function storeStepTwo(StoreEmployeeStepTwoRequest $request)
    {
        if ($request->boolean('skip')) {
            $this->employeeService->saveWizardStep([], 'step2');

            return redirect()->route('employee.create.step3');
        }

        $data = $request->validated();

        $existingData = session('employee_creation.step2', []);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('employee/profile-photos', 'public');
            $data['profile_photo'] = $path;
        } elseif (!empty($existingData['profile_photo'])) {
            $data['profile_photo'] = $existingData['profile_photo'];
        }

        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $path = $file->store('employee/signature', 'public');
            $data['signature_file'] = $path;
        } elseif (!empty($existingData['signature_file'])) {
            $data['signature_file'] = $existingData['signature_file'];
        }

        $data['full_name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $this->employeeService->saveWizardStep($data, 'step2');

        return redirect()->route('employee.create.step3');
    }


    public function createStepThree()
    {
        $data = session('employee_creation.step3', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step3', [
            'data' => $data,
        ]);
    }

    public function storeStepThree(StoreEmployeeStepThreeRequest $request)
    {
        if ($request->boolean('skip')) {
            $this->employeeService->saveWizardStep([
                'addresses' => []
            ], 'step3');

            return redirect()->route('employee.create.step4');
        }

        $validated = $request->validated();

        $addresses = [];

        if (!empty($validated['present_address']) && is_array($validated['present_address'])) {

            $present = $validated['present_address'];
            $present['address_type'] = 'present';

            $addresses[] = $present;
        }

        if (!empty($validated['permanent_address']) && is_array($validated['permanent_address'])) {

            $permanent = $validated['permanent_address'];
            $permanent['address_type'] = 'permanent';

            $addresses[] = $permanent;
        }

        $this->employeeService->saveWizardStep([
            'addresses' => $addresses
        ], 'step3');

        return redirect()->route('employee.create.step4');
    }

    public function createStepFour()
    {
        $data = session('employee_creation.step4', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step4', [
            'data' => $data,
            'wizard' => $this->employeeService->getWizardSummary(),
        ]);
    }

    public function storeStepFour(StoreEmployeeStepFourRequest $request)
    {
        $this->employeeService->saveWizardStep($request->boolean('skip') ? [] : $request->validated(), 'step4');

        return redirect()->route('employee.create.step5');
    }

    public function createStepFive()
    {
        $existingData = session('employee_creation.step5', []);

        // On validation failure, merge old() input to preserve submitted data
        // including dynamically-added document rows
        if (session()->has('errors')) {
            $oldDocuments = old('documents', []);

            if (!empty($oldDocuments)) {
                // Merge old() documents over existing ones to preserve
                // any new rows added via JavaScript before they were saved
                $existingData['documents'] = $oldDocuments;
            } else {
                $oldData = old();
                if (!empty($oldData)) {
                    $existingData = array_merge($existingData, $oldData);
                }
            }
        }

        return view('employee::create-step5', [
            'data' => $existingData,
        ]);
    }

    public function storeStepFive(StoreEmployeeStepFiveRequest $request)
    {
        if ($request->boolean('skip')) {
            $this->employeeService->saveWizardStep([
                'documents' => []
            ], 'step5');

            return redirect()->route('employee.create.step6');
        }

        $validated = $request->validated();
        $existingData = session('employee_creation.step5', []);
        $existingDocuments = $existingData['documents'] ?? [];

        $documents = [];

        foreach ($validated['documents'] ?? [] as $index => $document) {

            $filePath = null;
            $fileSize = null;
            $mimeType = null;

            if (
                isset($document['document_file']) &&
                $document['document_file'] instanceof \Illuminate\Http\UploadedFile
            ) {
                $file = $document['document_file'];

                $filePath = $file->store(
                    'employee/documents',
                    'public'
                );

                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
            } elseif (isset($existingDocuments[$index]['file_path'])) {
                // Preserve existing file path when going back to this step
                $filePath = $existingDocuments[$index]['file_path'];
                $fileSize = $existingDocuments[$index]['file_size'] ?? null;
                $mimeType = $existingDocuments[$index]['mime_type'] ?? null;
            }

            $documents[] = [
                'category'           => $document['category'],
                'document_name'      => $document['document_name'] ?? ($document['category'] ?? ''),
                'document_number'    => $document['document_number'] ?? null,
                'issuing_authority'  => $document['issuing_authority'] ?? null,
                'issue_date'         => $document['issue_date'] ?? null,
                'expiry_date'        => $document['expiry_date'] ?? null,
                'notes'              => $document['notes'] ?? null,

                'file_path'          => $filePath,
                'file_size'          => $fileSize,
                'mime_type'          => $mimeType,
            ];
        }

        $this->employeeService->saveWizardStep([
            'documents' => $documents
        ], 'step5');

        return redirect()->route('employee.create.step6');
    }

    public function createStepSix()
    {
        $data = session('employee_creation.step6', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step6', [
            'data' => $data,
        ]);
    }

    public function storeStepSix(StoreEmployeeStepSixRequest $request)
    {
        if ($request->boolean('skip')) {
            $this->employeeService->saveWizardStep([
                'educations' => []
            ], 'step6');

            return redirect()->route('employee.create.step7');
        }

        $validated = $request->validated();

        $educations = [];

        foreach ($validated['educations'] ?? [] as $index => $education) {
            $certificatePath = null;

            if (
                isset($education['certificate_file']) &&
                $education['certificate_file'] instanceof \Illuminate\Http\UploadedFile
            ) {
                $certificatePath = $education['certificate_file']->store(
                    'employee/education-certificates',
                    'public'
                );
            }

            $educations[] = [
                'degree'           => $education['degree'],
                'major_subject'    => $education['major_subject'] ?? null,
                'institution'      => $education['institution'] ?? null,
                'board_university' => $education['board_university'] ?? null,
                'passing_year'     => $education['passing_year'] ?? null,
                'result_type'      => $education['result_type'] ?? null,
                'result_value'     => $education['result_value'] ?? null,
                'duration_from'    => $education['duration_from'] ?? null,
                'duration_to'      => $education['duration_to'] ?? null,
                'country'          => $education['country'] ?? null,
                'certificate_path' => $certificatePath,
                'is_highest'       => !empty($education['is_highest']) ? 1 : 0,
            ];
        }

        $this->employeeService->saveWizardStep([
            'educations' => $educations
        ], 'step6');

        return redirect()->route('employee.create.step7');
    }

    public function createStepSeven()
    {
        $data = session('employee_creation.step7', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step7', [
            'data' => $data,
        ]);
    }

    public function storeStepSeven(StoreEmployeeStepSevenRequest $request)
    {
        if ($request->boolean('skip')) {
            $this->employeeService->saveWizardStep([
                'experiences' => []
            ], 'step7');

            return redirect()->route('employee.create.step8');
        }

        $validated = $request->validated();

        $experiences = [];

        foreach ($validated['experiences'] ?? [] as $index => $experience) {
            $certificatePath = null;

            if (
                isset($experience['certificate_file']) &&
                $experience['certificate_file'] instanceof \Illuminate\Http\UploadedFile
            ) {
                $certificatePath = $experience['certificate_file']->store(
                    'employee/experience-certificates',
                    'public'
                );
            }

            $experiences[] = [
                'company_name'       => $experience['company_name'],
                'designation'        => $experience['designation'] ?? null,
                'department'         => $experience['department'] ?? null,
                'from_date'          => $experience['from_date'] ?? null,
                'to_date'            => $experience['to_date'] ?? null,
                'is_current'         => !empty($experience['is_current']) ? 1 : 0,
                'responsibilities'   => $experience['responsibilities'] ?? null,
                'achievements'       => $experience['achievements'] ?? null,
                'reason_for_leaving' => $experience['reason_for_leaving'] ?? null,
                'salary_scale'       => $experience['salary_scale'] ?? null,
                'reference_name'     => $experience['reference_name'] ?? null,
                'reference_phone'    => $experience['reference_phone'] ?? null,
                'reference_email'    => $experience['reference_email'] ?? null,
                'certificate_path'   => $certificatePath,
            ];
        }

        $this->employeeService->saveWizardStep([
            'experiences' => $experiences
        ], 'step7');

        return redirect()->route('employee.create.step8');
    }

    public function createStepEight()
    {
        $companies = Company::all();

        $data = session('employee_creation.step8', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step8', [
            'data' => $data,
            'branches' => $companies->flatMap(fn($company) => $company->branches)->pluck('name', 'id'),
            'departments' => Department::all()->pluck('name', 'id'),
            'designations' => Designation::all()->pluck('title', 'id'),
            'grades' => SalaryGrade::all()->pluck('name', 'id'),
            'employees' => Employee::active()->get(),
        ]);
    }

    public function storeStepEight(StoreEmployeeStepEightRequest $request)
    {
        $this->employeeService->saveWizardStep($request->boolean('skip') ? [] : $request->validated(), 'step8');

        return redirect()->route('employee.create.step9');
    }

    public function createStepNine()
    {
        $data = session('employee_creation.step9', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step9', [
            'data' => $data,
        ]);
    }

    public function storeStepNine(StoreEmployeeStepNineRequest $request)
    {
        $this->employeeService->saveWizardStep($request->boolean('skip') ? [] : $request->validated(), 'step9');

        return redirect()->route('employee.create.step10');
    }

    public function createStepTen()
    {
        $data = session('employee_creation.step10', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step10', [
            'data' => $data,
            'categories' => SkillCategory::all(),
        ]);
    }

    public function storeStepTen(StoreEmployeeStepTenRequest $request)
    {
        $this->employeeService->saveWizardStep($request->boolean('skip') ? [] : $request->validated(), 'step10');

        return redirect()->route('employee.create.step11');
    }

    public function createStepEleven()
    {
        $data = session('employee_creation.step11', []);

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
        }

        return view('employee::create-step11', [
            'data' => $data,
            'wizard' => $this->employeeService->getWizardSummary(),
        ]);
    }

    public function finalize(StoreEmployeeStepElevenRequest $request)
    {
        $this->employeeService->saveWizardStep($request->boolean('skip') ? [] : $request->validated(), 'step11');

        $wizardData = $this->employeeService->getWizardSummary();
        $result = $this->employeeService->finalizeEmployee($wizardData);

        if ($result['status'] === 'success') {
            return redirect()->route('employee.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function show($id)
    {
        return view('employee::show');
    }

    public function edit($id)
    {
        return redirect()->route('employee::edit', $id);
    }

    // public function update(Request $request, $id)
    // {
    //     $section = $request->query('section', 'basic');

    //     switch ($section) {
    //         case 'personal':
    //             return app(\App\Http\Controllers\Dashboard\EmployeeEditController::class)->updatePersonal($request, $id);
    //         case 'documents':
    //             return app(\App\Http\Controllers\Dashboard\EmployeeEditController::class)->updateDocuments($request, $id);
    //         default:
    //             return app(\App\Http\Controllers\Dashboard\EmployeeEditController::class)->updateBasic($request, $id);
    //     }
    // }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        $employee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Employee deleted successfully.',
        ]);
    }

    public function resetStep(int $step)
    {
        session()->forget("employee_creation.step{$step}");

        return redirect()->route("employee.create.step{$step}")
            ->with('info', "Step {$step} data has been reset.");
    }

    public function cancel()
    {
        $this->employeeService->clearWizardData();

        return redirect()->route('employee.index')->with('info', 'Employee creation cancelled.');
    }
}
