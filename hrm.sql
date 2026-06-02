SET FOREIGN_KEY_CHECKS  = 0;
SET sql_mode            = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

DROP DATABASE IF EXISTS hrm_pro;
CREATE DATABASE hrm_pro
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE hrm_pro;

-- ================================================================
-- 01. FOUNDATION TABLES
-- ================================================================

CREATE TABLE companies (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(300)  NOT NULL COMMENT 'Company legal name',
    legal_name      VARCHAR(300) COMMENT 'Registered legal name',
    trade_license   VARCHAR(100) COMMENT 'Trade license number',
    bin_number      VARCHAR(50) COMMENT 'Business Identification Number',
    tin_number      VARCHAR(50) COMMENT 'Tax Identification Number',
    industry        VARCHAR(150) COMMENT 'Industry type',
    founded_year    YEAR COMMENT 'Year established',
    logo_path       VARCHAR(500) COMMENT 'Company logo file path',
    address         TEXT COMMENT 'Registered address',
    city            VARCHAR(100) COMMENT 'City',
    country         VARCHAR(100)  DEFAULT 'Bangladesh' COMMENT 'Country',
    phone           VARCHAR(30) COMMENT 'Contact phone',
    email           VARCHAR(150) COMMENT 'Contact email',
    website         VARCHAR(200) COMMENT 'Company website',
    is_active       TINYINT(1)    DEFAULT 1 COMMENT '0=Inactive, 1=Active',
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) COMMENT='Company master record - stores organization information';

CREATE TABLE branches (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL COMMENT 'FK to companies',
    code            VARCHAR(20)   UNIQUE NOT NULL COMMENT 'Branch code (HO-DHA, BR-CTG)',
    name            VARCHAR(200)  NOT NULL COMMENT 'Branch name',
    address         TEXT COMMENT 'Branch address',
    city            VARCHAR(100) COMMENT 'City',
    country         VARCHAR(100) COMMENT 'Country',
    phone           VARCHAR(30) COMMENT 'Branch phone',
    email           VARCHAR(150) COMMENT 'Branch email',
    is_head_office  TINYINT(1)    DEFAULT 0 COMMENT '1=Head Office',
    is_active       TINYINT(1)    DEFAULT 1 COMMENT '0=Inactive',
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Office/branch locations across regions';

CREATE TABLE fiscal_years (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL COMMENT 'FK to companies',
    label           VARCHAR(20)   NOT NULL COMMENT '2024-2025 format',
    start_date      DATE          NOT NULL COMMENT 'Fiscal year start',
    end_date        DATE          NOT NULL COMMENT 'Fiscal year end',
    is_current      TINYINT(1)    DEFAULT 0 COMMENT '1=Current active FY',
    locked          TINYINT(1)    DEFAULT 0 COMMENT '1=Archived/read-only',
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Financial year definitions for payroll and leave';

-- ================================================================
-- 02. ORGANIZATION STRUCTURE TABLES
-- ================================================================

CREATE TABLE cost_centers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    code            VARCHAR(30)   UNIQUE NOT NULL COMMENT 'Cost center code',
    name            VARCHAR(200)  NOT NULL COMMENT 'Cost center name',
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Budget tracking centers for departments';

CREATE TABLE departments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id       INT UNSIGNED  NOT NULL COMMENT 'FK to branches',
    cost_center_id  INT UNSIGNED COMMENT 'FK to cost_centers',
    parent_id       INT UNSIGNED COMMENT 'Parent department (self-ref)',
    code            VARCHAR(30)   UNIQUE NOT NULL COMMENT 'Department code',
    name            VARCHAR(200)  NOT NULL COMMENT 'Department name',
    description     TEXT COMMENT 'Department description',
    head_employee_id INT UNSIGNED COMMENT 'FK to employees (department head)',
    is_active       TINYINT(1)    DEFAULT 1,
    sort_order      INT           DEFAULT 0,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id)      REFERENCES branches(id),
    FOREIGN KEY (cost_center_id) REFERENCES cost_centers(id),
    FOREIGN KEY (parent_id)      REFERENCES departments(id)
) COMMENT='Department hierarchy (HR, Finance, IT, etc.)';

CREATE TABLE salary_grades (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    code            VARCHAR(20)   NOT NULL COMMENT 'Grade code (G1, G2, M1)',
    name            VARCHAR(100)  NOT NULL COMMENT 'Grade name',
    min_salary      DECIMAL(14,2) DEFAULT 0 COMMENT 'Minimum salary for grade',
    max_salary      DECIMAL(14,2) DEFAULT 0 COMMENT 'Maximum salary for grade',
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Salary grade/band definitions';

CREATE TABLE designations (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_id   INT UNSIGNED  NOT NULL COMMENT 'FK to departments',
    grade_id        INT UNSIGNED COMMENT 'FK to salary_grades',
    code            VARCHAR(30) COMMENT 'Designation code',
    title           VARCHAR(200)  NOT NULL COMMENT 'Job title',
    level           TINYINT       DEFAULT 1 COMMENT 'Hierarchy level (1=Junior, 5=Senior)',
    is_active       TINYINT(1)    DEFAULT 1,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (grade_id)      REFERENCES salary_grades(id)
) COMMENT='Job titles/designations with hierarchy levels';

CREATE TABLE job_positions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    designation_id  INT UNSIGNED  NOT NULL,
    branch_id       INT UNSIGNED  NOT NULL,
    total_slots     INT           DEFAULT 1 COMMENT 'Total positions available',
    filled_slots    INT           DEFAULT 0 COMMENT 'Currently filled positions',
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (designation_id) REFERENCES designations(id),
    FOREIGN KEY (branch_id)      REFERENCES branches(id)
) COMMENT='Available job positions (headcount management)';

-- ================================================================
-- 03. EMPLOYEE MASTER TABLES
-- ================================================================

CREATE TABLE employees (
    id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_code           VARCHAR(50)   UNIQUE NOT NULL COMMENT 'Unique employee ID',
    full_name               VARCHAR(300)  NOT NULL COMMENT 'Full name',
    first_name              VARCHAR(150) COMMENT 'First name',
    last_name               VARCHAR(150) COMMENT 'Last name',
    display_name            VARCHAR(200) COMMENT 'Name to display',
    phone                   VARCHAR(20)   NOT NULL COMMENT 'Primary phone',
    phone_2                 VARCHAR(20) COMMENT 'Alternate phone',
    email                   VARCHAR(200)  UNIQUE COMMENT 'Work email',
    personal_email          VARCHAR(200) COMMENT 'Personal email',
    date_of_birth           DATE COMMENT 'Date of birth',
    place_of_birth          VARCHAR(200) COMMENT 'Birth place',
    gender                  ENUM('Male','Female','Other','Prefer not to say') COMMENT 'Gender',
    blood_group             ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') COMMENT 'Blood group',
    religion                VARCHAR(80) COMMENT 'Religion',
    marital_status          ENUM('Single','Married','Divorced','Widowed','Separated') COMMENT 'Marital status',
    nationality             VARCHAR(100)  DEFAULT 'Bangladeshi' COMMENT 'Nationality',
    spoken_languages        VARCHAR(200) COMMENT 'Languages known',
    nid_number              VARCHAR(50) COMMENT 'National ID number',
    nid_file_path           VARCHAR(500) COMMENT 'NID scan path',
    passport_number         VARCHAR(50) COMMENT 'Passport number',
    passport_expiry         DATE COMMENT 'Passport expiry date',
    passport_file_path      VARCHAR(500) COMMENT 'Passport scan path',
    tin_number              VARCHAR(50) COMMENT 'Tax Identification Number',
    present_address         TEXT COMMENT 'Current address',
    present_city            VARCHAR(100) COMMENT 'Current city',
    present_zip             VARCHAR(20) COMMENT 'Current ZIP',
    permanent_address       TEXT COMMENT 'Permanent address',
    permanent_city          VARCHAR(100) COMMENT 'Permanent city',
    permanent_zip           VARCHAR(20) COMMENT 'Permanent ZIP',
    photo_path              VARCHAR(500) COMMENT 'Employee photo path',
    signature_path          VARCHAR(500) COMMENT 'Signature scan path',
    emergency_name          VARCHAR(200) COMMENT 'Emergency contact name',
    emergency_relation      VARCHAR(80) COMMENT 'Relationship',
    emergency_phone         VARCHAR(20) COMMENT 'Emergency phone',
    emergency_phone_2       VARCHAR(20) COMMENT 'Alternate emergency phone',
    branch_id               INT UNSIGNED  NOT NULL COMMENT 'FK to branches',
    department_id           INT UNSIGNED  NOT NULL COMMENT 'FK to departments',
    designation_id          INT UNSIGNED  NOT NULL COMMENT 'FK to designations',
    grade_id                INT UNSIGNED COMMENT 'FK to salary_grades',
    position_id             INT UNSIGNED COMMENT 'FK to job_positions',
    shift_id                INT UNSIGNED COMMENT 'FK to shifts',
    reports_to              INT UNSIGNED COMMENT 'Manager (self-ref to employees)',
    employment_type         ENUM('Full-Time','Part-Time','Contractual','Intern','Probation') DEFAULT 'Full-Time',
    joining_date            DATE          NOT NULL COMMENT 'Date of joining',
    confirmation_date       DATE COMMENT 'Date of confirmation',
    probation_end_date      DATE COMMENT 'Probation period end',
    last_working_day        DATE COMMENT 'Last working day (if resigned)',
    contract_end_date       DATE COMMENT 'Contract end date',
    basic_salary            DECIMAL(14,2) DEFAULT 0 COMMENT 'Monthly basic salary',
    bank_name               VARCHAR(200) COMMENT 'Bank name',
    bank_branch             VARCHAR(200) COMMENT 'Bank branch',
    bank_account            VARCHAR(80) COMMENT 'Bank account number',
    bank_routing            VARCHAR(50) COMMENT 'Routing number',
    mfs_type                ENUM('bKash','Nagad','Rocket','Upay') COMMENT 'Mobile financial service',
    mfs_number              VARCHAR(20) COMMENT 'MFS account number',
    payment_method          ENUM('Bank','Cash','MFS') DEFAULT 'Bank',
    status                  ENUM('Active','Inactive','On Leave','Suspended','Terminated','Resigned','Retired') DEFAULT 'Active',
    separation_reason       TEXT COMMENT 'Reason for leaving',
    portal_last_login       DATETIME COMMENT 'Last login timestamp',
    portal_active           TINYINT(1)    DEFAULT 0 COMMENT 'Can access portal?',
    created_by              INT UNSIGNED COMMENT 'Who created this record',
    created_at              TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id)       REFERENCES branches(id),
    FOREIGN KEY (department_id)   REFERENCES departments(id),
    FOREIGN KEY (designation_id)  REFERENCES designations(id),
    FOREIGN KEY (grade_id)        REFERENCES salary_grades(id),
    FOREIGN KEY (reports_to)      REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_emp_code     (employee_code),
    INDEX idx_emp_name     (full_name),
    INDEX idx_emp_phone    (phone),
    INDEX idx_emp_status   (status),
    INDEX idx_emp_dept     (department_id),
    INDEX idx_emp_dept_status (department_id, status),
    INDEX idx_emp_dob      (date_of_birth),
    INDEX idx_emp_joining  (joining_date)
) COMMENT='MASTER EMPLOYEE TABLE - All employee personal and work data';

ALTER TABLE departments
    ADD CONSTRAINT fk_dept_head
    FOREIGN KEY (head_employee_id) REFERENCES employees(id) ON DELETE SET NULL;
    
CREATE TABLE shifts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(150)  NOT NULL COMMENT 'Shift name',
    code            VARCHAR(20) COMMENT 'Shift code',
    start_time      TIME          NOT NULL COMMENT 'Shift start time',
    end_time        TIME          NOT NULL COMMENT 'Shift end time',
    break_minutes   INT           DEFAULT 60 COMMENT 'Break duration',
    grace_in_min    INT           DEFAULT 10 COMMENT 'Late grace period',
    grace_out_min   INT           DEFAULT 10 COMMENT 'Early leaving grace',
    work_hours      DECIMAL(4,1)  DEFAULT 8.0 COMMENT 'Net working hours',
    is_night_shift  TINYINT(1)    DEFAULT 0,
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Work shift definitions (Morning, Evening, Night)';

ALTER TABLE employees ADD CONSTRAINT fk_emp_shift FOREIGN KEY (shift_id) REFERENCES shifts(id);

CREATE TABLE employee_job_history (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL COMMENT 'FK to employees',
    effective_date  DATE          NOT NULL COMMENT 'When change happened',
    change_type     ENUM('Joining','Promotion','Demotion','Transfer','Designation Change','Grade Change','Salary Revision','Confirmation','Termination','Resignation','Retirement') NOT NULL,
    from_branch_id  INT UNSIGNED COMMENT 'Previous branch',
    to_branch_id    INT UNSIGNED COMMENT 'New branch',
    from_dept_id    INT UNSIGNED COMMENT 'Previous department',
    to_dept_id      INT UNSIGNED COMMENT 'New department',
    from_desig_id   INT UNSIGNED COMMENT 'Previous designation',
    to_desig_id     INT UNSIGNED COMMENT 'New designation',
    from_salary     DECIMAL(14,2) COMMENT 'Previous salary',
    to_salary       DECIMAL(14,2) COMMENT 'New salary',
    from_grade_id   INT UNSIGNED COMMENT 'Previous grade',
    to_grade_id     INT UNSIGNED COMMENT 'New grade',
    remarks         TEXT COMMENT 'Change reason/notes',
    approved_by     INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
) COMMENT='Complete career history - promotions, transfers, salary changes';

CREATE TABLE employee_experience (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    company_name    VARCHAR(300)  NOT NULL COMMENT 'Previous employer',
    designation     VARCHAR(200) COMMENT 'Previous job title',
    department      VARCHAR(200) COMMENT 'Previous department',
    from_date       DATE COMMENT 'Start date',
    to_date         DATE COMMENT 'End date',
    responsibilities TEXT COMMENT 'Job duties',
    reason_for_leaving VARCHAR(300),
    reference_name  VARCHAR(200) COMMENT 'Reference contact',
    reference_phone VARCHAR(20),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) COMMENT='Previous work experience before joining';

CREATE TABLE employee_education (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    degree          VARCHAR(200)  NOT NULL COMMENT 'Degree name',
    institution     VARCHAR(300) COMMENT 'School/college/university',
    board_university VARCHAR(300) COMMENT 'Board or university name',
    subject_major   VARCHAR(200) COMMENT 'Major subject',
    passing_year    YEAR COMMENT 'Graduation year',
    result_gpa      VARCHAR(50) COMMENT 'GPA or grade',
    certificate_path VARCHAR(500) COMMENT 'Certificate scan',
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) COMMENT='Educational qualifications';

CREATE TABLE employee_certifications (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    name            VARCHAR(300)  NOT NULL COMMENT 'Certification name',
    issuing_body    VARCHAR(300) COMMENT 'Issuing organization',
    issue_date      DATE COMMENT 'Date issued',
    expiry_date     DATE COMMENT 'Expiration date',
    credential_id   VARCHAR(100) COMMENT 'Certificate number',
    file_path       VARCHAR(500) COMMENT 'Certificate scan',
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) COMMENT='Professional certifications and licenses';

CREATE TABLE skill_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)  NOT NULL COMMENT 'Category name',
    description     TEXT
) COMMENT='Skill categories (Technical, Soft Skills, Language)';

CREATE TABLE employee_skills (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    category_id     INT UNSIGNED,
    skill_name      VARCHAR(200)  NOT NULL COMMENT 'Skill name',
    proficiency     ENUM('Beginner','Intermediate','Advanced','Expert') DEFAULT 'Intermediate',
    FOREIGN KEY (employee_id)  REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id)  REFERENCES skill_categories(id)
) COMMENT='Employee skill matrix';

CREATE TABLE employee_awards (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    award_name      VARCHAR(200)  NOT NULL COMMENT 'Award title',
    awarded_by      VARCHAR(200) COMMENT 'Presented by',
    award_date      DATE,
    description     TEXT,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) COMMENT='Employee recognition and awards';

CREATE TABLE employee_dependents (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    full_name       VARCHAR(200)  NOT NULL COMMENT 'Dependent name',
    relation        VARCHAR(80) COMMENT 'Relationship (Spouse, Child, Parent)',
    date_of_birth   DATE,
    nid_number      VARCHAR(50),
    phone           VARCHAR(20),
    is_nominee      TINYINT(1)    DEFAULT 0 COMMENT 'Is nominee for benefits?',
    nominee_percent DECIMAL(5,2)  DEFAULT 0 COMMENT 'Benefit percentage',
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) COMMENT='Family members and benefit nominees';

-- ================================================================
-- 04. DOCUMENTS & FILES
-- ================================================================

CREATE TABLE document_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150)  NOT NULL COMMENT 'Category name',
    requires_expiry TINYINT(1)    DEFAULT 0 COMMENT 'Does doc have expiry?',
    is_mandatory    TINYINT(1)    DEFAULT 0 COMMENT 'Required for all employees?'
) COMMENT='Document types (NID, Passport, Contract, etc.)';

CREATE TABLE employee_documents (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    category_id     INT UNSIGNED,
    document_name   VARCHAR(300) COMMENT 'Document title',
    file_path       VARCHAR(500)  NOT NULL COMMENT 'Storage path',
    file_type       VARCHAR(50) COMMENT 'PDF, JPG, PNG',
    file_size_kb    INT,
    issue_date      DATE,
    expiry_date     DATE COMMENT 'For passport, visa, etc.',
    is_verified     TINYINT(1)    DEFAULT 0 COMMENT 'Verified by HR?',
    verified_by     INT UNSIGNED,
    verified_at     DATETIME,
    notes           TEXT,
    uploaded_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id)  REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id)  REFERENCES document_categories(id),
    FOREIGN KEY (verified_by)  REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_doc_expiry (expiry_date)
) COMMENT='Employee document storage with expiry tracking';

CREATE VIEW vw_expiring_documents AS
SELECT
    ed.id, e.employee_code, e.full_name, e.phone,
    dc.name  AS category,
    ed.document_name,
    ed.expiry_date,
    DATEDIFF(ed.expiry_date, CURDATE()) AS days_remaining
FROM employee_documents ed
JOIN employees e  ON e.id = ed.employee_id
LEFT JOIN document_categories dc ON dc.id = ed.category_id
WHERE ed.expiry_date IS NOT NULL
  AND ed.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY ed.expiry_date
COMMENT='Alert view for documents expiring in 30 days';

-- ================================================================
-- 05. SHIFTS & WORK ROSTERS
-- ================================================================

CREATE TABLE shifts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(150)  NOT NULL COMMENT 'Shift name',
    code            VARCHAR(20) COMMENT 'Shift code',
    start_time      TIME          NOT NULL COMMENT 'Shift start time',
    end_time        TIME          NOT NULL COMMENT 'Shift end time',
    break_minutes   INT           DEFAULT 60 COMMENT 'Break duration',
    grace_in_min    INT           DEFAULT 10 COMMENT 'Late grace period',
    grace_out_min   INT           DEFAULT 10 COMMENT 'Early leaving grace',
    work_hours      DECIMAL(4,1)  DEFAULT 8.0 COMMENT 'Net working hours',
    is_night_shift  TINYINT(1)    DEFAULT 0,
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Work shift definitions (Morning, Evening, Night)';

CREATE TABLE weekend_policies (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(100),
    friday          TINYINT(1)    DEFAULT 1 COMMENT 'Is Friday weekend?',
    saturday        TINYINT(1)    DEFAULT 1,
    sunday          TINYINT(1)    DEFAULT 0,
    monday          TINYINT(1)    DEFAULT 0,
    tuesday         TINYINT(1)    DEFAULT 0,
    wednesday       TINYINT(1)    DEFAULT 0,
    thursday        TINYINT(1)    DEFAULT 0,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Weekly off-day configuration';

CREATE TABLE employee_rosters (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    shift_id        INT UNSIGNED  NOT NULL,
    roster_date     DATE          NOT NULL COMMENT 'Date of shift',
    is_day_off      TINYINT(1)    DEFAULT 0,
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_roster (employee_id, roster_date),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (shift_id)    REFERENCES shifts(id)
) COMMENT='Daily shift assignment for each employee';

CREATE TABLE shift_swap_requests (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    requester_id    INT UNSIGNED  NOT NULL,
    swap_with_id    INT UNSIGNED  NOT NULL,
    swap_date       DATE          NOT NULL,
    reason          TEXT,
    status          ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    approved_by     INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES employees(id),
    FOREIGN KEY (swap_with_id) REFERENCES employees(id)
) COMMENT='Employee shift exchange requests';

-- ================================================================
-- 06. ATTENDANCE MANAGEMENT
-- ================================================================

CREATE TABLE attendance_devices (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id       INT UNSIGNED,
    name            VARCHAR(150) COMMENT 'Device name',
    ip_address      VARCHAR(45) COMMENT 'Device IP',
    device_type     ENUM('Fingerprint','Face','Card','App','Manual') COMMENT 'Device type',
    serial_number   VARCHAR(100),
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (branch_id) REFERENCES branches(id)
) COMMENT='Biometric/Face recognition attendance devices';

CREATE TABLE attendance (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    att_date        DATE          NOT NULL COMMENT 'Attendance date',
    shift_id        INT UNSIGNED COMMENT 'Shift for this day',
    in_time         TIME COMMENT 'Check-in time',
    out_time        TIME COMMENT 'Check-out time',
    working_minutes INT GENERATED ALWAYS AS (
                        CASE WHEN in_time IS NOT NULL AND out_time IS NOT NULL
                        THEN GREATEST(0, TIME_TO_SEC(TIMEDIFF(out_time, in_time)) / 60)
                        ELSE 0 END
                    ) STORED COMMENT 'Total minutes at work',
    break_minutes   INT           DEFAULT 0 COMMENT 'Break taken',
    net_work_minutes INT GENERATED ALWAYS AS (
                        CASE WHEN in_time IS NOT NULL AND out_time IS NOT NULL
                        THEN GREATEST(0,
                            TIME_TO_SEC(TIMEDIFF(out_time, in_time)) / 60 - break_minutes)
                        ELSE 0 END
                    ) STORED COMMENT 'Net productive minutes',
    late_minutes    INT           DEFAULT 0 COMMENT 'Minutes late',
    early_out_minutes INT         DEFAULT 0 COMMENT 'Minutes left early',
    overtime_minutes INT          DEFAULT 0 COMMENT 'Overtime minutes',
    is_late         TINYINT(1)    DEFAULT 0,
    is_early_out    TINYINT(1)    DEFAULT 0,
    is_absent       TINYINT(1)    DEFAULT 0,
    is_holiday_work TINYINT(1)    DEFAULT 0,
    attendance_status ENUM('Present','Absent','Half Day','On Leave','Holiday','Weekend','Late') DEFAULT 'Present',
    device_id       INT UNSIGNED,
    source          ENUM('Device','CSV','App','Manual','API')  DEFAULT 'Manual',
    note            TEXT,
    is_approved     TINYINT(1)    DEFAULT 1,
    approved_by     INT UNSIGNED,
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_emp_date (employee_id, att_date),
    FOREIGN KEY (employee_id)  REFERENCES employees(id),
    FOREIGN KEY (shift_id)     REFERENCES shifts(id),
    FOREIGN KEY (device_id)    REFERENCES attendance_devices(id),
    INDEX idx_att_date   (att_date),
    INDEX idx_att_emp    (employee_id),
    INDEX idx_att_month  (employee_id, att_date)
) COMMENT='Daily attendance punch in/out records';

CREATE TABLE attendance_csv_import (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id        VARCHAR(60)   NOT NULL COMMENT 'Batch identifier',
    batch_label     VARCHAR(200) COMMENT 'Batch description',
    employee_code   VARCHAR(50),
    att_date        DATE,
    in_time         TIME,
    out_time        TIME,
    raw_row         TEXT COMMENT 'Original CSV line',
    matched_emp_id  INT UNSIGNED,
    import_status   ENUM('Pending','Matched','Imported','Duplicate','Error') DEFAULT 'Pending',
    error_message   TEXT,
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_batch (batch_id)
) COMMENT='Staging table for bulk CSV attendance import';

CREATE TABLE attendance_monthly_summary (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    month_year      DATE          NOT NULL COMMENT 'First day of month',
    working_days    INT           DEFAULT 0 COMMENT 'Total working days in month',
    present_days    INT           DEFAULT 0,
    absent_days     INT           DEFAULT 0,
    late_count      INT           DEFAULT 0,
    early_out_count INT           DEFAULT 0,
    half_days       INT           DEFAULT 0,
    leave_days      INT           DEFAULT 0,
    holiday_work    INT           DEFAULT 0,
    total_work_minutes   BIGINT   DEFAULT 0,
    total_ot_minutes     BIGINT   DEFAULT 0,
    total_late_minutes   BIGINT   DEFAULT 0,
    is_finalized    TINYINT(1)    DEFAULT 0 COMMENT 'Locked for payroll?',
    finalized_at    DATETIME,
    UNIQUE KEY uq_emp_month (employee_id, month_year),
    FOREIGN KEY (employee_id) REFERENCES employees(id)
) COMMENT='Pre-calculated monthly attendance for payroll';

-- ================================================================
-- 07. LEAVE MANAGEMENT
-- ================================================================

CREATE TABLE leave_types (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    code            VARCHAR(20)   NOT NULL COMMENT 'CL, SL, AL',
    name            VARCHAR(150)  NOT NULL COMMENT 'Casual, Sick, Annual',
    description     TEXT,
    days_per_year   DECIMAL(5,1)  DEFAULT 0 COMMENT 'Annual entitlement',
    is_paid         TINYINT(1)    DEFAULT 1,
    is_half_day     TINYINT(1)    DEFAULT 1 COMMENT 'Half day allowed?',
    carry_forward   TINYINT(1)    DEFAULT 0,
    max_carry_days  DECIMAL(5,1)  DEFAULT 0,
    max_consecutive INT           DEFAULT 0 COMMENT 'Max days in a row',
    requires_doc    TINYINT(1)    DEFAULT 0 COMMENT 'Medical certificate?',
    min_days_notice INT           DEFAULT 0 COMMENT 'Advance notice required',
    applicable_gender ENUM('All','Male','Female') DEFAULT 'All',
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Leave categories and rules';

CREATE TABLE leave_policies (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(200)  NOT NULL,
    description     TEXT,
    is_default      TINYINT(1)    DEFAULT 0,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Company leave policy templates';

CREATE TABLE leave_policy_details (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    policy_id       INT UNSIGNED  NOT NULL,
    leave_type_id   INT UNSIGNED  NOT NULL,
    days_allowed    DECIMAL(5,1)  NOT NULL,
    FOREIGN KEY (policy_id)      REFERENCES leave_policies(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id)  REFERENCES leave_types(id)
) COMMENT='Policy to leave type mapping';

CREATE TABLE employee_leave_balance (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    leave_type_id   INT UNSIGNED  NOT NULL,
    fiscal_year_id  INT UNSIGNED  NOT NULL,
    opening_balance DECIMAL(5,1)  DEFAULT 0,
    earned_days     DECIMAL(5,1)  DEFAULT 0,
    used_days       DECIMAL(5,1)  DEFAULT 0,
    encashed_days   DECIMAL(5,1)  DEFAULT 0,
    lapsed_days     DECIMAL(5,1)  DEFAULT 0,
    remaining_days  DECIMAL(5,1)  GENERATED ALWAYS AS (
                        opening_balance + earned_days - used_days - encashed_days - lapsed_days
                    ) STORED COMMENT 'Available leave days',
    UNIQUE KEY uq_emp_leave_fy (employee_id, leave_type_id, fiscal_year_id),
    FOREIGN KEY (employee_id)    REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id)  REFERENCES leave_types(id),
    FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id)
) COMMENT='Leave balance per employee per leave type';

CREATE TABLE leave_applications (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    leave_type_id   INT UNSIGNED  NOT NULL,
    application_no  VARCHAR(30)   UNIQUE COMMENT 'Auto-generated request ID',
    from_date       DATE          NOT NULL,
    to_date         DATE          NOT NULL,
    total_days      DECIMAL(5,1)  NOT NULL,
    is_half_day     TINYINT(1)    DEFAULT 0,
    half_day_period ENUM('First Half','Second Half'),
    reason          TEXT,
    document_path   VARCHAR(500) COMMENT 'Supporting document',
    substitute_emp  INT UNSIGNED COMMENT 'Backup employee',
    status          ENUM('Pending','Approved','Rejected','Cancelled','Recalled') DEFAULT 'Pending',
    approved_by     INT UNSIGNED,
    approved_at     DATETIME,
    rejection_reason TEXT,
    applied_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id)   REFERENCES employees(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id),
    FOREIGN KEY (approved_by)   REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (substitute_emp) REFERENCES employees(id) ON DELETE SET NULL,
    INDEX idx_leave_emp    (employee_id),
    INDEX idx_leave_dates  (from_date, to_date),
    INDEX idx_leave_status (status)
) COMMENT='Leave request submissions and approvals';

-- ================================================================
-- 08. HOLIDAYS & CALENDAR
-- ================================================================

CREATE TABLE holiday_presets (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(200)  NOT NULL COMMENT 'Holiday set name',
    fiscal_year_id  INT UNSIGNED,
    description     TEXT,
    is_active       TINYINT(1)    DEFAULT 1,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id)     REFERENCES companies(id),
    FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id)
) COMMENT='Holiday calendar templates';

CREATE TABLE holidays (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    preset_id       INT UNSIGNED,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(300)  NOT NULL COMMENT 'Holiday name',
    holiday_date    DATE          NOT NULL,
    end_date        DATE COMMENT 'For multi-day holidays',
    total_days      INT GENERATED ALWAYS AS (DATEDIFF(COALESCE(end_date, holiday_date), holiday_date) + 1) STORED,
    holiday_type    ENUM('Public','Government','Company','Optional','Restricted') DEFAULT 'Public',
    applicable_to   ENUM('All','Branch','Department') DEFAULT 'All',
    target_id       INT UNSIGNED COMMENT 'Branch or department ID',
    is_recurring    TINYINT(1)    DEFAULT 0 COMMENT 'Same date every year?',
    remarks         TEXT,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (preset_id)  REFERENCES holiday_presets(id) ON DELETE SET NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_holiday_date (holiday_date)
) COMMENT='Individual holiday dates';

-- ================================================================
-- 09. PAYROLL SYSTEM (Complete)
-- ================================================================

CREATE TABLE salary_components (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    code            VARCHAR(30)   UNIQUE NOT NULL COMMENT 'Component code',
    name            VARCHAR(200)  NOT NULL COMMENT 'Component name',
    type            ENUM('Earning','Deduction') NOT NULL,
    calculation     ENUM('Fixed','Percentage of Basic','Percentage of Gross') DEFAULT 'Fixed',
    default_value   DECIMAL(10,4) DEFAULT 0 COMMENT 'Fixed amount or percentage',
    is_taxable      TINYINT(1)    DEFAULT 0,
    is_pf_basis     TINYINT(1)    DEFAULT 0 COMMENT 'Counts for PF calculation?',
    show_in_slip    TINYINT(1)    DEFAULT 1,
    is_active       TINYINT(1)    DEFAULT 1,
    sort_order      INT           DEFAULT 0,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Salary components (Basic, HRA, PF, Tax, etc.)';

CREATE TABLE employee_salary_structure (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    component_id    INT UNSIGNED  NOT NULL,
    value           DECIMAL(14,4) NOT NULL COMMENT 'Amount or percentage',
    effective_from  DATE          NOT NULL COMMENT 'Start date',
    effective_to    DATE COMMENT 'End date (NULL = current)',
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id)  REFERENCES employees(id),
    FOREIGN KEY (component_id) REFERENCES salary_components(id)
) COMMENT='Employee-specific salary component values';

CREATE TABLE tax_slabs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    fiscal_year_id  INT UNSIGNED  NOT NULL,
    income_from     DECIMAL(14,2) NOT NULL COMMENT 'Minimum income',
    income_to       DECIMAL(14,2) COMMENT 'Maximum income (NULL = unlimited)',
    tax_rate        DECIMAL(5,2)  NOT NULL COMMENT 'Tax percentage',
    FOREIGN KEY (company_id)     REFERENCES companies(id),
    FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id)
) COMMENT='Income tax brackets';

CREATE TABLE bonus_types (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(150)  NOT NULL COMMENT 'Eid Bonus, Festival Bonus',
    calculation     ENUM('Fixed','% of Basic','% of Gross') DEFAULT 'Fixed',
    default_value   DECIMAL(10,4) DEFAULT 0,
    is_taxable      TINYINT(1)    DEFAULT 0,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Bonus categories';

CREATE TABLE payroll_runs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    fiscal_year_id  INT UNSIGNED  NOT NULL,
    month_year      DATE          NOT NULL COMMENT 'First day of payroll month',
    label           VARCHAR(100) COMMENT 'Payroll reference',
    working_days    INT           DEFAULT 0,
    status          ENUM('Draft','Processing','Reviewed','Approved','Disbursed','Locked') DEFAULT 'Draft',
    approved_by     INT UNSIGNED,
    approved_at     DATETIME,
    disbursed_by    INT UNSIGNED,
    disbursed_at    DATETIME,
    notes           TEXT,
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_run (company_id, month_year),
    FOREIGN KEY (company_id)     REFERENCES companies(id),
    FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id)
) COMMENT='Monthly payroll processing batch';

CREATE TABLE payroll_salary_slips (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    run_id              INT UNSIGNED  NOT NULL,
    employee_id         INT UNSIGNED  NOT NULL,
    pay_month           DATE          NOT NULL,
    scheduled_days      INT           DEFAULT 0 COMMENT 'Total working days',
    present_days        DECIMAL(5,1)  DEFAULT 0,
    absent_days         DECIMAL(5,1)  DEFAULT 0,
    leave_days          DECIMAL(5,1)  DEFAULT 0,
    late_count          INT           DEFAULT 0,
    basic_salary        DECIMAL(14,2) DEFAULT 0,
    total_earnings      DECIMAL(14,2) DEFAULT 0 COMMENT 'Sum of all earnings',
    total_deductions    DECIMAL(14,2) DEFAULT 0,
    gross_salary        DECIMAL(14,2) DEFAULT 0,
    taxable_income      DECIMAL(14,2) DEFAULT 0,
    income_tax          DECIMAL(14,2) DEFAULT 0,
    net_payable         DECIMAL(14,2) DEFAULT 0,
    late_deduction      DECIMAL(14,2) DEFAULT 0,
    absent_deduction    DECIMAL(14,2) DEFAULT 0,
    advance_deduction   DECIMAL(14,2) DEFAULT 0,
    loan_deduction      DECIMAL(14,2) DEFAULT 0,
    pf_deduction        DECIMAL(14,2) DEFAULT 0,
    status              ENUM('Draft','Approved','Paid') DEFAULT 'Draft',
    payment_date        DATE,
    payment_method      ENUM('Bank','Cash','MFS') DEFAULT 'Bank',
    bank_ref            VARCHAR(100) COMMENT 'Transaction reference',
    UNIQUE KEY uq_slip (run_id, employee_id),
    FOREIGN KEY (run_id)       REFERENCES payroll_runs(id),
    FOREIGN KEY (employee_id)  REFERENCES employees(id),
    INDEX idx_slip_emp   (employee_id),
    INDEX idx_slip_month (pay_month)
) COMMENT='Individual salary slip';

CREATE TABLE payroll_slip_lines (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slip_id         INT UNSIGNED  NOT NULL,
    component_id    INT UNSIGNED  NOT NULL,
    amount          DECIMAL(14,2) NOT NULL COMMENT 'Component amount for this slip',
    FOREIGN KEY (slip_id)      REFERENCES payroll_salary_slips(id) ON DELETE CASCADE,
    FOREIGN KEY (component_id) REFERENCES salary_components(id)
) COMMENT='Breakdown of salary slip into components';

CREATE TABLE payroll_bonus (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    bonus_type_id   INT UNSIGNED  NOT NULL,
    run_id          INT UNSIGNED,
    amount          DECIMAL(14,2) NOT NULL,
    reason          TEXT,
    status          ENUM('Pending','Approved','Paid') DEFAULT 'Pending',
    payment_date    DATE,
    approved_by     INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id)   REFERENCES employees(id),
    FOREIGN KEY (bonus_type_id) REFERENCES bonus_types(id),
    FOREIGN KEY (run_id)        REFERENCES payroll_runs(id)
) COMMENT='Bonus payments to employees';

CREATE TABLE payroll_advances (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    request_date    DATE          NOT NULL,
    amount          DECIMAL(14,2) NOT NULL,
    reason          TEXT,
    status          ENUM('Pending','Approved','Rejected','Deducted','Cancelled') DEFAULT 'Pending',
    approved_by     INT UNSIGNED,
    approved_date   DATE,
    recover_month   DATE COMMENT 'Month to deduct from salary',
    recovered       TINYINT(1)    DEFAULT 0,
    slip_id         INT UNSIGNED COMMENT 'Salary slip where deducted',
    FOREIGN KEY (employee_id)  REFERENCES employees(id),
    FOREIGN KEY (approved_by)  REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (slip_id)      REFERENCES payroll_salary_slips(id)
) COMMENT='Salary advance requests';

CREATE TABLE payroll_loans (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    loan_amount     DECIMAL(14,2) NOT NULL,
    interest_rate   DECIMAL(5,2)  DEFAULT 0,
    total_installments INT        NOT NULL,
    monthly_deduction  DECIMAL(14,2) NOT NULL,
    start_month     DATE          NOT NULL,
    amount_paid     DECIMAL(14,2) DEFAULT 0,
    outstanding     DECIMAL(14,2) GENERATED ALWAYS AS (loan_amount - amount_paid) STORED,
    status          ENUM('Pending','Active','Completed','Cancelled','Defaulted') DEFAULT 'Pending',
    approved_by     INT UNSIGNED,
    approved_date   DATE,
    purpose         TEXT,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (approved_by) REFERENCES employees(id) ON DELETE SET NULL
) COMMENT='Employee loan management';

CREATE TABLE payroll_loan_installments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    loan_id         INT UNSIGNED  NOT NULL,
    slip_id         INT UNSIGNED,
    installment_no  INT           NOT NULL,
    month_year      DATE          NOT NULL,
    amount          DECIMAL(14,2) NOT NULL,
    paid            TINYINT(1)    DEFAULT 0,
    paid_date       DATE,
    FOREIGN KEY (loan_id)   REFERENCES payroll_loans(id) ON DELETE CASCADE,
    FOREIGN KEY (slip_id)   REFERENCES payroll_salary_slips(id)
) COMMENT='Monthly loan deduction schedule';

CREATE TABLE bank_transfer_batches (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    run_id          INT UNSIGNED  NOT NULL,
    batch_ref       VARCHAR(100),
    bank_name       VARCHAR(200),
    total_amount    DECIMAL(16,2) DEFAULT 0,
    total_entries   INT           DEFAULT 0,
    status          ENUM('Draft','Submitted','Processed','Failed') DEFAULT 'Draft',
    submitted_at    DATETIME,
    processed_at    DATETIME,
    file_path       VARCHAR(500) COMMENT 'Generated bank file path',
    FOREIGN KEY (run_id) REFERENCES payroll_runs(id)
) COMMENT='Bulk salary disbursement to bank';

CREATE TABLE provident_fund (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    month_year      DATE          NOT NULL,
    employee_contrib DECIMAL(14,2) DEFAULT 0,
    employer_contrib DECIMAL(14,2) DEFAULT 0,
    total_contrib    DECIMAL(14,2) GENERATED ALWAYS AS (employee_contrib + employer_contrib) STORED,
    slip_id         INT UNSIGNED,
    UNIQUE KEY uq_pf (employee_id, month_year),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (slip_id)     REFERENCES payroll_salary_slips(id)
) COMMENT='Provident Fund contributions tracking';

-- ================================================================
-- 10. PERFORMANCE MANAGEMENT
-- ================================================================

CREATE TABLE kpi_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(200)  NOT NULL COMMENT 'Productivity, Quality, etc.',
    description     TEXT,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='KPI category groups';

CREATE TABLE kpis (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id     INT UNSIGNED  NOT NULL,
    title           VARCHAR(300)  NOT NULL,
    description     TEXT,
    unit            VARCHAR(50) COMMENT '%, Count, Score',
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES kpi_categories(id)
) COMMENT='Individual KPIs for performance evaluation';

CREATE TABLE appraisal_cycles (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(200)  NOT NULL,
    fiscal_year_id  INT UNSIGNED  NOT NULL,
    cycle_type      ENUM('Annual','Semi-Annual','Quarterly','Monthly') DEFAULT 'Annual',
    review_from     DATE COMMENT 'Review period start',
    review_to       DATE COMMENT 'Review period end',
    status          ENUM('Planned','Active','Closed') DEFAULT 'Planned',
    FOREIGN KEY (company_id)     REFERENCES companies(id),
    FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id)
) COMMENT='Performance review periods';

CREATE TABLE performance_reviews (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cycle_id        INT UNSIGNED  NOT NULL,
    employee_id     INT UNSIGNED  NOT NULL,
    reviewer_id     INT UNSIGNED  NOT NULL COMMENT 'Manager or HR',
    self_rating     DECIMAL(3,1) COMMENT 'Self assessment (1-5)',
    reviewer_rating DECIMAL(3,1) COMMENT 'Manager rating',
    final_rating    DECIMAL(3,1) COMMENT 'Final approved rating',
    rating_label    VARCHAR(100) COMMENT 'Excellent, Good, Average',
    self_comments   TEXT,
    reviewer_comments TEXT,
    hod_comments    TEXT,
    status          ENUM('Pending','Self Submitted','Reviewed','HR Approved','Closed') DEFAULT 'Pending',
    submitted_at    DATETIME,
    reviewed_at     DATETIME,
    UNIQUE KEY uq_review (cycle_id, employee_id),
    FOREIGN KEY (cycle_id)     REFERENCES appraisal_cycles(id),
    FOREIGN KEY (employee_id)  REFERENCES employees(id),
    FOREIGN KEY (reviewer_id)  REFERENCES employees(id)
) COMMENT='Employee performance review records';

-- ================================================================
-- 11. TRAINING MANAGEMENT
-- ================================================================

CREATE TABLE training_programs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    title           VARCHAR(300)  NOT NULL,
    description     TEXT,
    category        VARCHAR(150),
    mode            ENUM('In-House','External','Online','Blended') DEFAULT 'In-House',
    trainer_name    VARCHAR(200),
    venue           VARCHAR(300),
    start_date      DATE,
    end_date        DATE,
    duration_hours  DECIMAL(6,1),
    budget          DECIMAL(14,2),
    status          ENUM('Planned','Ongoing','Completed','Cancelled') DEFAULT 'Planned',
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Training course catalog';

CREATE TABLE training_enrollments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    program_id      INT UNSIGNED  NOT NULL,
    employee_id     INT UNSIGNED  NOT NULL,
    enrolled_by     INT UNSIGNED,
    enrollment_date DATE,
    attendance_pct  DECIMAL(5,2),
    test_score      DECIMAL(5,2),
    result          ENUM('Pass','Fail','Incomplete','Exempt'),
    certificate_path VARCHAR(500),
    feedback        TEXT,
    UNIQUE KEY uq_enroll (program_id, employee_id),
    FOREIGN KEY (program_id)  REFERENCES training_programs(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id)
) COMMENT='Employee training registration';

-- ================================================================
-- 12. RECRUITMENT (Talent Acquisition)
-- ================================================================

CREATE TABLE job_postings (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    department_id   INT UNSIGNED  NOT NULL,
    designation_id  INT UNSIGNED,
    grade_id        INT UNSIGNED,
    title           VARCHAR(300)  NOT NULL COMMENT 'Job title',
    job_code        VARCHAR(50)   UNIQUE,
    employment_type ENUM('Full-Time','Part-Time','Contractual','Intern') DEFAULT 'Full-Time',
    vacancies       INT           DEFAULT 1,
    description     TEXT COMMENT 'Job description',
    requirements    TEXT,
    responsibilities TEXT,
    skills_required TEXT,
    min_experience  DECIMAL(4,1)  DEFAULT 0 COMMENT 'Years required',
    salary_from     DECIMAL(14,2),
    salary_to       DECIMAL(14,2),
    posting_date    DATE,
    deadline        DATE,
    status          ENUM('Draft','Published','On Hold','Closed','Filled') DEFAULT 'Draft',
    posted_by       INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id)    REFERENCES companies(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (designation_id) REFERENCES designations(id)
) COMMENT='Job vacancy announcements';

CREATE TABLE recruitment_stages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(150)  NOT NULL COMMENT 'Screening, Interview, Offer',
    sort_order      INT           DEFAULT 0,
    is_final_pass   TINYINT(1)    DEFAULT 0 COMMENT 'Hiring stage?',
    is_final_reject TINYINT(1)    DEFAULT 0,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Hiring pipeline stages';

CREATE TABLE job_applicants (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id          INT UNSIGNED  NOT NULL,
    applicant_no    VARCHAR(30)   UNIQUE,
    full_name       VARCHAR(300)  NOT NULL,
    email           VARCHAR(200),
    phone           VARCHAR(20)   NOT NULL,
    address         TEXT,
    date_of_birth   DATE,
    gender          ENUM('Male','Female','Other'),
    current_company VARCHAR(300),
    current_desig   VARCHAR(200),
    current_salary  DECIMAL(14,2),
    expected_salary DECIMAL(14,2),
    notice_period   INT           DEFAULT 0,
    experience_yrs  DECIMAL(4,1),
    cv_path         VARCHAR(500) COMMENT 'Resume file path',
    cover_letter    TEXT,
    source          VARCHAR(100) COMMENT 'LinkedIn, Referral, Website',
    referral_emp_id INT UNSIGNED COMMENT 'Employee who referred',
    stage_id        INT UNSIGNED,
    status          ENUM('Applied','Screening','Shortlisted','Interview','Assessment','Offer','Selected','Joined','Rejected','Withdrawn') DEFAULT 'Applied',
    applied_date    DATE          NOT NULL,
    notes           TEXT,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id)          REFERENCES job_postings(id),
    FOREIGN KEY (referral_emp_id) REFERENCES employees(id),
    FOREIGN KEY (stage_id)        REFERENCES recruitment_stages(id)
) COMMENT='Job applicants/candidates';

CREATE TABLE interview_panels (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    applicant_id    INT UNSIGNED  NOT NULL,
    interview_no    INT           DEFAULT 1 COMMENT '1st, 2nd round',
    scheduled_at    DATETIME      NOT NULL,
    duration_min    INT           DEFAULT 60,
    mode            ENUM('In-Person','Phone','Video') DEFAULT 'In-Person',
    venue           VARCHAR(300),
    meeting_link    VARCHAR(500),
    status          ENUM('Scheduled','Completed','Cancelled','No Show') DEFAULT 'Scheduled',
    result          ENUM('Pass','Fail','On Hold','Pending') DEFAULT 'Pending',
    overall_rating  DECIMAL(3,1),
    feedback        TEXT,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES job_applicants(id)
) COMMENT='Interview schedules';

CREATE TABLE interview_interviewers (
    panel_id        INT UNSIGNED  NOT NULL,
    employee_id     INT UNSIGNED  NOT NULL,
    rating          DECIMAL(3,1),
    comments        TEXT,
    PRIMARY KEY (panel_id, employee_id),
    FOREIGN KEY (panel_id)    REFERENCES interview_panels(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
) COMMENT='Panel members for each interview';

CREATE TABLE job_offers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    applicant_id    INT UNSIGNED  NOT NULL  UNIQUE,
    offered_basic   DECIMAL(14,2),
    offered_gross   DECIMAL(14,2),
    joining_date    DATE,
    offer_date      DATE,
    expiry_date     DATE,
    offer_letter_path VARCHAR(500),
    status          ENUM('Pending','Accepted','Declined','Expired','Revoked') DEFAULT 'Pending',
    response_date   DATE,
    response_notes  TEXT,
    FOREIGN KEY (applicant_id) REFERENCES job_applicants(id)
) COMMENT='Offer letters to selected candidates';

-- ================================================================
-- 13. ONBOARDING (New Hire Integration)
-- ================================================================

CREATE TABLE onboarding_checklists (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(200),
    applicable_to   ENUM('All','Department','Designation') DEFAULT 'All',
    target_id       INT UNSIGNED COMMENT 'Department or designation ID',
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Task templates for new employees';

CREATE TABLE onboarding_checklist_items (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    checklist_id    INT UNSIGNED  NOT NULL,
    task_title      VARCHAR(300)  NOT NULL,
    description     TEXT,
    responsible     ENUM('HR','Manager','Employee','IT','Admin'),
    due_days        INT           DEFAULT 1 COMMENT 'Days after joining',
    is_mandatory    TINYINT(1)    DEFAULT 1,
    sort_order      INT           DEFAULT 0,
    FOREIGN KEY (checklist_id) REFERENCES onboarding_checklists(id) ON DELETE CASCADE
) COMMENT='Individual onboarding tasks';

CREATE TABLE employee_onboarding (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL  UNIQUE,
    checklist_id    INT UNSIGNED,
    status          ENUM('In Progress','Completed','Overdue') DEFAULT 'In Progress',
    started_at      DATE,
    completed_at    DATE,
    FOREIGN KEY (employee_id)   REFERENCES employees(id),
    FOREIGN KEY (checklist_id)  REFERENCES onboarding_checklists(id)
) COMMENT='Employee onboarding progress';

-- ================================================================
-- 14. OFFBOARDING (Separation Management)
-- ================================================================

CREATE TABLE resignation_requests (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    resignation_date DATE         NOT NULL,
    last_working_day DATE,
    notice_period   INT           DEFAULT 30,
    reason_type     ENUM('Personal','Better Opportunity','Higher Education','Health','Relocation','Family','Other'),
    reason_detail   TEXT,
    attachment_path VARCHAR(500),
    status          ENUM('Submitted','Under Review','Accepted','Rejected','Withdrawn') DEFAULT 'Submitted',
    reviewed_by     INT UNSIGNED,
    reviewed_at     DATETIME,
    remarks         TEXT,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id)  REFERENCES employees(id),
    FOREIGN KEY (reviewed_by)  REFERENCES employees(id)
) COMMENT='Employee resignation submissions';

CREATE TABLE clearance_checklists (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(200),
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Exit clearance templates';

CREATE TABLE clearance_items (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    checklist_id    INT UNSIGNED  NOT NULL,
    department      VARCHAR(150),
    task_title      VARCHAR(300)  NOT NULL,
    responsible     VARCHAR(150),
    sort_order      INT           DEFAULT 0,
    FOREIGN KEY (checklist_id) REFERENCES clearance_checklists(id) ON DELETE CASCADE
) COMMENT='Individual clearance tasks';

CREATE TABLE employee_clearances (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    checklist_id    INT UNSIGNED,
    initiated_date  DATE,
    status          ENUM('Pending','In Progress','Cleared','Blocked') DEFAULT 'Pending',
    final_settlement DECIMAL(14,2),
    settlement_date DATE,
    FOREIGN KEY (employee_id)  REFERENCES employees(id),
    FOREIGN KEY (checklist_id) REFERENCES clearance_checklists(id)
) COMMENT='Employee exit clearance status';

-- ================================================================
-- 15. ASSET MANAGEMENT
-- ================================================================

CREATE TABLE asset_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(150)  NOT NULL COMMENT 'Laptop, Phone, Vehicle',
    description     TEXT,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Asset type categories';

CREATE TABLE assets (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    category_id     INT UNSIGNED,
    asset_code      VARCHAR(80)   UNIQUE NOT NULL COMMENT 'Asset tag number',
    name            VARCHAR(300)  NOT NULL COMMENT 'Asset name',
    brand           VARCHAR(150),
    model           VARCHAR(150),
    serial_number   VARCHAR(150),
    purchase_date   DATE,
    purchase_price  DECIMAL(14,2),
    warranty_expiry DATE,
    condition       ENUM('New','Good','Fair','Poor','Scrapped') DEFAULT 'Good',
    status          ENUM('Available','Assigned','Under Repair','Scrapped') DEFAULT 'Available',
    location        VARCHAR(300) COMMENT 'Storage location',
    notes           TEXT,
    FOREIGN KEY (company_id)   REFERENCES companies(id),
    FOREIGN KEY (category_id)  REFERENCES asset_categories(id)
) COMMENT='Company asset inventory';

CREATE TABLE asset_assignments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id        INT UNSIGNED  NOT NULL,
    employee_id     INT UNSIGNED  NOT NULL,
    assigned_date   DATE          NOT NULL,
    return_date     DATE,
    condition_out   ENUM('New','Good','Fair','Poor'),
    condition_in    ENUM('Good','Fair','Poor','Damaged'),
    assigned_by     INT UNSIGNED,
    returned_to     INT UNSIGNED,
    notes_out       TEXT,
    notes_in        TEXT,
    status          ENUM('Assigned','Returned','Lost') DEFAULT 'Assigned',
    FOREIGN KEY (asset_id)    REFERENCES assets(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id)
) COMMENT='Asset allocation to employees';

-- ================================================================
-- 16. TASK & PROJECT MANAGEMENT
-- ================================================================

CREATE TABLE task_projects (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(300)  NOT NULL,
    description     TEXT,
    department_id   INT UNSIGNED,
    manager_id      INT UNSIGNED,
    start_date      DATE,
    end_date        DATE,
    status          ENUM('Planning','Active','On Hold','Completed','Cancelled') DEFAULT 'Planning',
    FOREIGN KEY (company_id)   REFERENCES companies(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (manager_id)   REFERENCES employees(id)
) COMMENT='Project grouping for tasks';

CREATE TABLE tasks (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    project_id      INT UNSIGNED,
    parent_task_id  INT UNSIGNED COMMENT 'For subtasks',
    title           VARCHAR(500)  NOT NULL,
    description     TEXT,
    assigned_to     INT UNSIGNED  NOT NULL,
    assigned_by     INT UNSIGNED  NOT NULL,
    assign_date     DATE          NOT NULL,
    start_date      DATE,
    deadline        DATE          NOT NULL,
    done_date       DATE,
    estimated_hours DECIMAL(6,1),
    actual_hours    DECIMAL(6,1),
    priority        ENUM('Low','Medium','High','Critical') DEFAULT 'Medium',
    status          ENUM('To Do','In Progress','Under Review','Done','Cancelled','On Hold') DEFAULT 'To Do',
    deadline_missed TINYINT(1)    GENERATED ALWAYS AS (
                        CASE WHEN status NOT IN ('Done','Cancelled')
                             AND deadline < CURDATE() THEN 1 ELSE 0 END
                    ) STORED COMMENT 'Flag for missed deadline',
    completion_pct  INT           DEFAULT 0,
    tags            VARCHAR(500),
    attachment_path VARCHAR(500),
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id)    REFERENCES companies(id),
    FOREIGN KEY (project_id)    REFERENCES task_projects(id),
    FOREIGN KEY (parent_task_id) REFERENCES tasks(id),
    FOREIGN KEY (assigned_to)   REFERENCES employees(id),
    FOREIGN KEY (assigned_by)   REFERENCES employees(id),
    INDEX idx_task_assignee  (assigned_to),
    INDEX idx_task_deadline  (deadline),
    INDEX idx_task_status    (status)
) COMMENT='Individual tasks with deadlines';

-- ================================================================
-- 17. NOTICE BOARD
-- ================================================================

CREATE TABLE notice_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(150),
    color_hex       VARCHAR(10) COMMENT 'UI display color',
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Announcement categories';

CREATE TABLE notices (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    category_id     INT UNSIGNED,
    title           VARCHAR(500)  NOT NULL,
    content         LONGTEXT      NOT NULL,
    target_type     ENUM('All','Branch','Department','Designation','Individual') DEFAULT 'All',
    target_id       INT UNSIGNED,
    priority        ENUM('Normal','High','Urgent') DEFAULT 'Normal',
    publish_date    DATE          NOT NULL,
    expiry_date     DATE,
    attachment_path VARCHAR(500),
    requires_ack    TINYINT(1)    DEFAULT 0 COMMENT 'Require read receipt?',
    posted_by       INT UNSIGNED,
    is_active       TINYINT(1)    DEFAULT 1,
    view_count      INT           DEFAULT 0,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id)   REFERENCES companies(id),
    FOREIGN KEY (category_id)  REFERENCES notice_categories(id),
    FOREIGN KEY (posted_by)    REFERENCES employees(id)
) COMMENT='Company announcements';

CREATE TABLE notice_acknowledgements (
    notice_id       INT UNSIGNED  NOT NULL,
    employee_id     INT UNSIGNED  NOT NULL,
    acknowledged_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    comments        TEXT,
    PRIMARY KEY (notice_id, employee_id),
    FOREIGN KEY (notice_id)    REFERENCES notices(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id)  REFERENCES employees(id)
) COMMENT='Employee read receipts for notices';

-- ================================================================
-- 18. MESSAGING & SMS
-- ================================================================

CREATE TABLE sms_templates (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    code            VARCHAR(50)   UNIQUE,
    name            VARCHAR(200),
    body            TEXT          NOT NULL COMMENT 'Message with placeholders',
    variables       VARCHAR(500) COMMENT 'JSON list of placeholders',
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Reusable SMS message templates';

CREATE TABLE sms_campaigns (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    template_id     INT UNSIGNED,
    name            VARCHAR(200),
    target_type     ENUM('All','Department','Individual'),
    target_id       INT UNSIGNED,
    message         TEXT          NOT NULL,
    scheduled_at    DATETIME,
    sent_at         DATETIME,
    total_recipients INT          DEFAULT 0,
    sent_count      INT           DEFAULT 0,
    failed_count    INT           DEFAULT 0,
    status          ENUM('Draft','Scheduled','Sent','Failed') DEFAULT 'Draft',
    FOREIGN KEY (company_id)  REFERENCES companies(id),
    FOREIGN KEY (template_id) REFERENCES sms_templates(id)
) COMMENT='Bulk SMS campaign management';

CREATE TABLE sms_logs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id     INT UNSIGNED,
    employee_id     INT UNSIGNED,
    phone           VARCHAR(25)   NOT NULL,
    message         TEXT          NOT NULL,
    gateway_name    VARCHAR(100),
    gateway_ref     VARCHAR(200) COMMENT 'Provider reference ID',
    status          ENUM('Pending','Sent','Delivered','Failed') DEFAULT 'Pending',
    sent_at         TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    delivered_at    DATETIME,
    error_code      VARCHAR(50),
    error_message   TEXT,
    FOREIGN KEY (campaign_id) REFERENCES sms_campaigns(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id)
) COMMENT='SMS delivery tracking';

CREATE TABLE messages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id       INT UNSIGNED  NOT NULL,
    receiver_id     INT UNSIGNED  NOT NULL,
    subject         VARCHAR(300),
    body            TEXT          NOT NULL,
    is_read         TINYINT(1)    DEFAULT 0,
    read_at         DATETIME,
    sent_at         TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id)   REFERENCES employees(id),
    FOREIGN KEY (receiver_id) REFERENCES employees(id)
) COMMENT='Internal employee messaging';

-- ================================================================
-- 19. DISCIPLINE MANAGEMENT
-- ================================================================

CREATE TABLE discipline_types (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(200)  NOT NULL COMMENT 'Warning, Suspension',
    severity        TINYINT       DEFAULT 1 COMMENT '1=Low to 5=High',
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Disciplinary action types';

CREATE TABLE disciplinary_actions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    type_id         INT UNSIGNED  NOT NULL,
    incident_date   DATE,
    action_date     DATE          NOT NULL,
    description     TEXT          NOT NULL,
    action_taken    TEXT,
    issued_by       INT UNSIGNED,
    response_due    DATE COMMENT 'Employee response deadline',
    employee_response TEXT,
    responded_at    DATETIME,
    status          ENUM('Issued','Responded','Closed','Escalated') DEFAULT 'Issued',
    attachment_path VARCHAR(500),
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (type_id)     REFERENCES discipline_types(id),
    FOREIGN KEY (issued_by)   REFERENCES employees(id) ON DELETE SET NULL
) COMMENT='Employee disciplinary records';

-- ================================================================
-- 20. GRIEVANCE MANAGEMENT
-- ================================================================

CREATE TABLE grievance_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(200)  NOT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Complaint categories';

CREATE TABLE grievances (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    category_id     INT UNSIGNED,
    ticket_no       VARCHAR(30)   UNIQUE,
    subject         VARCHAR(500)  NOT NULL,
    description     TEXT          NOT NULL,
    against_emp_id  INT UNSIGNED COMMENT 'Employee being complained about',
    attachment_path VARCHAR(500),
    is_anonymous    TINYINT(1)    DEFAULT 0,
    severity        ENUM('Low','Medium','High','Critical') DEFAULT 'Medium',
    status          ENUM('Submitted','Acknowledged','Under Investigation','Resolved','Closed','Rejected') DEFAULT 'Submitted',
    assigned_to     INT UNSIGNED COMMENT 'HR person handling',
    resolution      TEXT,
    resolved_at     DATETIME,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id)    REFERENCES employees(id),
    FOREIGN KEY (category_id)    REFERENCES grievance_categories(id),
    FOREIGN KEY (against_emp_id) REFERENCES employees(id),
    FOREIGN KEY (assigned_to)    REFERENCES employees(id)
) COMMENT='Employee complaint/grievance tickets';

-- ================================================================
-- 21. OVERTIME MANAGEMENT
-- ================================================================

CREATE TABLE overtime_policies (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    name            VARCHAR(200),
    weekday_rate    DECIMAL(4,2)  DEFAULT 1.5 COMMENT 'OT multiplier',
    weekend_rate    DECIMAL(4,2)  DEFAULT 2.0,
    holiday_rate    DECIMAL(4,2)  DEFAULT 2.5,
    max_ot_hours_day DECIMAL(4,1) DEFAULT 4.0,
    min_ot_minutes  INT           DEFAULT 30,
    is_active       TINYINT(1)    DEFAULT 1,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) COMMENT='Overtime rate rules';

CREATE TABLE overtime_requests (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  NOT NULL,
    ot_date         DATE          NOT NULL,
    from_time       TIME          NOT NULL,
    to_time         TIME          NOT NULL,
    ot_minutes      INT           GENERATED ALWAYS AS (TIME_TO_SEC(TIMEDIFF(to_time, from_time)) / 60) STORED,
    reason          TEXT,
    status          ENUM('Pending','Approved','Rejected','Cancelled') DEFAULT 'Pending',
    approved_by     INT UNSIGNED,
    approved_at     DATETIME,
    payroll_id      INT UNSIGNED,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (approved_by) REFERENCES employees(id) ON DELETE SET NULL
) COMMENT='Overtime approval requests';

-- ================================================================
-- 22. SYSTEM CONFIGURATION
-- ================================================================

CREATE TABLE system_settings (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED,
    setting_key     VARCHAR(150)  NOT NULL,
    setting_value   TEXT          NOT NULL,
    data_type       ENUM('String','Integer','Decimal','Boolean','JSON') DEFAULT 'String',
    description     TEXT,
    is_encrypted    TINYINT(1)    DEFAULT 0,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_setting (company_id, setting_key)
) COMMENT='Key-value configuration store';

CREATE TABLE roles (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED,
    name            VARCHAR(150)  NOT NULL,
    description     TEXT,
    is_system       TINYINT(1)    DEFAULT 0 COMMENT 'System role - not deletable',
    is_active       TINYINT(1)    DEFAULT 1
) COMMENT='User role definitions';

CREATE TABLE modules_list (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)  UNIQUE NOT NULL,
    display_name    VARCHAR(150),
    parent_module   VARCHAR(100)
) COMMENT='System module registry';

CREATE TABLE permissions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_id       INT UNSIGNED  NOT NULL,
    action          VARCHAR(50)   NOT NULL COMMENT 'view, create, edit, delete',
    description     TEXT,
    UNIQUE KEY uq_perm (module_id, action),
    FOREIGN KEY (module_id) REFERENCES modules_list(id)
) COMMENT='Action permissions per module';

CREATE TABLE role_permissions (
    role_id         INT UNSIGNED  NOT NULL,
    permission_id   INT UNSIGNED  NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id)       REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) COMMENT='Role to permission mapping';

CREATE TABLE user_accounts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED  UNIQUE,
    username        VARCHAR(100)  UNIQUE NOT NULL,
    email           VARCHAR(200)  UNIQUE,
    password_hash   VARCHAR(255)  NOT NULL,
    role_id         INT UNSIGNED  NOT NULL,
    is_active       TINYINT(1)    DEFAULT 1,
    must_change_pwd TINYINT(1)    DEFAULT 1,
    last_login      DATETIME,
    login_count     INT           DEFAULT 0,
    failed_attempts INT           DEFAULT 0,
    locked_until    DATETIME,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (role_id)     REFERENCES roles(id)
) COMMENT='System login accounts';

-- ================================================================
-- 23. AUDIT LOG
-- ================================================================

CREATE TABLE audit_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED COMMENT 'Who performed action',
    employee_id     INT UNSIGNED,
    action          VARCHAR(100)  NOT NULL COMMENT 'INSERT, UPDATE, DELETE',
    module          VARCHAR(100),
    table_name      VARCHAR(100),
    record_id       BIGINT UNSIGNED,
    old_values      JSON COMMENT 'Previous values',
    new_values      JSON COMMENT 'New values',
    description     TEXT,
    ip_address      VARCHAR(45),
    user_agent      VARCHAR(500),
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_user  (user_id),
    INDEX idx_audit_table (table_name, record_id),
    INDEX idx_audit_date  (created_at)
) COMMENT='Complete audit trail of all changes';

-- ================================================================
-- 24. ANALYTICS VIEWS
-- ================================================================

CREATE VIEW vw_employees AS
SELECT
    e.id, e.employee_code, e.full_name, e.phone, e.email,
    e.date_of_birth, e.gender, e.blood_group, e.nid_number,
    b.name  AS branch,
    d.name  AS department,
    dg.title AS designation,
    sg.name AS grade,
    m.full_name AS manager,
    e.employment_type, e.joining_date, e.last_working_day, e.status,
    e.basic_salary, e.payment_method, e.bank_account,
    TIMESTAMPDIFF(YEAR,  e.joining_date, COALESCE(e.last_working_day, CURDATE())) AS service_years,
    MOD(TIMESTAMPDIFF(MONTH, e.joining_date, COALESCE(e.last_working_day, CURDATE())), 12) AS service_months,
    CONCAT(
        TIMESTAMPDIFF(YEAR, e.joining_date, COALESCE(e.last_working_day, CURDATE())), 'Y ',
        MOD(TIMESTAMPDIFF(MONTH, e.joining_date, COALESCE(e.last_working_day, CURDATE())), 12), 'M ',
        MOD(DATEDIFF(COALESCE(e.last_working_day, CURDATE()), e.joining_date), 30), 'D'
    ) AS service_period
FROM employees e
LEFT JOIN branches     b  ON b.id  = e.branch_id
LEFT JOIN departments  d  ON d.id  = e.department_id
LEFT JOIN designations dg ON dg.id = e.designation_id
LEFT JOIN salary_grades sg ON sg.id = e.grade_id
LEFT JOIN employees    m  ON m.id  = e.reports_to
COMMENT='Complete employee master view with computed fields';

CREATE VIEW vw_birthday_alerts AS
SELECT
    e.id, e.employee_code, e.full_name, e.phone, e.email,
    d.name AS department, dg.title AS designation,
    e.date_of_birth,
    YEAR(CURDATE()) - YEAR(e.date_of_birth) AS age_today,
    CASE
        WHEN DATE_FORMAT(e.date_of_birth, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d') THEN 0
        ELSE DATEDIFF(
            DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(e.date_of_birth, '%m-%d'))),
            CURDATE()
        )
    END AS days_until_birthday
FROM employees e
JOIN departments  d  ON d.id  = e.department_id
LEFT JOIN designations dg ON dg.id = e.designation_id
WHERE e.status = 'Active'
  AND e.date_of_birth IS NOT NULL
  AND (
      DATE_FORMAT(e.date_of_birth, '%m-%d') BETWEEN DATE_FORMAT(CURDATE(), '%m-%d')
      AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%m-%d')
  )
COMMENT='Upcoming birthdays in next 7 days';

CREATE VIEW vw_headcount_summary AS
SELECT
    d.name AS department, dg.title AS designation,
    COUNT(*) AS headcount,
    SUM(CASE WHEN e.gender = 'Male'   THEN 1 ELSE 0 END) AS male_count,
    SUM(CASE WHEN e.gender = 'Female' THEN 1 ELSE 0 END) AS female_count,
    AVG(TIMESTAMPDIFF(YEAR, e.joining_date, CURDATE())) AS avg_tenure_years,
    AVG(e.basic_salary) AS avg_basic_salary
FROM employees e
JOIN departments  d  ON d.id  = e.department_id
LEFT JOIN designations dg ON dg.id = e.designation_id
WHERE e.status = 'Active'
GROUP BY d.name, dg.title
COMMENT='Department-wise employee count and statistics';

-- ================================================================
-- 25. SEED DATA (Initial System Setup)
-- ================================================================

INSERT INTO companies (name, legal_name, industry, phone, email, address, city) VALUES
('Acme Corporation Ltd.', 'Acme Corporation Limited', 'Manufacturing', '+8801700000000', 'hr@acme.com.bd', 'House 12, Road 5, Gulshan-2', 'Dhaka');

INSERT INTO branches (company_id, code, name, address, city, is_head_office) VALUES
(1, 'HO-DHA', 'Head Office – Dhaka', 'House 12, Road 5, Gulshan-2', 'Dhaka', 1),
(1, 'BR-CTG', 'Branch – Chattogram', 'Agrabad, Port Area', 'Chattogram', 0);

INSERT INTO fiscal_years (company_id, label, start_date, end_date, is_current) VALUES
(1, '2024-2025', '2024-07-01', '2025-06-30', 0),
(1, '2025-2026', '2025-07-01', '2026-06-30', 1);

INSERT INTO salary_components (company_id, code, name, type, calculation, default_value, is_taxable, sort_order) VALUES
(1, 'BASIC', 'Basic Salary', 'Earning', 'Fixed', 0, 1, 1),
(1, 'HRA', 'House Rent Allowance', 'Earning', 'Percentage of Basic', 50, 0, 2),
(1, 'PF_EMP', 'Provident Fund (Employee)', 'Deduction', 'Percentage of Basic', 10, 0, 10),
(1, 'TAX', 'Income Tax', 'Deduction', 'Percentage of Gross', 0, 0, 11);

INSERT INTO roles (company_id, name, description, is_system) VALUES
(NULL, 'Super Admin', 'Full system access', 1),
(1, 'HR Manager', 'HR module full access', 0),
(1, 'Employee', 'Self-service portal access', 0);

INSERT INTO modules_list (name, display_name, parent_module) VALUES
('employee', 'Employee Management', NULL),
('attendance', 'Attendance', NULL),
('leave', 'Leave Management', NULL),
('payroll', 'Payroll', NULL);

INSERT INTO leave_types (company_id, code, name, days_per_year, is_paid, carry_forward) VALUES
(1, 'CL', 'Casual Leave', 10, 1, 0),
(1, 'SL', 'Sick Leave', 14, 1, 0),
(1, 'AL', 'Annual Leave', 20, 1, 1);

INSERT INTO shifts (company_id, name, code, start_time, end_time, break_minutes, work_hours) VALUES
(1, 'Morning Shift', 'SH-MOR', '09:00:00', '18:00:00', 60, 8.0);

-- ================================================================
-- END OF HRM PROFESSIONAL DATABASE SCHEMA v2.0
-- ================================================================
-- TOTAL: 83 TABLES | 15 VIEWS | 24 MODULES
-- ================================================================

SET FOREIGN_KEY_CHECKS = 1;

-- ================================================================
-- QUICK REFERENCE COMMANDS
-- ================================================================
-- 
-- 1. CREATE DATABASE:
--    mysql -u root -p < hrm_professional.sql
--
-- 2. BACKUP DATABASE:
--    mysqldump -u root -p hrm_pro > hrm_backup.sql
--
-- 3. RESTORE DATABASE:
--    mysql -u root -p hrm_pro < hrm_backup.sql
--
-- 4. CHECK TABLE COUNT:
--    SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'hrm_pro';
--
-- 5. VIEW ALL EMPLOYEES:
--    SELECT * FROM vw_employees;
--
-- 6. CHECK UPCOMING BIRTHDAYS:
--    SELECT * FROM vw_birthday_alerts;
--
-- 7. RUN PAYROLL FOR A MONTH:
--    INSERT INTO payroll_runs (company_id, fiscal_year_id, month_year, status) 
--    VALUES (1, 2, '2025-11-01', 'Draft');
--
-- ================================================================