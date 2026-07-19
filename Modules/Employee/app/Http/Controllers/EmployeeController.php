<?php

namespace Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
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
        $employee = Employee::active()->with('personalInfo')->get();
        dd($employee);
        $data = session('employee_creation.step1', []);

        // Use existing employee_code from session if available, otherwise generate new
        if (!empty($data['employee_code'])) {
            $employeeCode = $data['employee_code'];
        } else {
            $employeeCode = Employee::max('id') + 1;
            $employeeCode = 'EMP-' . str_pad($employeeCode, 4, '0', STR_PAD_LEFT);
        }

        // On validation failure, merge old() input to preserve submitted data
        if (session()->has('errors')) {
            $oldData = old();
            if (!empty($oldData)) {
                $data = array_merge($data, $oldData);
            }
            // Re-check employee_code from old input after merge
            if (!empty(old('employee_code'))) {
                $employeeCode = old('employee_code');
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

            $employeeCode = $validated['employee_code'] ?? 'EMP';
            $ext = $request->file('profile_photo')->getClientOriginalExtension();
            $filename = $employeeCode . ' - profile.' . $ext;
            $validated['profile_photo'] = $request->file('profile_photo')->storeAs('employee/profile-photos', $filename, 'public');
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
        $step1Code = session('employee_creation.step1.employee_code', '');

        // If employee code has changed, clear stale file references from step2 data
        if (!empty($data['profile_photo']) && !empty($step1Code)) {
            if (!str_contains($data['profile_photo'], $step1Code)) {
                $data['profile_photo'] = null;
            }
        }
        if (!empty($data['signature_file']) && !empty($step1Code)) {
            if (!str_contains($data['signature_file'], $step1Code)) {
                $data['signature_file'] = null;
            }
        }

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
            $employeeCode = session('employee_creation.step1.employee_code', 'EMP');
            $ext = $file->getClientOriginalExtension();
            $filename = $employeeCode . ' - profile.' . $ext;
            $data['profile_photo'] = $file->storeAs('employee/profile-photos', $filename, 'public');
        } elseif (!empty($existingData['profile_photo'])) {
            $data['profile_photo'] = $existingData['profile_photo'];
        }

        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $employeeCode = session('employee_creation.step1.employee_code', 'EMP');
            $ext = $file->getClientOriginalExtension();
            $filename = $employeeCode . ' - signature.' . $ext;
            $data['signature_file'] = $file->storeAs('employee/signature', $filename, 'public');
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

        // On validation failure, merge old() input and uploaded files
        if (session()->has('errors')) {
            $oldDocuments = old('documents', []);

            if (!empty($oldDocuments)) {
                $existingData['documents'] = $oldDocuments;
            } else {
                $oldData = old();
                if (!empty($oldData)) {
                    $existingData = array_merge($existingData, $oldData);
                }
            }

            // Merge uploaded files into documents from session storage
            $uploadedFiles = session('employee_creation.step5_uploaded_files', []);
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $index => $fileInfo) {
                    if (!isset($existingData['documents'][$index])) {
                        $existingData['documents'][$index] = [];
                    }
                    $existingData['documents'][$index]['file_path'] = $fileInfo['file_path'];
                    $existingData['documents'][$index]['file_size'] = $fileInfo['file_size'] ?? null;
                    $existingData['documents'][$index]['file_uploaded'] = true;
                }
            }
        }

        // Also pass any previously uploaded files from session
        $uploadedFiles = session('employee_creation.step5_uploaded_files', []);
        if (!empty($uploadedFiles) && !session()->has('errors')) {
            // Merge uploaded files into documents for fresh display
            foreach ($uploadedFiles as $index => $fileInfo) {
                if (!isset($existingData['documents'][$index])) {
                    $existingData['documents'][$index] = [];
                }
                $existingData['documents'][$index]['file_path'] = $fileInfo['file_path'];
                $existingData['documents'][$index]['file_size'] = $fileInfo['file_size'] ?? null;
                $existingData['documents'][$index]['file_uploaded'] = true;
            }
        }

        return view('employee::create-step5', [
            'data' => $existingData,
        ]);
    }

    public function storeStepFive(Request $request)
    {
        if ($request->boolean('skip')) {
            $this->employeeService->saveWizardStep([
                'documents' => []
            ], 'step5');

            return redirect()->route('employee.create.step6');
        }

        // Get all input data including existing_file references
        $allInput = $request->all();
        $documentsInput = $allInput['documents'] ?? [];
        $uploadedFiles = [];
        $uploadErrors = [];

        // Process file uploads and existing file references
        foreach ($documentsInput as $index => $docData) {
            // Check if a new file was uploaded for this document
            if ($request->hasFile("documents.{$index}.document_file")) {
                $uploadedFile = $request->file("documents.{$index}.document_file");

                // Validate file type
                $allowedMimes = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
                $extension = strtolower($uploadedFile->getClientOriginalExtension());

                if (!in_array($extension, $allowedMimes) && !in_array($uploadedFile->getMimeType(), ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])) {
                    $uploadErrors["documents.{$index}.document_file"] = 'Allowed file types: PDF, JPG, JPEG, PNG, WEBP.';
                    continue;
                }

                // Validate file size (5MB max)
                if ($uploadedFile->getSize() > 5 * 1024 * 1024) {
                    $uploadErrors["documents.{$index}.document_file"] = 'Document size cannot exceed 5 MB.';
                    continue;
                }

                // Generate custom filename: employee_code - category.ext
                $category = $docData['category'] ?? 'document';
                $extension = $uploadedFile->getClientOriginalExtension();
                $employeeCode = session('employee_creation.step1.employee_code', 'EMP');
                $customFilename = $employeeCode . ' - ' . $category . '.' . $extension;

                // Store with custom filename
                $filePath = $uploadedFile->storeAs('employee/documents', $customFilename, 'public');
                $uploadedFiles[$index] = [
                    'file_path' => $filePath,
                    'file_size' => $uploadedFile->getSize(),
                    'mime_type' => $uploadedFile->getMimeType(),
                ];
            }
            // Check if there's an existing file reference (from previous failed attempt)
            elseif (!empty($docData['existing_file'])) {
                $uploadedFiles[$index] = [
                    'file_path' => $docData['existing_file'],
                    'file_size' => $docData['file_size'] ?? null,
                    'mime_type' => $docData['mime_type'] ?? null,
                ];
            }
            // Check if there's a file_path from session data
            elseif (!empty($docData['file_path'])) {
                $uploadedFiles[$index] = [
                    'file_path' => $docData['file_path'],
                    'file_size' => $docData['file_size'] ?? null,
                    'mime_type' => $docData['mime_type'] ?? null,
                ];
            }
        }

        // If there are upload errors, redirect back
        if (!empty($uploadErrors)) {
            if (!empty($uploadedFiles)) {
                session()->put('employee_creation.step5_uploaded_files', $uploadedFiles);
            }
            return redirect()->back()
                ->withErrors($uploadErrors)
                ->withInput();
        }

        // Validate the rest of the form data
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'documents' => ['required', 'array', 'min:1'],
            'documents.*.category' => ['required', 'string', 'max:500'],
            'documents.*.document_name' => ['nullable', 'string', 'max:300'],
            'documents.*.document_number' => ['nullable', 'string', 'max:100'],
            'documents.*.issuing_authority' => ['nullable', 'string', 'max:300'],
            'documents.*.issue_date' => ['nullable', 'date'],
            'documents.*.expiry_date' => ['nullable', 'date'],
            'documents.*.notes' => ['nullable', 'string'],
        ], [
            'documents.required' => 'Please add at least one document.',
            'documents.*.category.required' => 'Document category is required.',
        ]);

        if ($validator->fails()) {
            if (!empty($uploadedFiles)) {
                session()->put('employee_creation.step5_uploaded_files', $uploadedFiles);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $existingData = session('employee_creation.step5', []);
        $documents = [];

        foreach ($validated['documents'] ?? [] as $index => $document) {
            $fileInfo = $uploadedFiles[$index] ?? null;
            $filePath = $fileInfo['file_path'] ?? null;
            $fileSize = $fileInfo['file_size'] ?? null;
            $mimeType = $fileInfo['mime_type'] ?? null;

            // Fallback to existing session data
            if (!$filePath && isset($existingData['documents'][$index]['file_path'])) {
                $filePath = $existingData['documents'][$index]['file_path'];
                $fileSize = $existingData['documents'][$index]['file_size'] ?? null;
                $mimeType = $existingData['documents'][$index]['mime_type'] ?? null;
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

        session()->forget('employee_creation.step5_uploaded_files');

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

    public function profile($id)
    {
        $employee = Employee::with([
            'personalInfo',
            'addresses',
            'banking',
            'documents',
            'educations',
            'experiences',
            'skills',
            'languages',
            'dependents',
            'department',
            'designation',
            'branch',
            'shift',
            'salaryGrade',
            'manager.personalInfo',
        ])->findOrFail($id);

        return view('employee::profile', compact('employee'));
    }

    public function show($id)
    {
        $employee = Employee::with([
            'personalInfo',
            'addresses',
            'banking',
            'documents',
            'educations',
            'experiences',
            'skills',
            'languages',
            'dependents',
            'department',
            'designation',
        ])->findOrFail($id);

        return view('employee::profile', compact('employee'));
    }

    public function edit($id)
    {
        return redirect()->route('employee::edit', $id);
    }

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
