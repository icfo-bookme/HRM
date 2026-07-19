<?php

namespace Modules\Employee\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeeAddress;
use Modules\Employee\Models\EmployeeBanking;
use Modules\Employee\Models\EmployeeDependent;
use Modules\Employee\Models\EmployeeDocument;
use Modules\Employee\Models\EmployeeEducation;
use Modules\Employee\Models\EmployeeExperience;
use Modules\Employee\Models\EmployeeJobHistory;
use Modules\Employee\Models\EmployeeLanguage;
use Modules\Employee\Models\EmployeePersonalInfo;
use Modules\Employee\Models\EmployeeSkill;

class EmployeeEditService
{
    /**
     * Update employee basic/core info (step1 fields).
     */
    public function updateBasic(Employee $employee, array $data): void
    {
        $employee->update($data);
    }

    /**
     * Update personal info (step2 fields).
     */
    public function updatePersonal(Employee $employee, array $data, ?UploadedFile $profilePhoto = null, ?UploadedFile $signatureFile = null): void
    {
        if ($profilePhoto) {
            if ($employee->personalInfo?->profile_photo) {
                Storage::disk('public')->delete($employee->personalInfo->profile_photo);
            }
            $data['profile_photo'] = $profilePhoto->store('employee/profile-photos', 'public');
        }

        if ($signatureFile) {
            if ($employee->personalInfo?->signature_file) {
                Storage::disk('public')->delete($employee->personalInfo->signature_file);
            }
            $data['signature_file'] = $signatureFile->store('employee/signature', 'public');
        }

        EmployeePersonalInfo::updateOrCreate(
            ['employee_id' => $employee->id],
            $data
        );
    }

    /**
     * Update addresses (step3 fields).
     */
    public function updateAddresses(Employee $employee, array $addresses): void
    {
        $existingIds = collect($addresses)->filter(fn($a) => !empty($a['id']))->pluck('id');
        $employee->addresses()->whereNotIn('id', $existingIds)->delete();

        foreach ($addresses as $address) {
            $addrData = [
                'address_type' => $address['address_type'] ?? 'present',
                'house_no'     => $address['house_no'] ?? null,
                'road_no'      => $address['road_no'] ?? null,
                'road_name'    => $address['road_name'] ?? null,
                'village'      => $address['village'] ?? null,
                'area'         => $address['area'] ?? null,
                'post_office'  => $address['post_office'] ?? null,
                'postal_code'  => $address['postal_code'] ?? null,
                'city'         => $address['city'] ?? null,
                'upazila'      => $address['upazila'] ?? null,
                'district'     => $address['district'] ?? null,
                'division'     => $address['division'] ?? null,
                'state'        => $address['state'] ?? null,
                'country'      => $address['country'] ?? null,
                'latitude'     => $address['latitude'] ?? null,
                'longitude'    => $address['longitude'] ?? null,
            ];

            if (!empty($address['id'])) {
                EmployeeAddress::where('id', $address['id'])->update($addrData);
            } else {
                $employee->addresses()->create($addrData);
            }
        }
    }

    /**
     * Update banking info (step4).
     */
    public function updateBanking(Employee $employee, array $data): void
    {
        $banking = $employee->banking()->first();
        if ($banking) {
            $banking->update($data);
        } elseif (!empty($data['bank_name']) || !empty($data['mfs_type'])) {
            $employee->banking()->create($data);
        }
    }

    /**
     * Update documents (step5).
     */
    public function updateDocuments(Employee $employee, array $documents, ?array $uploadedFiles = []): void
    {
        $existingIds = collect($documents)->filter(fn($d) => !empty($d['id']))->pluck('id');
        $employee->documents()->whereNotIn('id', $existingIds)->delete();

        foreach ($documents as $index => $docData) {
            $filePath = $docData['file_path'] ?? null;
            $fileSize = $docData['file_size'] ?? null;
            $mimeType = $docData['mime_type'] ?? null;

            if (isset($uploadedFiles[$index]) && $uploadedFiles[$index] instanceof UploadedFile) {
                $file = $uploadedFiles[$index];
                $filePath = $file->store('employee/documents', 'public');
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
            }

            $docRecord = [
                'category'          => $docData['category'],
                'document_name'     => $docData['document_name'] ?? $docData['category'],
                'document_number'   => $docData['document_number'] ?? null,
                'issuing_authority' => $docData['issuing_authority'] ?? null,
                'issue_date'        => $docData['issue_date'] ?? null,
                'expiry_date'       => $docData['expiry_date'] ?? null,
                'notes'             => $docData['notes'] ?? null,
                'file_path'         => $filePath,
                'file_size'         => $fileSize,
                'mime_type'         => $mimeType,
            ];

            if (!empty($docData['id'])) {
                EmployeeDocument::where('id', $docData['id'])->update($docRecord);
            } else {
                $employee->documents()->create($docRecord);
            }
        }
    }

    /**
     * Update education records (step6).
     */
    public function updateEducation(Employee $employee, array $educations, ?array $uploadedFiles = []): void
    {
        $existingIds = collect($educations)->filter(fn($e) => !empty($e['id']))->pluck('id');
        $employee->educations()->whereNotIn('id', $existingIds)->delete();

        foreach ($educations as $index => $edu) {
            $certificatePath = $edu['certificate_path'] ?? null;

            if (isset($uploadedFiles[$index]) && $uploadedFiles[$index] instanceof UploadedFile) {
                $certificatePath = $uploadedFiles[$index]->store('employee/education-certificates', 'public');
            }

            $eduData = [
                'degree'           => $edu['degree'],
                'major_subject'    => $edu['major_subject'] ?? null,
                'institution'      => $edu['institution'] ?? null,
                'board_university' => $edu['board_university'] ?? null,
                'passing_year'     => $edu['passing_year'] ?? null,
                'result_type'      => $edu['result_type'] ?? null,
                'result_value'     => $edu['result_value'] ?? null,
                'duration_from'    => $edu['duration_from'] ?? null,
                'duration_to'      => $edu['duration_to'] ?? null,
                'country'          => $edu['country'] ?? null,
                'certificate_path' => $certificatePath,
                'is_highest'       => !empty($edu['is_highest']) ? 1 : 0,
            ];

            if (!empty($edu['id'])) {
                EmployeeEducation::where('id', $edu['id'])->update($eduData);
            } else {
                $employee->educations()->create($eduData);
            }
        }
    }

    /**
     * Update experience records (step7).
     */
    public function updateExperience(Employee $employee, array $experiences, ?array $uploadedFiles = []): void
    {
        $existingIds = collect($experiences)->filter(fn($e) => !empty($e['id']))->pluck('id');
        $employee->experiences()->whereNotIn('id', $existingIds)->delete();

        foreach ($experiences as $index => $exp) {
            $certificatePath = $exp['certificate_path'] ?? null;

            if (isset($uploadedFiles[$index]) && $uploadedFiles[$index] instanceof UploadedFile) {
                $certificatePath = $uploadedFiles[$index]->store('employee/experience-certificates', 'public');
            }

            $expData = [
                'company_name'       => $exp['company_name'],
                'designation'        => $exp['designation'] ?? null,
                'department'         => $exp['department'] ?? null,
                'from_date'          => $exp['from_date'] ?? null,
                'to_date'            => $exp['to_date'] ?? null,
                'is_current'         => !empty($exp['is_current']) ? 1 : 0,
                'responsibilities'   => $exp['responsibilities'] ?? null,
                'achievements'       => $exp['achievements'] ?? null,
                'reason_for_leaving' => $exp['reason_for_leaving'] ?? null,
                'salary_scale'       => $exp['salary_scale'] ?? null,
                'reference_name'     => $exp['reference_name'] ?? null,
                'reference_phone'    => $exp['reference_phone'] ?? null,
                'reference_email'    => $exp['reference_email'] ?? null,
                'certificate_path'   => $certificatePath,
            ];

            if (!empty($exp['id'])) {
                EmployeeExperience::where('id', $exp['id'])->update($expData);
            } else {
                $employee->experiences()->create($expData);
            }
        }
    }

    /**
     * Update job history (step8).
     */
    public function updateJobHistory(Employee $employee, array $data): void
    {
        if (!empty($data['id'])) {
            $history = EmployeeJobHistory::where('id', $data['id'])->where('employee_id', $employee->id)->first();
            if ($history) {
                $history->update($data);
            }
        } elseif (!empty($data['effective_date']) && !empty($data['change_type'])) {
            $employee->jobHistory()->create($data);
        }
    }

    /**
     * Update languages (step9).
     */
    public function updateLanguages(Employee $employee, array $languages): void
    {
        $existingIds = collect($languages)->filter(fn($l) => !empty($l['id']))->pluck('id');
        $employee->languages()->whereNotIn('id', $existingIds)->delete();

        foreach ($languages as $lang) {
            $langData = [
                'language_name' => $lang['language_name'],
                'proficiency'   => $lang['proficiency'] ?? null,
                'can_read'      => !empty($lang['can_read']) ? 1 : 0,
                'can_write'     => !empty($lang['can_write']) ? 1 : 0,
                'can_speak'     => !empty($lang['can_speak']) ? 1 : 0,
            ];

            if (!empty($lang['id'])) {
                EmployeeLanguage::where('id', $lang['id'])->update($langData);
            } else {
                $employee->languages()->create($langData);
            }
        }
    }

    /**
     * Update skills (step10).
     */
    public function updateSkills(Employee $employee, array $skills): void
    {
        $existingIds = collect($skills)->filter(fn($s) => !empty($s['id']))->pluck('id');
        $employee->skills()->whereNotIn('id', $existingIds)->delete();

        foreach ($skills as $skill) {
            $skillData = [
                'category_id'        => $skill['category_id'] ?? null,
                'skill_name'         => $skill['skill_name'],
                'description'        => $skill['description'] ?? null,
                'proficiency'        => $skill['proficiency'] ?? null,
                'years_of_experience'=> $skill['years_of_experience'] ?? null,
                'last_used_date'     => $skill['last_used_date'] ?? null,
                'certification'      => $skill['certification'] ?? null,
                'is_active'          => !empty($skill['is_active']) ? 1 : 0,
            ];

            if (!empty($skill['id'])) {
                EmployeeSkill::where('id', $skill['id'])->update($skillData);
            } else {
                $employee->skills()->create($skillData);
            }
        }
    }

    /**
     * Update dependents (step11).
     */
    public function updateDependents(Employee $employee, array $dependents): void
    {
        $existingIds = collect($dependents)->filter(fn($d) => !empty($d['id']))->pluck('id');
        $employee->dependents()->whereNotIn('id', $existingIds)->delete();

        foreach ($dependents as $dep) {
            $depData = [
                'full_name'       => $dep['full_name'],
                'relation'        => $dep['relation'],
                'date_of_birth'   => $dep['date_of_birth'] ?? null,
                'nid_number'      => $dep['nid_number'] ?? null,
                'phone'           => $dep['phone'] ?? null,
                'email'           => $dep['email'] ?? null,
                'occupation'      => $dep['occupation'] ?? null,
                'is_nominee'      => !empty($dep['is_nominee']) ? 1 : 0,
                'nominee_percent' => $dep['nominee_percent'] ?? null,
                'priority_order'  => $dep['priority_order'] ?? null,
            ];

            if (!empty($dep['id'])) {
                EmployeeDependent::where('id', $dep['id'])->update($depData);
            } else {
                $employee->dependents()->create($depData);
            }
        }
    }
}