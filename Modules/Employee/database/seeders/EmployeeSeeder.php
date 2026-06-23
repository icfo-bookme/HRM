<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeePersonalInfo;
use Modules\Employee\Models\EmployeeAddress;
use Modules\Employee\Models\EmployeeEducation;
use Modules\Employee\Models\EmployeeExperience;
use Modules\Employee\Models\EmployeeSkill;
use Modules\Employee\Models\EmployeeLanguage;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have a branch, or use branch_id = 1
        $branchId = 1;

        // Ensure we have departments, or use department_id = 1
        $departmentId = 1;

        // Ensure we have designations, or use designation_id = 1
        $designationId = 1;

        // Ensure we have a salary grade, or use grade_id = 1
        $gradeId = 1;

        // Ensure we have a shift, or use shift_id = 1
        $shiftId = 1;

        $faker = \Faker\Factory::create('en_BD');
        $faker->addProvider(new \Faker\Provider\en_US\Person($faker));
        $faker->addProvider(new \Faker\Provider\en_US\PhoneNumber($faker));
        $faker->addProvider(new \Faker\Provider\en_US\Address($faker));

        $genders = ['Male', 'Female', 'Other'];
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $maritalStatuses = ['Single', 'Married', 'Divorced', 'Widowed', 'Separated'];
        $employmentTypes = ['Full-Time', 'Part-Time', 'Contractual', 'Intern', 'Probation', 'Freelance'];
        $statuses = ['Active', 'Inactive', 'On Leave', 'Suspended', 'Terminated', 'Resigned', 'Retired'];
        $religions = ['Islam', 'Hinduism', 'Buddhism', 'Christianity', 'Other'];
        $nationalities = ['Bangladeshi', 'Indian', 'Pakistani', 'Nepali', 'Sri Lankan', 'Other'];

        $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Sales', 'Operations', 'Admin', 'R&D', 'Legal', 'Procurement'];
        $designations = [
            'Chief Executive Officer', 'Chief Technology Officer', 'Chief Financial Officer',
            'Senior Software Engineer', 'Software Engineer', 'Junior Software Engineer',
            'HR Manager', 'HR Executive', 'Accountant', 'Finance Manager',
            'Marketing Manager', 'Marketing Executive', 'Sales Manager', 'Sales Executive',
            'Operations Manager', 'Operations Executive', 'Admin Manager', 'Admin Executive',
            'UI/UX Designer', 'Quality Assurance Engineer', 'DevOps Engineer',
            'Data Analyst', 'Business Analyst', 'Project Manager', 'Product Manager',
            'Customer Support Lead', 'Customer Support Executive', 'Intern'
        ];

        $employeeData = [];

        // Define 30 employees with diverse data
        $employeeData[] = [
            'first_name' => 'Md. Arif',
            'last_name' => 'Hossain',
            'phone' => '01712345678',
            'email' => 'arif.hossain@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1985-03-15',
            'nationality' => 'Bangladeshi',
            'department' => 'IT',
            'designation' => 'Chief Technology Officer',
            'employment_type' => 'Full-Time',
            'joining_date' => '2020-01-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'arif.hossain@gmail.com',
                    'blood_group' => 'A+',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Fatima Hossain',
                    'father_name' => 'Md. Kamal Hossain',
                    'mother_name' => 'Shahida Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01722345678',
                ],
            'present_address' => ['house_no' => '12', 'road_no' => '5', 'area' => 'Gulshan', 'city' => 'Dhaka', 'postal_code' => '1212'],
            'permanent_address' => ['house_no' => '45', 'road_no' => '2', 'area' => 'Bogra Sadar', 'city' => 'Bogra', 'postal_code' => '5800'],
            'educations' => [
                ['degree' => 'Bachelor of Science in Computer Science', 'major_subject' => 'Computer Science', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2008, 'result_type' => 'CGPA', 'result_value' => '3.75', 'is_highest' => 1],
                ['degree' => 'Master of Science in Software Engineering', 'major_subject' => 'Software Engineering', 'institution' => 'Bangladesh University of Engineering and Technology', 'board_university' => 'BUET', 'passing_year' => 2012, 'result_type' => 'CGPA', 'result_value' => '3.85', 'is_highest' => 0],
            ],
            'experiences' => [
                ['company_name' => 'Tech Solutions Ltd.', 'designation' => 'Senior Software Engineer', 'department' => 'IT', 'from_date' => '2012-06-01', 'to_date' => '2016-12-31', 'is_current' => 0, 'responsibilities' => 'Led a team of 5 developers building enterprise web applications.', 'reason_for_leaving' => 'Better opportunity'],
                ['company_name' => 'Digital Innovators Inc.', 'designation' => 'Engineering Manager', 'department' => 'IT', 'from_date' => '2017-01-01', 'to_date' => '2019-12-31', 'is_current' => 0, 'responsibilities' => 'Managed software development lifecycle and mentored junior developers.', 'reason_for_leaving' => 'Career advancement'],
            ],
            'skills' => [
                ['skill_name' => 'PHP', 'category_id' => 1, 'proficiency' => 'Expert', 'years_of_experience' => 12],
                ['skill_name' => 'Laravel', 'category_id' => 2, 'proficiency' => 'Expert', 'years_of_experience' => 8],
                ['skill_name' => 'JavaScript', 'category_id' => 1, 'proficiency' => 'Advanced', 'years_of_experience' => 10],
                ['skill_name' => 'MySQL', 'category_id' => 3, 'proficiency' => 'Expert', 'years_of_experience' => 12],
                ['skill_name' => 'Docker', 'category_id' => 4, 'proficiency' => 'Advanced', 'years_of_experience' => 5],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'Hindi', 'proficiency' => 'Conversational', 'can_read' => 0, 'can_write' => 0, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Sadia',
            'last_name' => 'Rahman',
            'phone' => '01722345679',
            'email' => 'sadia.rahman@company.com',
            'gender' => 'Female',
            'date_of_birth' => '1990-07-22',
            'nationality' => 'Bangladeshi',
            'department' => 'HR',
            'designation' => 'HR Manager',
            'employment_type' => 'Full-Time',
            'joining_date' => '2021-03-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'sadia.rahman@yahoo.com',
                    'blood_group' => 'B+',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Tanvir Ahmed',
                    'father_name' => 'Abdur Rahman',
                    'mother_name' => 'Rashida Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01732345679',
                ],
            'present_address' => ['house_no' => '8', 'road_no' => '3', 'area' => 'Banani', 'city' => 'Dhaka', 'postal_code' => '1213'],
            'permanent_address' => ['house_no' => '120', 'road_no' => '1', 'area' => 'Chawkbazar', 'city' => 'Chittagong', 'postal_code' => '4203'],
            'educations' => [
                ['degree' => 'Bachelor of Business Administration', 'major_subject' => 'Human Resources', 'institution' => 'North South University', 'board_university' => 'North South University', 'passing_year' => 2013, 'result_type' => 'CGPA', 'result_value' => '3.60', 'is_highest' => 1],
                ['degree' => 'Master of Business Administration', 'major_subject' => 'HRM', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2016, 'result_type' => 'CGPA', 'result_value' => '3.55', 'is_highest' => 0],
            ],
            'experiences' => [
                ['company_name' => 'ABC Group', 'designation' => 'HR Executive', 'department' => 'HR', 'from_date' => '2016-07-01', 'to_date' => '2019-08-31', 'is_current' => 0, 'responsibilities' => 'Managed recruitment, onboarding, and employee relations.', 'reason_for_leaving' => 'Career growth'],
                ['company_name' => 'XYZ Corporation', 'designation' => 'Senior HR Executive', 'department' => 'HR', 'from_date' => '2019-09-01', 'to_date' => '2021-02-28', 'is_current' => 0, 'responsibilities' => 'Led performance management and training initiatives.', 'reason_for_leaving' => 'Better position'],
            ],
            'skills' => [
                ['skill_name' => 'Recruitment', 'category_id' => 10, 'proficiency' => 'Expert', 'years_of_experience' => 8],
                ['skill_name' => 'Payroll Management', 'category_id' => 10, 'proficiency' => 'Advanced', 'years_of_experience' => 6],
                ['skill_name' => 'Performance Management', 'category_id' => 10, 'proficiency' => 'Advanced', 'years_of_experience' => 5],
                ['skill_name' => 'Microsoft Excel', 'category_id' => 18, 'proficiency' => 'Expert', 'years_of_experience' => 10],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Md. Kamal',
            'last_name' => 'Uddin',
            'phone' => '01732345680',
            'email' => 'kamal.uddin@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1988-11-10',
            'nationality' => 'Bangladeshi',
            'department' => 'Finance',
            'designation' => 'Chief Financial Officer',
            'employment_type' => 'Full-Time',
            'joining_date' => '2020-06-15',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'kamal.uddin@hotmail.com',
                    'blood_group' => 'O+',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Nasrin Sultana',
                    'father_name' => 'Md. Abdul Jabbar',
                    'mother_name' => 'Jahanara Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01742345680',
                ],
            'present_address' => ['house_no' => '25', 'road_no' => '7', 'area' => 'Dhanmondi', 'city' => 'Dhaka', 'postal_code' => '1205'],
            'permanent_address' => ['house_no' => '55', 'road_no' => '3', 'area' => 'Kushtia Sadar', 'city' => 'Kushtia', 'postal_code' => '7000'],
            'educations' => [
                ['degree' => 'Bachelor of Commerce', 'major_subject' => 'Accounting', 'institution' => 'University of Chittagong', 'board_university' => 'University of Chittagong', 'passing_year' => 2010, 'result_type' => 'CGPA', 'result_value' => '3.70', 'is_highest' => 0],
                ['degree' => 'Master of Commerce', 'major_subject' => 'Accounting & Finance', 'institution' => 'University of Chittagong', 'board_university' => 'University of Chittagong', 'passing_year' => 2013, 'result_type' => 'CGPA', 'result_value' => '3.65', 'is_highest' => 0],
                ['degree' => 'Chartered Accountant', 'major_subject' => 'Accounting', 'institution' => 'ICAB', 'board_university' => 'ICAB', 'passing_year' => 2016, 'result_type' => 'Grade', 'result_value' => 'Pass', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'PwC Bangladesh', 'designation' => 'Senior Auditor', 'department' => 'Audit', 'from_date' => '2013-07-01', 'to_date' => '2017-06-30', 'is_current' => 0, 'responsibilities' => 'Conducted financial audits for multiple corporate clients.', 'reason_for_leaving' => 'Career change'],
                ['company_name' => 'Square Group', 'designation' => 'Finance Manager', 'department' => 'Finance', 'from_date' => '2017-07-01', 'to_date' => '2020-05-31', 'is_current' => 0, 'responsibilities' => 'Managed financial operations, budgeting, and reporting.', 'reason_for_leaving' => 'Better opportunity'],
            ],
            'skills' => [
                ['skill_name' => 'Financial Reporting', 'category_id' => 9, 'proficiency' => 'Expert', 'years_of_experience' => 10],
                ['skill_name' => 'Budgeting', 'category_id' => 9, 'proficiency' => 'Expert', 'years_of_experience' => 8],
                ['skill_name' => 'Taxation', 'category_id' => 9, 'proficiency' => 'Advanced', 'years_of_experience' => 7],
                ['skill_name' => 'QuickBooks', 'category_id' => 9, 'proficiency' => 'Advanced', 'years_of_experience' => 5],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Mehrin',
            'last_name' => 'Jahan',
            'phone' => '01742345681',
            'email' => 'mehrin.jahan@company.com',
            'gender' => 'Female',
            'date_of_birth' => '1992-05-18',
            'nationality' => 'Bangladeshi',
            'department' => 'Marketing',
            'designation' => 'Marketing Manager',
            'employment_type' => 'Full-Time',
            'joining_date' => '2021-08-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'mehrin.jahan@gmail.com',
                    'blood_group' => 'AB+',
                    'marital_status' => 'Single',
                    'father_name' => 'Md. Shahidul Islam',
                    'mother_name' => 'Shamima Akhter',
                    'religion' => 'Islam',
                    'personal_mobile' => '01752345681',
                ],
            'present_address' => ['house_no' => '42', 'road_no' => '12', 'area' => 'Uttara', 'city' => 'Dhaka', 'postal_code' => '1230'],
            'permanent_address' => ['house_no' => '78', 'road_no' => '5', 'area' => 'Kishoreganj Sadar', 'city' => 'Kishoreganj', 'postal_code' => '2300'],
            'educations' => [
                ['degree' => 'Bachelor of Business Administration', 'major_subject' => 'Marketing', 'institution' => 'Independent University, Bangladesh', 'board_university' => 'IUB', 'passing_year' => 2015, 'result_type' => 'CGPA', 'result_value' => '3.55', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Grameenphone Ltd.', 'designation' => 'Marketing Executive', 'department' => 'Marketing', 'from_date' => '2015-09-01', 'to_date' => '2018-12-31', 'is_current' => 0, 'responsibilities' => 'Executed digital marketing campaigns and social media strategy.', 'reason_for_leaving' => 'Career growth'],
                ['company_name' => 'Banglalink Digital', 'designation' => 'Senior Marketing Executive', 'department' => 'Marketing', 'from_date' => '2019-01-01', 'to_date' => '2021-07-31', 'is_current' => 0, 'responsibilities' => 'Led brand marketing initiatives and managed marketing budget.', 'reason_for_leaving' => 'Better position'],
            ],
            'skills' => [
                ['skill_name' => 'Digital Marketing', 'category_id' => 11, 'proficiency' => 'Expert', 'years_of_experience' => 8],
                ['skill_name' => 'SEO', 'category_id' => 11, 'proficiency' => 'Advanced', 'years_of_experience' => 6],
                ['skill_name' => 'Social Media Marketing', 'category_id' => 11, 'proficiency' => 'Expert', 'years_of_experience' => 8],
                ['skill_name' => 'Google Analytics', 'category_id' => 11, 'proficiency' => 'Advanced', 'years_of_experience' => 5],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'Spanish', 'proficiency' => 'Basic', 'can_read' => 1, 'can_write' => 0, 'can_speak' => 0],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Rafiq',
            'last_name' => 'Hasan',
            'phone' => '01752345682',
            'email' => 'rafiq.hasan@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1995-09-25',
            'nationality' => 'Bangladeshi',
            'department' => 'IT',
            'designation' => 'Senior Software Engineer',
            'employment_type' => 'Full-Time',
            'joining_date' => '2022-01-15',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'rafiq.hasan@outlook.com',
                    'blood_group' => 'B-',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Nusrat Jahan',
                    'father_name' => 'Md. Joynal Abedin',
                    'mother_name' => 'Hasna Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01762345682',
                ],
            'present_address' => ['house_no' => '15', 'road_no' => '8', 'area' => 'Mirpur', 'city' => 'Dhaka', 'postal_code' => '1216'],
            'permanent_address' => ['house_no' => '22', 'road_no' => '2', 'area' => 'Mymensingh Sadar', 'city' => 'Mymensingh', 'postal_code' => '2200'],
            'educations' => [
                ['degree' => 'Bachelor of Science in Software Engineering', 'major_subject' => 'Software Engineering', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2017, 'result_type' => 'CGPA', 'result_value' => '3.80', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'BJIT Ltd.', 'designation' => 'Software Engineer', 'department' => 'IT', 'from_date' => '2017-06-01', 'to_date' => '2020-05-31', 'is_current' => 0, 'responsibilities' => 'Developed web applications using Laravel and Vue.js.', 'reason_for_leaving' => 'Better opportunity'],
                ['company_name' => 'Brain Station 23', 'designation' => 'Senior Software Engineer', 'department' => 'IT', 'from_date' => '2020-06-01', 'to_date' => '2021-12-31', 'is_current' => 0, 'responsibilities' => 'Led development of microservices architecture.', 'reason_for_leaving' => 'Career advancement'],
            ],
            'skills' => [
                ['skill_name' => 'PHP', 'category_id' => 1, 'proficiency' => 'Expert', 'years_of_experience' => 7],
                ['skill_name' => 'Laravel', 'category_id' => 2, 'proficiency' => 'Expert', 'years_of_experience' => 6],
                ['skill_name' => 'Vue.js', 'category_id' => 2, 'proficiency' => 'Advanced', 'years_of_experience' => 4],
                ['skill_name' => 'React', 'category_id' => 2, 'proficiency' => 'Intermediate', 'years_of_experience' => 3],
                ['skill_name' => 'MySQL', 'category_id' => 3, 'proficiency' => 'Advanced', 'years_of_experience' => 7],
                ['skill_name' => 'Docker', 'category_id' => 4, 'proficiency' => 'Advanced', 'years_of_experience' => 4],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Nusrat',
            'last_name' => 'Tabassum',
            'phone' => '01762345683',
            'email' => 'nusrat.tabassum@company.com',
            'gender' => 'Female',
            'date_of_birth' => '1993-12-03',
            'nationality' => 'Bangladeshi',
            'department' => 'Sales',
            'designation' => 'Sales Manager',
            'employment_type' => 'Full-Time',
            'joining_date' => '2021-11-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'nusrat.tabassum@gmail.com',
                    'blood_group' => 'A-',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Md. Sabbir Ahmed',
                    'father_name' => 'Md. Abdul Bari',
                    'mother_name' => 'Saleha Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01772345683',
                ],
            'present_address' => ['house_no' => '30', 'road_no' => '4', 'area' => 'Bashundhara', 'city' => 'Dhaka', 'postal_code' => '1229'],
            'permanent_address' => ['house_no' => '5', 'road_no' => '1', 'area' => 'Comilla Sadar', 'city' => 'Comilla', 'postal_code' => '3500'],
            'educations' => [
                ['degree' => 'Bachelor of Business Administration', 'major_subject' => 'Marketing', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2016, 'result_type' => 'CGPA', 'result_value' => '3.65', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Bata Bangladesh', 'designation' => 'Sales Executive', 'department' => 'Sales', 'from_date' => '2016-07-01', 'to_date' => '2019-06-30', 'is_current' => 0, 'responsibilities' => 'Managed retail sales and distribution channels.', 'reason_for_leaving' => 'Better opportunity'],
                ['company_name' => 'Partex Group', 'designation' => 'Senior Sales Executive', 'department' => 'Sales', 'from_date' => '2019-07-01', 'to_date' => '2021-10-31', 'is_current' => 0, 'responsibilities' => 'Led sales team and achieved quarterly targets.', 'reason_for_leaving' => 'Career growth'],
            ],
            'skills' => [
                ['skill_name' => 'Sales Strategy', 'category_id' => 11, 'proficiency' => 'Expert', 'years_of_experience' => 8],
                ['skill_name' => 'CRM', 'category_id' => 11, 'proficiency' => 'Advanced', 'years_of_experience' => 5],
                ['skill_name' => 'Negotiation', 'category_id' => 14, 'proficiency' => 'Expert', 'years_of_experience' => 8],
                ['skill_name' => 'Client Relationship', 'category_id' => 20, 'proficiency' => 'Expert', 'years_of_experience' => 8],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'Hindi', 'proficiency' => 'Conversational', 'can_read' => 0, 'can_write' => 0, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Shahidul',
            'last_name' => 'Islam',
            'phone' => '01772345684',
            'email' => 'shahidul.islam@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1982-08-12',
            'nationality' => 'Bangladeshi',
            'department' => 'Operations',
            'designation' => 'Operations Manager',
            'employment_type' => 'Full-Time',
            'joining_date' => '2019-04-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'shahidul.islam@yahoo.com',
                    'blood_group' => 'O-',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Nasima Begum',
                    'father_name' => 'Md. Abdul Karim',
                    'mother_name' => 'Rahima Khatun',
                    'religion' => 'Islam',
                    'personal_mobile' => '01782345684',
                ],
            'present_address' => ['house_no' => '7', 'road_no' => '2', 'area' => 'Mohammadpur', 'city' => 'Dhaka', 'postal_code' => '1207'],
            'permanent_address' => ['house_no' => '33', 'road_no' => '4', 'area' => 'Faridpur Sadar', 'city' => 'Faridpur', 'postal_code' => '7800'],
            'educations' => [
                ['degree' => 'Bachelor of Science in Industrial Engineering', 'major_subject' => 'Industrial Engineering', 'institution' => 'BUET', 'board_university' => 'BUET', 'passing_year' => 2006, 'result_type' => 'CGPA', 'result_value' => '3.45', 'is_highest' => 0],
                ['degree' => 'Master of Business Administration', 'major_subject' => 'Operations Management', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2010, 'result_type' => 'CGPA', 'result_value' => '3.50', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'ACI Limited', 'designation' => 'Operations Executive', 'department' => 'Operations', 'from_date' => '2010-05-01', 'to_date' => '2015-08-31', 'is_current' => 0, 'responsibilities' => 'Managed supply chain and logistics operations.', 'reason_for_leaving' => 'Career growth'],
                ['company_name' => 'PRAN Group', 'designation' => 'Senior Operations Executive', 'department' => 'Operations', 'from_date' => '2015-09-01', 'to_date' => '2019-03-31', 'is_current' => 0, 'responsibilities' => 'Oversaw production planning and inventory management.', 'reason_for_leaving' => 'Better position'],
            ],
            'skills' => [
                ['skill_name' => 'Supply Chain Management', 'category_id' => 12, 'proficiency' => 'Expert', 'years_of_experience' => 12],
                ['skill_name' => 'Inventory Management', 'category_id' => 12, 'proficiency' => 'Expert', 'years_of_experience' => 10],
                ['skill_name' => 'Procurement', 'category_id' => 12, 'proficiency' => 'Advanced', 'years_of_experience' => 8],
                ['skill_name' => 'ERP Systems', 'category_id' => 19, 'proficiency' => 'Advanced', 'years_of_experience' => 6],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Tanvir',
            'last_name' => 'Ahmed',
            'phone' => '01782345685',
            'email' => 'tanvir.ahmed@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1997-02-14',
            'nationality' => 'Bangladeshi',
            'department' => 'IT',
            'designation' => 'Software Engineer',
            'employment_type' => 'Full-Time',
            'joining_date' => '2023-01-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'tanvir.ahmed@gmail.com',
                    'blood_group' => 'A+',
                    'marital_status' => 'Single',
                    'father_name' => 'Md. Rafiqul Islam',
                    'mother_name' => 'Shahnaz Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01792345685',
                ],
            'present_address' => ['house_no' => '50', 'road_no' => '10', 'area' => 'Rampura', 'city' => 'Dhaka', 'postal_code' => '1219'],
            'permanent_address' => ['house_no' => '18', 'road_no' => '3', 'area' => 'Narsingdi Sadar', 'city' => 'Narsingdi', 'postal_code' => '1600'],
            'educations' => [
                ['degree' => 'Bachelor of Science in Computer Science', 'major_subject' => 'Computer Science', 'institution' => 'American International University-Bangladesh', 'board_university' => 'AIUB', 'passing_year' => 2020, 'result_type' => 'CGPA', 'result_value' => '3.70', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Tech Mahindra', 'designation' => 'Junior Software Engineer', 'department' => 'IT', 'from_date' => '2020-08-01', 'to_date' => '2022-12-31', 'is_current' => 0, 'responsibilities' => 'Developed RESTful APIs and web applications.', 'reason_for_leaving' => 'Better opportunity'],
            ],
            'skills' => [
                ['skill_name' => 'PHP', 'category_id' => 1, 'proficiency' => 'Advanced', 'years_of_experience' => 4],
                ['skill_name' => 'Laravel', 'category_id' => 2, 'proficiency' => 'Advanced', 'years_of_experience' => 3],
                ['skill_name' => 'JavaScript', 'category_id' => 1, 'proficiency' => 'Advanced', 'years_of_experience' => 4],
                ['skill_name' => 'React', 'category_id' => 2, 'proficiency' => 'Intermediate', 'years_of_experience' => 2],
                ['skill_name' => 'MySQL', 'category_id' => 3, 'proficiency' => 'Advanced', 'years_of_experience' => 4],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Farzana',
            'last_name' => 'Haque',
            'phone' => '01792345686',
            'email' => 'farzana.haque@company.com',
            'gender' => 'Female',
            'date_of_birth' => '1991-06-30',
            'nationality' => 'Bangladeshi',
            'department' => 'Admin',
            'designation' => 'Admin Manager',
            'employment_type' => 'Full-Time',
            'joining_date' => '2020-09-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'farzana.haque@yahoo.com',
                    'blood_group' => 'AB-',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Mahbubur Rahman',
                    'father_name' => 'Md. Shamsul Haque',
                    'mother_name' => 'Rokeya Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01802345686',
                ],
            'present_address' => ['house_no' => '3', 'road_no' => '6', 'area' => 'Shyamoli', 'city' => 'Dhaka', 'postal_code' => '1204'],
            'permanent_address' => ['house_no' => '90', 'road_no' => '2', 'area' => 'Rajshahi Sadar', 'city' => 'Rajshahi', 'postal_code' => '6000'],
            'educations' => [
                ['degree' => 'Bachelor of Business Administration', 'major_subject' => 'Management', 'institution' => 'University of Rajshahi', 'board_university' => 'University of Rajshahi', 'passing_year' => 2014, 'result_type' => 'CGPA', 'result_value' => '3.55', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'BRAC Bank', 'designation' => 'Admin Executive', 'department' => 'Admin', 'from_date' => '2014-10-01', 'to_date' => '2017-12-31', 'is_current' => 0, 'responsibilities' => 'Managed office administration and facilities.', 'reason_for_leaving' => 'Relocation'],
                ['company_name' => 'BEXIMCO Group', 'designation' => 'Senior Admin Executive', 'department' => 'Admin', 'from_date' => '2018-01-01', 'to_date' => '2020-08-31', 'is_current' => 0, 'responsibilities' => 'Supervised administrative operations and vendor management.', 'reason_for_leaving' => 'Better opportunity'],
            ],
            'skills' => [
                ['skill_name' => 'Office Administration', 'category_id' => 10, 'proficiency' => 'Expert', 'years_of_experience' => 9],
                ['skill_name' => 'Vendor Management', 'category_id' => 12, 'proficiency' => 'Advanced', 'years_of_experience' => 5],
                ['skill_name' => 'Microsoft Office', 'category_id' => 18, 'proficiency' => 'Expert', 'years_of_experience' => 10],
                ['skill_name' => 'Event Management', 'category_id' => 15, 'proficiency' => 'Advanced', 'years_of_experience' => 6],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Md. Jubayer',
            'last_name' => 'Ali',
            'phone' => '01802345687',
            'email' => 'jubayer.ali@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1998-10-05',
            'nationality' => 'Bangladeshi',
            'department' => 'IT',
            'designation' => 'Junior Software Engineer',
            'employment_type' => 'Full-Time',
            'joining_date' => '2023-06-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'jubayer.ali@gmail.com',
                    'blood_group' => 'B+',
                    'marital_status' => 'Single',
                    'father_name' => 'Md. Abdul Hakim',
                    'mother_name' => 'Ayesha Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01812345687',
                ],
            'present_address' => ['house_no' => '22', 'road_no' => '3', 'area' => 'Khilgaon', 'city' => 'Dhaka', 'postal_code' => '1219'],
            'permanent_address' => ['house_no' => '12', 'road_no' => '1', 'area' => 'Sylhet Sadar', 'city' => 'Sylhet', 'postal_code' => '3100'],
            'educations' => [
                ['degree' => 'Bachelor of Science in Computer Science & Engineering', 'major_subject' => 'CSE', 'institution' => 'Shahjalal University of Science and Technology', 'board_university' => 'SUST', 'passing_year' => 2023, 'result_type' => 'CGPA', 'result_value' => '3.50', 'is_highest' => 1],
            ],
            'experiences' => [],
            'skills' => [
                ['skill_name' => 'PHP', 'category_id' => 1, 'proficiency' => 'Intermediate', 'years_of_experience' => 1],
                ['skill_name' => 'Laravel', 'category_id' => 2, 'proficiency' => 'Intermediate', 'years_of_experience' => 1],
                ['skill_name' => 'JavaScript', 'category_id' => 1, 'proficiency' => 'Intermediate', 'years_of_experience' => 2],
                ['skill_name' => 'MySQL', 'category_id' => 3, 'proficiency' => 'Intermediate', 'years_of_experience' => 1.5],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Conversational', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Shamima',
            'last_name' => 'Akhtar',
            'phone' => '01812345688',
            'email' => 'shamima.akhtar@company.com',
            'gender' => 'Female',
            'date_of_birth' => '1987-04-20',
            'nationality' => 'Bangladeshi',
            'department' => 'HR',
            'designation' => 'HR Executive',
            'employment_type' => 'Full-Time',
            'joining_date' => '2022-03-15',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'shamima.akhtar@outlook.com',
                    'blood_group' => 'O+',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Md. Shafiqul Islam',
                    'father_name' => 'Md. Joynal Abedin',
                    'mother_name' => 'Mahmuda Khatun',
                    'religion' => 'Islam',
                    'personal_mobile' => '01822345688',
                ],
            'present_address' => ['house_no' => '9', 'road_no' => '5', 'area' => 'Malibagh', 'city' => 'Dhaka', 'postal_code' => '1217'],
            'permanent_address' => ['house_no' => '45', 'road_no' => '2', 'area' => 'Barisal Sadar', 'city' => 'Barisal', 'postal_code' => '8200'],
            'educations' => [
                ['degree' => 'Bachelor of Social Science', 'major_subject' => 'Sociology', 'institution' => 'University of Barisal', 'board_university' => 'University of Barisal', 'passing_year' => 2009, 'result_type' => 'CGPA', 'result_value' => '3.40', 'is_highest' => 0],
                ['degree' => 'Master of Business Administration', 'major_subject' => 'HRM', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2013, 'result_type' => 'CGPA', 'result_value' => '3.45', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Rahimafrooz Group', 'designation' => 'HR Officer', 'department' => 'HR', 'from_date' => '2013-06-01', 'to_date' => '2017-05-31', 'is_current' => 0, 'responsibilities' => 'Handled employee records, attendance, and payroll processing.', 'reason_for_leaving' => 'Better opportunity'],
                ['company_name' => 'Kazi IT Center', 'designation' => 'HR Executive', 'department' => 'HR', 'from_date' => '2017-06-01', 'to_date' => '2022-02-28', 'is_current' => 0, 'responsibilities' => 'Managed full-cycle recruitment and employee engagement.', 'reason_for_leaving' => 'Career advancement'],
            ],
            'skills' => [
                ['skill_name' => 'Recruitment', 'category_id' => 10, 'proficiency' => 'Expert', 'years_of_experience' => 10],
                ['skill_name' => 'Payroll Management', 'category_id' => 10, 'proficiency' => 'Advanced', 'years_of_experience' => 8],
                ['skill_name' => 'Training & Development', 'category_id' => 10, 'proficiency' => 'Advanced', 'years_of_experience' => 6],
                ['skill_name' => 'Microsoft Excel', 'category_id' => 18, 'proficiency' => 'Expert', 'years_of_experience' => 10],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Mizanur',
            'last_name' => 'Rahman',
            'phone' => '01822345689',
            'email' => 'mizanur.rahman@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1986-11-28',
            'nationality' => 'Bangladeshi',
            'department' => 'Finance',
            'designation' => 'Accountant',
            'employment_type' => 'Full-Time',
            'joining_date' => '2021-05-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'mizanur.rahman@gmail.com',
                    'blood_group' => 'A+',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Sharmin Akhter',
                    'father_name' => 'Md. Abdur Rahman',
                    'mother_name' => 'Sufia Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01832345689',
                ],
            'present_address' => ['house_no' => '14', 'road_no' => '9', 'area' => 'Khilbarirtek', 'city' => 'Dhaka', 'postal_code' => '1229'],
            'permanent_address' => ['house_no' => '55', 'road_no' => '1', 'area' => 'Tangail Sadar', 'city' => 'Tangail', 'postal_code' => '1900'],
            'educations' => [
                ['degree' => 'Bachelor of Commerce', 'major_subject' => 'Accounting', 'institution' => 'National University', 'board_university' => 'National University', 'passing_year' => 2010, 'result_type' => 'Division', 'result_value' => 'First', 'is_highest' => 0],
                ['degree' => 'Master of Commerce', 'major_subject' => 'Accounting', 'institution' => 'National University', 'board_university' => 'National University', 'passing_year' => 2013, 'result_type' => 'Division', 'result_value' => 'First', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Navana Group', 'designation' => 'Junior Accountant', 'department' => 'Finance', 'from_date' => '2013-08-01', 'to_date' => '2017-07-31', 'is_current' => 0, 'responsibilities' => 'Maintained ledgers, processed invoices and payments.', 'reason_for_leaving' => 'Better opportunity'],
                ['company_name' => 'Concord Group', 'designation' => 'Accountant', 'department' => 'Finance', 'from_date' => '2017-08-01', 'to_date' => '2021-04-30', 'is_current' => 0, 'responsibilities' => 'Prepared financial statements and tax returns.', 'reason_for_leaving' => 'Career growth'],
            ],
            'skills' => [
                ['skill_name' => 'Accounting', 'category_id' => 9, 'proficiency' => 'Expert', 'years_of_experience' => 10],
                ['skill_name' => 'Taxation', 'category_id' => 9, 'proficiency' => 'Advanced', 'years_of_experience' => 7],
                ['skill_name' => 'Financial Reporting', 'category_id' => 9, 'proficiency' => 'Advanced', 'years_of_experience' => 8],
                ['skill_name' => 'Tally ERP', 'category_id' => 9, 'proficiency' => 'Expert', 'years_of_experience' => 8],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Conversational', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Sabbir',
            'last_name' => 'Hossain',
            'phone' => '01832345690',
            'email' => 'sabbir.hossain@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1994-08-15',
            'nationality' => 'Bangladeshi',
            'department' => 'IT',
            'designation' => 'DevOps Engineer',
            'employment_type' => 'Full-Time',
            'joining_date' => '2022-07-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'sabbir.hossain@outlook.com',
                    'blood_group' => 'AB+',
                    'marital_status' => 'Single',
                    'father_name' => 'Md. Nurul Hossain',
                    'mother_name' => 'Jesmin Akhter',
                    'religion' => 'Islam',
                    'personal_mobile' => '01842345690',
                ],
            'present_address' => ['house_no' => '28', 'road_no' => '11', 'area' => 'Banasree', 'city' => 'Dhaka', 'postal_code' => '1219'],
            'permanent_address' => ['house_no' => '8', 'road_no' => '2', 'area' => 'Jamalpur Sadar', 'city' => 'Jamalpur', 'postal_code' => '2000'],
            'educations' => [
                ['degree' => 'Bachelor of Science in Computer Science', 'major_subject' => 'CSE', 'institution' => 'Daffodil International University', 'board_university' => 'DIU', 'passing_year' => 2017, 'result_type' => 'CGPA', 'result_value' => '3.65', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Samsung R&D Bangladesh', 'designation' => 'DevOps Engineer', 'department' => 'IT', 'from_date' => '2018-01-01', 'to_date' => '2022-06-30', 'is_current' => 0, 'responsibilities' => 'Managed CI/CD pipelines and cloud infrastructure.', 'reason_for_leaving' => 'Better opportunity'],
            ],
            'skills' => [
                ['skill_name' => 'Docker', 'category_id' => 4, 'proficiency' => 'Expert', 'years_of_experience' => 5],
                ['skill_name' => 'Kubernetes', 'category_id' => 4, 'proficiency' => 'Advanced', 'years_of_experience' => 4],
                ['skill_name' => 'AWS', 'category_id' => 4, 'proficiency' => 'Advanced', 'years_of_experience' => 5],
                ['skill_name' => 'CI/CD', 'category_id' => 4, 'proficiency' => 'Expert', 'years_of_experience' => 5],
                ['skill_name' => 'Linux', 'category_id' => 1, 'proficiency' => 'Expert', 'years_of_experience' => 6],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Laila',
            'last_name' => 'Sultana',
            'phone' => '01842345691',
            'email' => 'laila.sultana@company.com',
            'gender' => 'Female',
            'date_of_birth' => '1990-01-08',
            'nationality' => 'Bangladeshi',
            'department' => 'Marketing',
            'designation' => 'Marketing Executive',
            'employment_type' => 'Full-Time',
            'joining_date' => '2022-10-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'laila.sultana@gmail.com',
                    'blood_group' => 'B+',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Md. Kabir Hossain',
                    'father_name' => 'Md. Aminul Islam',
                    'mother_name' => 'Nasrin Sultana',
                    'religion' => 'Islam',
                    'personal_mobile' => '01852345691',
                ],
            'present_address' => ['house_no' => '33', 'road_no' => '7', 'area' => 'Wari', 'city' => 'Dhaka', 'postal_code' => '1203'],
            'permanent_address' => ['house_no' => '60', 'road_no' => '4', 'area' => 'Narayanganj Sadar', 'city' => 'Narayanganj', 'postal_code' => '1400'],
            'educations' => [
                ['degree' => 'Bachelor of Business Administration', 'major_subject' => 'Marketing', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2013, 'result_type' => 'CGPA', 'result_value' => '3.50', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Bashundhara Group', 'designation' => 'Marketing Officer', 'department' => 'Marketing', 'from_date' => '2014-01-01', 'to_date' => '2018-06-30', 'is_current' => 0, 'responsibilities' => 'Developed marketing campaigns and managed brand presence.', 'reason_for_leaving' => 'Relocation'],
                ['company_name' => 'ACI Godrej', 'designation' => 'Brand Executive', 'department' => 'Marketing', 'from_date' => '2018-07-01', 'to_date' => '2022-09-30', 'is_current' => 0, 'responsibilities' => 'Managed brand portfolio and new product launches.', 'reason_for_leaving' => 'Better opportunity'],
            ],
            'skills' => [
                ['skill_name' => 'Brand Management', 'category_id' => 11, 'proficiency' => 'Expert', 'years_of_experience' => 9],
                ['skill_name' => 'Digital Marketing', 'category_id' => 11, 'proficiency' => 'Advanced', 'years_of_experience' => 7],
                ['skill_name' => 'Market Research', 'category_id' => 11, 'proficiency' => 'Advanced', 'years_of_experience' => 8],
                ['skill_name' => 'Content Writing', 'category_id' => 14, 'proficiency' => 'Advanced', 'years_of_experience' => 5],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Kazi Shafiq',
            'last_name' => 'Rahman',
            'phone' => '01852345692',
            'email' => 'shafiq.rahman@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1983-09-12',
            'nationality' => 'Bangladeshi',
            'department' => 'IT',
            'designation' => 'Project Manager',
            'employment_type' => 'Full-Time',
            'joining_date' => '2020-02-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'shafiq.rahman@yahoo.com',
                    'blood_group' => 'O+',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Tamanna Rahman',
                    'father_name' => 'Kazi Abdul Latif',
                    'mother_name' => 'Kazi Shahida Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01862345692',
                ],
            'present_address' => ['house_no' => '5', 'road_no' => '4', 'area' => 'Baridhara', 'city' => 'Dhaka', 'postal_code' => '1212'],
            'permanent_address' => ['house_no' => '20', 'road_no' => '2', 'area' => 'Rangpur Sadar', 'city' => 'Rangpur', 'postal_code' => '5400'],
            'educations' => [
                ['degree' => 'Bachelor of Science in Computer Science', 'major_subject' => 'Computer Science', 'institution' => 'BUET', 'board_university' => 'BUET', 'passing_year' => 2006, 'result_type' => 'CGPA', 'result_value' => '3.55', 'is_highest' => 0],
                ['degree' => 'Master of Science in Software Engineering', 'major_subject' => 'Software Engineering', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2009, 'result_type' => 'CGPA', 'result_value' => '3.60', 'is_highest' => 0],
                ['degree' => 'Project Management Professional (PMP)', 'major_subject' => 'Project Management', 'institution' => 'PMI', 'board_university' => 'PMI', 'passing_year' => 2013, 'result_type' => 'Grade', 'result_value' => 'Pass', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Accenture Bangladesh', 'designation' => 'IT Consultant', 'department' => 'IT', 'from_date' => '2009-07-01', 'to_date' => '2014-08-31', 'is_current' => 0, 'responsibilities' => 'Managed enterprise IT projects for banking clients.', 'reason_for_leaving' => 'Better opportunity'],
                ['company_name' => 'Reve Systems', 'designation' => 'Senior Project Manager', 'department' => 'IT', 'from_date' => '2014-09-01', 'to_date' => '2020-01-31', 'is_current' => 0, 'responsibilities' => 'Led multiple software development projects and Agile teams.', 'reason_for_leaving' => 'Career growth'],
            ],
            'skills' => [
                ['skill_name' => 'Project Management', 'category_id' => 8, 'proficiency' => 'Expert', 'years_of_experience' => 14],
                ['skill_name' => 'Agile & Scrum', 'category_id' => 8, 'proficiency' => 'Expert', 'years_of_experience' => 10],
                ['skill_name' => 'Risk Management', 'category_id' => 8, 'proficiency' => 'Advanced', 'years_of_experience' => 8],
                ['skill_name' => 'JIRA', 'category_id' => 8, 'proficiency' => 'Expert', 'years_of_experience' => 8],
                ['skill_name' => 'Team Management', 'category_id' => 15, 'proficiency' => 'Expert', 'years_of_experience' => 10],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'Arabic', 'proficiency' => 'Basic', 'can_read' => 1, 'can_write' => 0, 'can_speak' => 0],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Taslima',
            'last_name' => 'Begum',
            'phone' => '01862345693',
            'email' => 'taslima.begum@company.com',
            'gender' => 'Female',
            'date_of_birth' => '1996-03-22',
            'nationality' => 'Bangladeshi',
            'department' => 'IT',
            'designation' => 'UI/UX Designer',
            'employment_type' => 'Full-Time',
            'joining_date' => '2023-02-15',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'taslima.begum@gmail.com',
                    'blood_group' => 'A-',
                    'marital_status' => 'Single',
                    'father_name' => 'Md. Abdul Baten',
                    'mother_name' => 'Shahida Akhter',
                    'religion' => 'Islam',
                    'personal_mobile' => '01872345693',
                ],
            'present_address' => ['house_no' => '18', 'road_no' => '5', 'area' => 'Shantibagh', 'city' => 'Dhaka', 'postal_code' => '1217'],
            'permanent_address' => ['house_no' => '35', 'road_no' => '3', 'area' => 'Pabna Sadar', 'city' => 'Pabna', 'postal_code' => '6600'],
            'educations' => [
                ['degree' => 'Bachelor of Fine Arts', 'major_subject' => 'Graphic Design', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2019, 'result_type' => 'CGPA', 'result_value' => '3.60', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Doodle Inc.', 'designation' => 'Junior UI Designer', 'department' => 'Design', 'from_date' => '2019-08-01', 'to_date' => '2021-12-31', 'is_current' => 0, 'responsibilities' => 'Designed user interfaces for web and mobile applications.', 'reason_for_leaving' => 'Better opportunity'],
                ['company_name' => 'Creative IT', 'designation' => 'UI/UX Designer', 'department' => 'Design', 'from_date' => '2022-01-01', 'to_date' => '2023-01-31', 'is_current' => 0, 'responsibilities' => 'Created wireframes, prototypes, and user flows.', 'reason_for_leaving' => 'Career advancement'],
            ],
            'skills' => [
                ['skill_name' => 'Figma', 'category_id' => 7, 'proficiency' => 'Expert', 'years_of_experience' => 5],
                ['skill_name' => 'Adobe XD', 'category_id' => 7, 'proficiency' => 'Advanced', 'years_of_experience' => 4],
                ['skill_name' => 'Wireframing', 'category_id' => 7, 'proficiency' => 'Expert', 'years_of_experience' => 5],
                ['skill_name' => 'Prototyping', 'category_id' => 7, 'proficiency' => 'Advanced', 'years_of_experience' => 4],
                ['skill_name' => 'User Research', 'category_id' => 7, 'proficiency' => 'Advanced', 'years_of_experience' => 3],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Mahbub',
            'last_name' => 'Alam',
            'phone' => '01872345694',
            'email' => 'mahbub.alam@company.com',
            'gender' => 'Male',
            'date_of_birth' => '1980-12-01',
            'nationality' => 'Bangladeshi',
            'department' => 'Legal',
            'designation' => 'Legal Advisor',
            'employment_type' => 'Contractual',
            'joining_date' => '2022-01-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'mahbub.alam@hotmail.com',
                    'blood_group' => 'B+',
                    'marital_status' => 'Married',
                    'spouse_name' => 'Sharmin Alam',
                    'father_name' => 'Late. Abdul Mannan',
                    'mother_name' => 'Rowshan Ara Begum',
                    'religion' => 'Islam',
                    'personal_mobile' => '01882345694',
                ],
            'present_address' => ['house_no' => '40', 'road_no' => '15', 'area' => 'Lalmatia', 'city' => 'Dhaka', 'postal_code' => '1207'],
            'permanent_address' => ['house_no' => '25', 'road_no' => '1', 'area' => 'Chandpur Sadar', 'city' => 'Chandpur', 'postal_code' => '3600'],
            'educations' => [
                ['degree' => 'Bachelor of Laws (LL.B)', 'major_subject' => 'Law', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2003, 'result_type' => 'Division', 'result_value' => 'First', 'is_highest' => 0],
                ['degree' => 'Master of Laws (LL.M)', 'major_subject' => 'Corporate Law', 'institution' => 'University of Dhaka', 'board_university' => 'University of Dhaka', 'passing_year' => 2006, 'result_type' => 'CGPA', 'result_value' => '3.40', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'The Law Chambers', 'designation' => 'Associate Lawyer', 'department' => 'Legal', 'from_date' => '2006-06-01', 'to_date' => '2012-12-31', 'is_current' => 0, 'responsibilities' => 'Handled corporate litigation and contract drafting.', 'reason_for_leaving' => 'Better opportunity'],
                ['company_name' => 'Bashundhara Group', 'designation' => 'Senior Legal Counsel', 'department' => 'Legal', 'from_date' => '2013-01-01', 'to_date' => '2021-12-31', 'is_current' => 0, 'responsibilities' => 'Advised on corporate governance, contracts, and compliance.', 'reason_for_leaving' => 'Contract ended'],
            ],
            'skills' => [
                ['skill_name' => 'Corporate Law', 'category_id' => 6, 'proficiency' => 'Expert', 'years_of_experience' => 15],
                ['skill_name' => 'Contract Drafting', 'category_id' => 6, 'proficiency' => 'Expert', 'years_of_experience' => 15],
                ['skill_name' => 'Compliance', 'category_id' => 6, 'proficiency' => 'Advanced', 'years_of_experience' => 10],
                ['skill_name' => 'Negotiation', 'category_id' => 14, 'proficiency' => 'Expert', 'years_of_experience' => 15],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        $employeeData[] = [
            'first_name' => 'Nazia',
            'last_name' => 'Hassan',
            'phone' => '01882345695',
            'email' => 'nazia.hassan@company.com',
            'gender' => 'Female',
            'date_of_birth' => '1997-07-14',
            'nationality' => 'Bangladeshi',
            'department' => 'IT',
            'designation' => 'Quality Assurance Engineer',
            'employment_type' => 'Full-Time',
            'joining_date' => '2022-09-01',
            'status' => 'Active',
                'personal' => [
                    'personal_email' => 'nazia.hassan@gmail.com',
                    'blood_group' => 'O-',
                    'marital_status' => 'Single',
                    'father_name' => 'Md. Nurul Hassan',
                    'mother_name' => 'Fahmida Hassan',
                    'religion' => 'Islam',
                    'personal_mobile' => '01892345695',
                ],
            'present_address' => ['house_no' => '19', 'road_no' => '6', 'area' => 'Moghbazar', 'city' => 'Dhaka', 'postal_code' => '1217'],
            'permanent_address' => ['house_no' => '42', 'road_no' => '5', 'area' => 'Brahmanbaria Sadar', 'city' => 'Brahmanbaria', 'postal_code' => '3400'],
            'educations' => [
                ['degree' => 'Bachelor of Science in Computer Science', 'major_subject' => 'CSE', 'institution' => 'East West University', 'board_university' => 'East West University', 'passing_year' => 2020, 'result_type' => 'CGPA', 'result_value' => '3.55', 'is_highest' => 1],
            ],
            'experiences' => [
                ['company_name' => 'Cefalo Bangladesh', 'designation' => 'QA Engineer', 'department' => 'IT', 'from_date' => '2020-10-01', 'to_date' => '2022-08-31', 'is_current' => 0, 'responsibilities' => 'Performed manual and automated testing of web applications.', 'reason_for_leaving' => 'Better opportunity'],
            ],
            'skills' => [
                ['skill_name' => 'Manual Testing', 'category_id' => 16, 'proficiency' => 'Expert', 'years_of_experience' => 4],
                ['skill_name' => 'Automated Testing', 'category_id' => 16, 'proficiency' => 'Advanced', 'years_of_experience' => 3],
                ['skill_name' => 'Selenium', 'category_id' => 16, 'proficiency' => 'Advanced', 'years_of_experience' => 3],
                ['skill_name' => 'Test Planning', 'category_id' => 16, 'proficiency' => 'Expert', 'years_of_experience' => 4],
                ['skill_name' => 'Bug Tracking', 'category_id' => 16, 'proficiency' => 'Expert', 'years_of_experience' => 4],
            ],
            'languages' => [
                ['language_name' => 'Bengali', 'proficiency' => 'Native', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
                ['language_name' => 'English', 'proficiency' => 'Professional', 'can_read' => 1, 'can_write' => 1, 'can_speak' => 1],
            ],
        ];

        // Process all employees
        foreach ($employeeData as $index => $data) {
            $employeeCode = 'EMP-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            $fullName = $data['first_name'] . ' ' . $data['last_name'];
            $employee = Employee::create([
                'employee_code' => $employeeCode,
                'branch_id' => $branchId,
                'department_id' => $departmentId,
                'designation_id' => $designationId,
                'grade_id' => $gradeId,
                'shift_id' => $shiftId,
                'employment_type' => $data['employment_type'],
                'joining_date' => $data['joining_date'],
                'status' => $data['status'],
                'portal_active' => 1,
                'created_at' => Carbon::now()->subDays(count($employeeData) - $index),
            ]);

            // Create personal info (including name, contact, and other personal fields)
            EmployeePersonalInfo::create(array_merge(
                [
                    'employee_id' => $employee->id,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'full_name' => $fullName,
                    'display_name' => $fullName,
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'gender' => $data['gender'],
                    'date_of_birth' => $data['date_of_birth'],
                    'nationality' => $data['nationality'],
                ],
                $data['personal'] ?? []
            ));

            // Create present address
            if (isset($data['present_address'])) {
                EmployeeAddress::create(array_merge(
                    ['employee_id' => $employee->id, 'address_type' => 'present', 'is_primary' => 1],
                    $data['present_address']
                ));
            }

            // Create permanent address
            if (isset($data['permanent_address'])) {
                EmployeeAddress::create(array_merge(
                    ['employee_id' => $employee->id, 'address_type' => 'permanent', 'is_primary' => 0],
                    $data['permanent_address']
                ));
            }

            // Create education records
            if (isset($data['educations'])) {
                foreach ($data['educations'] as $edu) {
                    EmployeeEducation::create(array_merge(
                        ['employee_id' => $employee->id],
                        $edu,
                        ['created_at' => Carbon::now()]
                    ));
                }
            }

            // Create experience records
            if (isset($data['experiences'])) {
                foreach ($data['experiences'] as $exp) {
                    EmployeeExperience::create(array_merge(
                        ['employee_id' => $employee->id],
                        $exp,
                        ['created_at' => Carbon::now()]
                    ));
                }
            }

            // Create skills
            if (isset($data['skills'])) {
                foreach ($data['skills'] as $skill) {
                    EmployeeSkill::create(array_merge(
                        ['employee_id' => $employee->id, 'is_active' => 1],
                        $skill
                    ));
                }
            }

            // Create languages
            if (isset($data['languages'])) {
                foreach ($data['languages'] as $lang) {
                    EmployeeLanguage::create(array_merge(
                        ['employee_id' => $employee->id],
                        $lang,
                        ['created_at' => Carbon::now()]
                    ));
                }
            }
        }

        $this->command->info('✓ Employees seeded: ' . count($employeeData) . ' records');
        $this->command->info('✓ Personal info, addresses, educations, experiences, skills, and languages seeded successfully!');
    }
}