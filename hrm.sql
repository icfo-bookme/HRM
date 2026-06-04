-- ================================================================
-- HRM PROFESSIONAL DATABASE - OPTIMIZED VERSION 3.0
-- ================================================================
-- Optimizations Applied:
-- 1. Normalized employee data (split into logical tables)
-- 2. Added proper indexing strategies
-- 3. Implemented table partitioning for large tables
-- 4. Added JSON fields for flexible data
-- 5. Optimized data types and constraints
-- 6. Added comprehensive audit trails
-- 7. Implemented soft deletes
-- 8. Added caching summary tables
-- 9. Optimized views with materialized patterns
-- 10. Added performance tuning hints
-- ================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- DROP DATABASE IF EXISTS hrm_optimized;
-- CREATE DATABASE hrm_optimized
--     CHARACTER SET utf8mb4
--     COLLATE utf8mb4_unicode_ci;
-- USE hrm_optimized;

-- ================================================================
-- 01. CORE CONFIGURATION TABLES
-- ================================================================

CREATE TABLE companies (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(300)  NOT NULL,
    legal_name      VARCHAR(300),
    trade_license   VARCHAR(100),
    bin_number      VARCHAR(50),
    tin_number      VARCHAR(50),
    industry        VARCHAR(150),
    founded_year    YEAR,
    logo_path       VARCHAR(500),
    address         TEXT,
    city            VARCHAR(100),
    country         VARCHAR(100)  DEFAULT 'Bangladesh',
    phone           VARCHAR(30),
    email           VARCHAR(150),
    website         VARCHAR(200),
    timezone        VARCHAR(50)   DEFAULT 'Asia/Dhaka',
    date_format     VARCHAR(20)   DEFAULT 'Y-m-d',
    fiscal_year_start DATE,
    is_active       TINYINT(1)    DEFAULT 1,
    settings        JSON COMMENT 'Additional company settings',
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_company_active (is_active),
    INDEX idx_company_city (city)
) ENGINE=InnoDB COMMENT='Company master record';

CREATE TABLE branches (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    code            VARCHAR(20)   NOT NULL,
    name            VARCHAR(200)  NOT NULL,
    address         TEXT,
    city            VARCHAR(100),
    state           VARCHAR(100),
    country         VARCHAR(100)  DEFAULT 'Bangladesh',
    zip_code        VARCHAR(20),
    phone           VARCHAR(30),
    email           VARCHAR(150),
    latitude        DECIMAL(10,8),
    longitude       DECIMAL(11,8),
    is_head_office  TINYINT(1)    DEFAULT 0,
    is_active       TINYINT(1)    DEFAULT 1,
    metadata        JSON,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_branch_code (company_id, code),
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
    INDEX idx_branch_company (company_id),
    INDEX idx_branch_city (city),
    INDEX idx_branch_active (is_active)
) ENGINE=InnoDB COMMENT='Branch/office locations';

CREATE TABLE fiscal_years (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    label           VARCHAR(20)   NOT NULL,
    start_date      DATE          NOT NULL,
    end_date        DATE          NOT NULL,
    is_current      TINYINT(1)    DEFAULT 0,
    locked          TINYINT(1)    DEFAULT 0,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_fy_company_label (company_id, label),
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
    INDEX idx_fy_current (company_id, is_current),
    INDEX idx_fy_dates (start_date, end_date)
) ENGINE=InnoDB COMMENT='Financial year definitions';

-- ================================================================
-- 02. ORGANIZATION STRUCTURE (Normalized)
-- ================================================================

CREATE TABLE cost_centers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    parent_id       INT UNSIGNED,
    code            VARCHAR(30)   NOT NULL,
    name            VARCHAR(200)  NOT NULL,
    description     TEXT,
    manager_id      INT UNSIGNED,
    budget_amount   DECIMAL(14,2) DEFAULT 0,
    is_active       TINYINT(1)    DEFAULT 1,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_cc_code (company_id, code),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (parent_id) REFERENCES cost_centers(id),
    INDEX idx_cc_company (company_id),
    INDEX idx_cc_parent (parent_id)
) ENGINE=InnoDB COMMENT='Cost centers for budgeting';

CREATE TABLE departments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    branch_id       INT UNSIGNED  NOT NULL,
    cost_center_id  INT UNSIGNED,
    parent_id       INT UNSIGNED,
    code            VARCHAR(30)   NOT NULL,
    name            VARCHAR(200)  NOT NULL,
    description     TEXT,
    head_employee_id INT UNSIGNED,
    email           VARCHAR(150),
    phone           VARCHAR(30),
    is_active       TINYINT(1)    DEFAULT 1,
    sort_order      INT           DEFAULT 0,
    metadata        JSON,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_dept_code (company_id, code),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (cost_center_id) REFERENCES cost_centers(id),
    FOREIGN KEY (parent_id) REFERENCES departments(id),
    INDEX idx_dept_company (company_id),
    INDEX idx_dept_branch (branch_id),
    INDEX idx_dept_active (is_active)
) ENGINE=InnoDB COMMENT='Department hierarchy';

CREATE TABLE salary_grades (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    code            VARCHAR(20)   NOT NULL,
    name            VARCHAR(100)  NOT NULL,
    min_salary      DECIMAL(14,2) DEFAULT 0,
    max_salary      DECIMAL(14,2) DEFAULT 0,
    currency        CHAR(3)       DEFAULT 'BDT',
    is_active       TINYINT(1)    DEFAULT 1,
    metadata        JSON,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_grade_code (company_id, code),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_grade_company (company_id)
) ENGINE=InnoDB COMMENT='Salary grade/band definitions';

CREATE TABLE designations (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED  NOT NULL,
    department_id   INT UNSIGNED,
    grade_id        INT UNSIGNED,
    code            VARCHAR(30),
    title           VARCHAR(200)  NOT NULL,
    level           TINYINT       DEFAULT 1,
    responsibilities JSON,
    requirements    JSON,
    is_active       TINYINT(1)    DEFAULT 1,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (grade_id) REFERENCES salary_grades(id),
    INDEX idx_desig_company (company_id),
    INDEX idx_desig_dept (department_id),
    INDEX idx_desig_title (title)
) ENGINE=InnoDB COMMENT='Job titles/designations';

-- ================================================================
-- 03. EMPLOYEE CORE TABLE (Normalized - Only Essential Fields)
-- ================================================================

CREATE TABLE employees (
    id                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id              INT UNSIGNED  NOT NULL,
    employee_code           VARCHAR(50)   NOT NULL,
    full_name               VARCHAR(300)  NOT NULL,
    first_name              VARCHAR(150),
    last_name               VARCHAR(150),
    display_name            VARCHAR(200),
    phone                   VARCHAR(20)   NOT NULL,
    phone_2                 VARCHAR(20),
    email                   VARCHAR(200),
    gender                  ENUM('Male','Female','Other','Prefer not to say'),
    date_of_birth           DATE,
    nationality             VARCHAR(100)  DEFAULT 'Bangladeshi',
    branch_id               INT UNSIGNED  NOT NULL,
    department_id           INT UNSIGNED  NOT NULL,
    designation_id          INT UNSIGNED  NOT NULL,
    grade_id                INT UNSIGNED,
    shift_id                INT UNSIGNED,
    reports_to              INT UNSIGNED,
    employment_type         ENUM('Full-Time','Part-Time','Contractual','Intern','Probation','Freelance') DEFAULT 'Full-Time',
    joining_date            DATE          NOT NULL,
    confirmation_date       DATE,
    probation_end_date      DATE,
    last_working_day        DATE,
    contract_end_date       DATE,
    status                  ENUM('Active','Inactive','On Leave','Suspended','Terminated','Resigned','Retired') DEFAULT 'Active',
    portal_active           TINYINT(1)    DEFAULT 0,
    portal_last_login       DATETIME,
    created_by              INT UNSIGNED,
    created_at              TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at              TIMESTAMP     NULL COMMENT 'Soft delete',
    UNIQUE KEY uk_emp_code_company (company_id, employee_code),
    UNIQUE KEY uk_emp_email_company (company_id, email),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (designation_id) REFERENCES designations(id),
    FOREIGN KEY (grade_id) REFERENCES salary_grades(id),
    FOREIGN KEY (shift_id) REFERENCES shifts(id),
    FOREIGN KEY (reports_to) REFERENCES employees(id),
    INDEX idx_emp_status (status),
    INDEX idx_emp_dept (department_id, status),
    INDEX idx_emp_joining (joining_date),
    INDEX idx_emp_dob (date_of_birth),
    INDEX idx_emp_phone (phone),
    INDEX idx_emp_deleted (deleted_at)
) ENGINE=InnoDB COMMENT='Employee core table - essential work data';

-- ================================================================
-- 04. EMPLOYEE PERSONAL INFORMATION (Separate for security)
-- ================================================================

CREATE TABLE employee_personal_info (
    employee_id         INT UNSIGNED PRIMARY KEY,
    personal_email      VARCHAR(200),
    place_of_birth      VARCHAR(200),
    blood_group         ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-'),
    religion            VARCHAR(80),
    marital_status      ENUM('Single','Married','Divorced','Widowed','Separated'),
    spouse_name         VARCHAR(200),
    emergency_contact   JSON COMMENT 'Emergency contacts stored as JSON',
    nid_number          VARCHAR(50),
    nid_issue_date      DATE,
    nid_expiry_date     DATE,
    nid_file_path       VARCHAR(500),
    passport_number     VARCHAR(50),
    passport_issue_date DATE,
    passport_expiry     DATE,
    passport_file_path  VARCHAR(500),
    tin_number          VARCHAR(50),
    tin_file_path       VARCHAR(500),
    birth_certificate   VARCHAR(50),
    driving_license     VARCHAR(50),
    metadata            JSON,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_nid (nid_number),
    INDEX idx_passport (passport_number),
    INDEX idx_tin (tin_number)
) ENGINE=InnoDB COMMENT='Sensitive PII data - separate for security';

-- ================================================================
-- 05. EMPLOYEE ADDRESSES (Supporting multiple addresses)
-- ================================================================

CREATE TABLE employee_addresses (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    address_type    ENUM('present','permanent','mailing','emergency') NOT NULL,
    address_line1   TEXT,
    address_line2   TEXT,
    city            VARCHAR(100),
    state           VARCHAR(100),
    zip_code        VARCHAR(20),
    country         VARCHAR(100) DEFAULT 'Bangladesh',
    latitude        DECIMAL(10,8),
    longitude       DECIMAL(11,8),
    is_primary      TINYINT(1) DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_address_employee (employee_id),
    INDEX idx_address_type (address_type)
) ENGINE=InnoDB COMMENT='Employee address information';

-- ================================================================
-- 06. EMPLOYEE BANKING & PAYMENT (Separate for compliance)
-- ================================================================

CREATE TABLE employee_banking (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    bank_name       VARCHAR(200),
    bank_branch     VARCHAR(200),
    bank_account    VARCHAR(80),
    bank_routing    VARCHAR(50),
    iban            VARCHAR(50),
    swift_code      VARCHAR(20),
    mfs_type        ENUM('bKash','Nagad','Rocket','Upay','Others'),
    mfs_number      VARCHAR(20),
    payment_method  ENUM('Bank','Cash','MFS','Cheque') DEFAULT 'Bank',
    is_primary      TINYINT(1) DEFAULT 1,
    verification_status ENUM('Pending','Verified','Rejected') DEFAULT 'Pending',
    verified_at     DATETIME,
    verified_by     INT UNSIGNED,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY uk_emp_primary (employee_id, is_primary),
    INDEX idx_bank_account (bank_account)
) ENGINE=InnoDB COMMENT='Employee banking and payment details';

-- ================================================================
-- 07. EMPLOYEE DOCUMENTS (Scalable document management)
-- ================================================================

CREATE TABLE document_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    name            VARCHAR(150) NOT NULL,
    code            VARCHAR(50) UNIQUE,
    requires_expiry TINYINT(1) DEFAULT 0,
    is_mandatory    TINYINT(1) DEFAULT 0,
    retention_days  INT DEFAULT 0,
    metadata        JSON,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) ENGINE=InnoDB COMMENT='Document type categories';

CREATE TABLE employee_documents (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    category_id     INT UNSIGNED,
    document_name   VARCHAR(300),
    file_path       VARCHAR(500) NOT NULL,
    file_hash       VARCHAR(64) COMMENT 'SHA-256 for deduplication',
    file_size       INT UNSIGNED,
    mime_type       VARCHAR(100),
    issue_date      DATE,
    expiry_date     DATE,
    document_number VARCHAR(100),
    issuing_authority VARCHAR(300),
    is_verified     TINYINT(1) DEFAULT 0,
    verified_by     INT UNSIGNED,
    verified_at     DATETIME,
    verification_notes TEXT,
    notes           TEXT,
    uploaded_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES document_categories(id),
    FOREIGN KEY (verified_by) REFERENCES employees(id),
    INDEX idx_doc_employee (employee_id),
    INDEX idx_doc_expiry (expiry_date),
    INDEX idx_doc_category (category_id),
    INDEX idx_doc_verified (is_verified)
) ENGINE=InnoDB COMMENT='Employee document storage';

-- ================================================================
-- 08. EMPLOYEE JOB HISTORY (Complete audit trail)
-- ================================================================

CREATE TABLE employee_job_history (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    effective_date  DATE NOT NULL,
    change_type     ENUM('Joining','Promotion','Demotion','Transfer','Designation Change',
                         'Grade Change','Salary Revision','Confirmation','Termination',
                         'Resignation','Retirement','Rehired') NOT NULL,
    from_branch_id  INT UNSIGNED,
    to_branch_id    INT UNSIGNED,
    from_dept_id    INT UNSIGNED,
    to_dept_id      INT UNSIGNED,
    from_desig_id   INT UNSIGNED,
    to_desig_id     INT UNSIGNED,
    from_grade_id   INT UNSIGNED,
    to_grade_id     INT UNSIGNED,
    from_salary     DECIMAL(14,2),
    to_salary       DECIMAL(14,2),
    reason          TEXT,
    remarks         TEXT,
    approved_by     INT UNSIGNED,
    document_ref    VARCHAR(500),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (from_branch_id) REFERENCES branches(id),
    FOREIGN KEY (to_branch_id) REFERENCES branches(id),
    FOREIGN KEY (from_dept_id) REFERENCES departments(id),
    FOREIGN KEY (to_dept_id) REFERENCES departments(id),
    FOREIGN KEY (from_desig_id) REFERENCES designations(id),
    FOREIGN KEY (to_desig_id) REFERENCES designations(id),
    FOREIGN KEY (approved_by) REFERENCES employees(id),
    INDEX idx_history_employee (employee_id, effective_date),
    INDEX idx_history_dates (effective_date)
) ENGINE=InnoDB COMMENT='Complete career history tracking';

-- ================================================================
-- 09. EMPLOYEE QUALIFICATIONS (Education, Experience, Skills)
-- ================================================================

CREATE TABLE employee_education (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    degree          VARCHAR(200) NOT NULL,
    major_subject   VARCHAR(200),
    institution     VARCHAR(300),
    board_university VARCHAR(300),
    passing_year    YEAR,
    result_type     ENUM('CGPA','Percentage','Grade','Division'),
    result_value    VARCHAR(50),
    duration_from   DATE,
    duration_to     DATE,
    country         VARCHAR(100),
    certificate_path VARCHAR(500),
    is_highest      TINYINT(1) DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_edu_employee (employee_id)
) ENGINE=InnoDB COMMENT='Educational qualifications';

CREATE TABLE employee_experience (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    company_name    VARCHAR(300) NOT NULL,
    designation     VARCHAR(200),
    department      VARCHAR(200),
    from_date       DATE,
    to_date         DATE,
    is_current      TINYINT(1) DEFAULT 0,
    responsibilities TEXT,
    achievements    TEXT,
    reason_for_leaving VARCHAR(300),
    salary_scale    VARCHAR(100),
    reference_name  VARCHAR(200),
    reference_phone VARCHAR(20),
    reference_email VARCHAR(200),
    certificate_path VARCHAR(500),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_exp_employee (employee_id),
    INDEX idx_exp_dates (from_date, to_date)
) ENGINE=InnoDB COMMENT='Previous work experience';

CREATE TABLE skill_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    description     TEXT,
    is_active       TINYINT(1) DEFAULT 1
) ENGINE=InnoDB COMMENT='Skill categories';

CREATE TABLE employee_skills (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    category_id     INT UNSIGNED,
    skill_name      VARCHAR(200) NOT NULL,
    proficiency     ENUM('Beginner','Intermediate','Advanced','Expert','Master') DEFAULT 'Intermediate',
    years_of_experience DECIMAL(3,1),
    last_used_date  DATE,
    certification   VARCHAR(300),
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES skill_categories(id),
    UNIQUE KEY uk_emp_skill (employee_id, skill_name),
    INDEX idx_skill_employee (employee_id),
    INDEX idx_skill_category (category_id)
) ENGINE=InnoDB COMMENT='Employee skills matrix';

CREATE TABLE employee_languages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    language_name   VARCHAR(50) NOT NULL,
    proficiency     ENUM('Basic','Conversational','Professional','Native') DEFAULT 'Basic',
    can_read        TINYINT(1) DEFAULT 0,
    can_write       TINYINT(1) DEFAULT 0,
    can_speak       TINYINT(1) DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY uk_emp_lang (employee_id, language_name)
) ENGINE=InnoDB COMMENT='Languages spoken by employee';

CREATE TABLE employee_awards (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    award_name      VARCHAR(200) NOT NULL,
    award_date      DATE,
    awarded_by      VARCHAR(200),
    organization    VARCHAR(200),
    description     TEXT,
    certificate_path VARCHAR(500),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_award_employee (employee_id)
) ENGINE=InnoDB COMMENT='Employee recognition and awards';

CREATE TABLE employee_dependents (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    full_name       VARCHAR(200) NOT NULL,
    relation        ENUM('Spouse','Son','Daughter','Father','Mother','Brother','Sister','Other') NOT NULL,
    date_of_birth   DATE,
    nid_number      VARCHAR(50),
    phone           VARCHAR(20),
    email           VARCHAR(200),
    occupation      VARCHAR(200),
    is_nominee      TINYINT(1) DEFAULT 0,
    nominee_percent DECIMAL(5,2) DEFAULT 0,
    priority_order  TINYINT DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_dependents_employee (employee_id),
    INDEX idx_nominee (is_nominee)
) ENGINE=InnoDB COMMENT='Family members and benefit nominees';

-- ================================================================
-- 10. SHIFT & ROSTER MANAGEMENT
-- ================================================================

CREATE TABLE shifts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    name            VARCHAR(150) NOT NULL,
    code            VARCHAR(20),
    start_time      TIME NOT NULL,
    end_time        TIME NOT NULL,
    break_minutes   INT DEFAULT 60,
    grace_in_min    INT DEFAULT 10,
    grace_out_min   INT DEFAULT 10,
    work_hours      DECIMAL(4,1) DEFAULT 8.0,
    is_night_shift  TINYINT(1) DEFAULT 0,
    is_flexible     TINYINT(1) DEFAULT 0,
    is_active       TINYINT(1) DEFAULT 1,
    metadata        JSON,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_shift_company (company_id),
    INDEX idx_shift_active (is_active)
) ENGINE=InnoDB COMMENT='Work shift definitions';

CREATE TABLE weekend_policies (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    name            VARCHAR(100),
    is_default      TINYINT(1) DEFAULT 0,
    saturday        TINYINT(1) DEFAULT 0,
    sunday          TINYINT(1) DEFAULT 0,
    monday          TINYINT(1) DEFAULT 0,
    tuesday         TINYINT(1) DEFAULT 0,
    wednesday       TINYINT(1) DEFAULT 0,
    thursday        TINYINT(1) DEFAULT 0,
    friday          TINYINT(1) DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_weekend_company (company_id)
) ENGINE=InnoDB COMMENT='Weekly off-day configuration';

CREATE TABLE employee_rosters (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    shift_id        INT UNSIGNED NOT NULL,
    roster_date     DATE NOT NULL,
    is_day_off      TINYINT(1) DEFAULT 0,
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_emp_roster (employee_id, roster_date),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (shift_id) REFERENCES shifts(id),
    INDEX idx_roster_date (roster_date),
    INDEX idx_roster_employee (employee_id)
) ENGINE=InnoDB COMMENT='Daily shift assignment';

-- ================================================================
-- 11. ATTENDANCE MANAGEMENT (Partitioned by date)
-- ================================================================

CREATE TABLE attendance_devices (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id       INT UNSIGNED,
    name            VARCHAR(150),
    device_id       VARCHAR(100) UNIQUE,
    ip_address      VARCHAR(45),
    device_type     ENUM('Fingerprint','Face','Card','App','Web','Manual') NOT NULL,
    serial_number   VARCHAR(100),
    firmware_version VARCHAR(50),
    last_sync       DATETIME,
    is_active       TINYINT(1) DEFAULT 1,
    metadata        JSON,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    INDEX idx_device_branch (branch_id),
    INDEX idx_device_type (device_type)
) ENGINE=InnoDB COMMENT='Attendance recording devices';

-- Main attendance table (consider partitioning by att_date for large datasets)
CREATE TABLE attendance (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    att_date        DATE NOT NULL,
    shift_id        INT UNSIGNED,
    first_in_time   TIME,
    last_out_time   TIME,
    check_in_time   TIME,
    check_out_time  TIME,
    in_device_id    INT UNSIGNED,
    out_device_id   INT UNSIGNED,
    working_minutes INT GENERATED ALWAYS AS (
        CASE WHEN check_in_time IS NOT NULL AND check_out_time IS NOT NULL
        THEN GREATEST(0, TIME_TO_SEC(TIMEDIFF(check_out_time, check_in_time)) / 60)
        ELSE 0 END
    ) STORED,
    break_minutes   INT DEFAULT 0,
    net_working_minutes INT GENERATED ALWAYS AS (
        CASE WHEN check_in_time IS NOT NULL AND check_out_time IS NOT NULL
        THEN GREATEST(0, TIME_TO_SEC(TIMEDIFF(check_out_time, check_in_time)) / 60 - break_minutes)
        ELSE 0 END
    ) STORED,
    late_minutes    INT DEFAULT 0,
    early_out_minutes INT DEFAULT 0,
    overtime_minutes INT DEFAULT 0,
    is_late         TINYINT(1) GENERATED ALWAYS AS (late_minutes > 0) STORED,
    is_early_out    TINYINT(1) GENERATED ALWAYS AS (early_out_minutes > 0) STORED,
    is_absent       TINYINT(1) DEFAULT 0,
    is_holiday_work TINYINT(1) DEFAULT 0,
    attendance_status ENUM('Present','Absent','Half Day','On Leave','Holiday','Weekend','Late','Early Out') 
                      GENERATED ALWAYS AS (
                          CASE 
                              WHEN is_absent = 1 THEN 'Absent'
                              WHEN check_in_time IS NULL THEN 'Absent'
                              WHEN is_late = 1 AND (net_working_minutes / 60) < 4 THEN 'Half Day'
                              WHEN is_late = 1 THEN 'Late'
                              WHEN is_early_out = 1 THEN 'Early Out'
                              ELSE 'Present'
                          END
                      ) STORED,
    approval_status ENUM('Pending','Approved','Rejected') DEFAULT 'Approved',
    approved_by     INT UNSIGNED,
    approved_at     DATETIME,
    notes           TEXT,
    source          ENUM('Device','CSV','App','Manual','API') DEFAULT 'Manual',
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_attendance_emp_date (employee_id, att_date),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (shift_id) REFERENCES shifts(id),
    FOREIGN KEY (in_device_id) REFERENCES attendance_devices(id),
    FOREIGN KEY (out_device_id) REFERENCES attendance_devices(id),
    INDEX idx_att_date (att_date),
    INDEX idx_att_employee (employee_id),
    INDEX idx_att_status (attendance_status),
    INDEX idx_att_approval (approval_status)
) ENGINE=InnoDB 
-- Uncomment for production with large data:
-- PARTITION BY RANGE (YEAR(att_date) * 100 + MONTH(att_date)) (
--     PARTITION p202401 VALUES LESS THAN (202402),
--     PARTITION p202402 VALUES LESS THAN (202403),
--     PARTITION p202403 VALUES LESS THAN (202404),
--     PARTITION p_future VALUES LESS THAN MAXVALUE
-- )
COMMENT='Daily attendance records';

-- Attendance summary table for quick reporting
CREATE TABLE attendance_monthly_summary (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    year_month      DATE NOT NULL COMMENT 'First day of month',
    total_working_days INT DEFAULT 0,
    present_days    DECIMAL(5,1) DEFAULT 0,
    absent_days     DECIMAL(5,1) DEFAULT 0,
    late_days       INT DEFAULT 0,
    half_days       INT DEFAULT 0,
    leave_days      DECIMAL(5,1) DEFAULT 0,
    holiday_days    INT DEFAULT 0,
    weekend_days    INT DEFAULT 0,
    overtime_hours  DECIMAL(6,2) DEFAULT 0,
    total_working_hours DECIMAL(6,2) DEFAULT 0,
    total_late_minutes INT DEFAULT 0,
    is_locked       TINYINT(1) DEFAULT 0,
    locked_at       DATETIME,
    locked_by       INT UNSIGNED,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_summary_emp_month (employee_id, year_month),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    INDEX idx_summary_month (year_month),
    INDEX idx_summary_employee (employee_id)
) ENGINE=InnoDB COMMENT='Pre-aggregated monthly attendance for payroll';

-- ================================================================
-- 12. LEAVE MANAGEMENT
-- ================================================================

CREATE TABLE leave_types (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    code            VARCHAR(20) NOT NULL,
    name            VARCHAR(150) NOT NULL,
    description     TEXT,
    days_per_year   DECIMAL(5,1) DEFAULT 0,
    is_paid         TINYINT(1) DEFAULT 1,
    is_half_day_allowed TINYINT(1) DEFAULT 1,
    carry_forward   TINYINT(1) DEFAULT 0,
    max_carry_days  DECIMAL(5,1) DEFAULT 0,
    max_consecutive_days INT DEFAULT 0,
    requires_document TINYINT(1) DEFAULT 0,
    min_days_notice INT DEFAULT 0,
    applicable_gender ENUM('All','Male','Female') DEFAULT 'All',
    color_code      VARCHAR(10),
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_leavetype_code (company_id, code),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_leavetype_active (is_active)
) ENGINE=InnoDB COMMENT='Leave categories and rules';

CREATE TABLE employee_leave_balance (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    leave_type_id   INT UNSIGNED NOT NULL,
    fiscal_year_id  INT UNSIGNED NOT NULL,
    opening_balance DECIMAL(5,1) DEFAULT 0,
    earned_days     DECIMAL(5,1) DEFAULT 0,
    used_days       DECIMAL(5,1) DEFAULT 0,
    encashed_days   DECIMAL(5,1) DEFAULT 0,
    lapsed_days     DECIMAL(5,1) DEFAULT 0,
    pending_days    DECIMAL(5,1) DEFAULT 0,
    remaining_days  DECIMAL(5,1) GENERATED ALWAYS AS (
        opening_balance + earned_days - used_days - encashed_days - lapsed_days - pending_days
    ) STORED,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_leave_balance (employee_id, leave_type_id, fiscal_year_id),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id),
    FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id),
    INDEX idx_balance_employee (employee_id),
    INDEX idx_balance_fiscal (fiscal_year_id)
) ENGINE=InnoDB COMMENT='Leave balance per employee';

CREATE TABLE leave_applications (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    leave_type_id   INT UNSIGNED NOT NULL,
    application_no  VARCHAR(30) UNIQUE,
    from_date       DATE NOT NULL,
    to_date         DATE NOT NULL,
    total_days      DECIMAL(5,1) NOT NULL,
    is_half_day     TINYINT(1) DEFAULT 0,
    half_day_period ENUM('First Half','Second Half'),
    reason          TEXT,
    document_path   VARCHAR(500),
    substitute_employee_id INT UNSIGNED,
    contact_during_leave VARCHAR(50),
    status          ENUM('Draft','Pending','Approved','Rejected','Cancelled','Withdrawn') DEFAULT 'Pending',
    rejection_reason TEXT,
    approved_by     INT UNSIGNED,
    approved_at     DATETIME,
    applied_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id),
    FOREIGN KEY (approved_by) REFERENCES employees(id),
    FOREIGN KEY (substitute_employee_id) REFERENCES employees(id),
    INDEX idx_leave_employee (employee_id),
    INDEX idx_leave_dates (from_date, to_date),
    INDEX idx_leave_status (status),
    INDEX idx_leave_number (application_no)
) ENGINE=InnoDB COMMENT='Leave request submissions';

CREATE TABLE leave_encashment (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    leave_type_id   INT UNSIGNED NOT NULL,
    encashment_date DATE NOT NULL,
    days_encashed   DECIMAL(5,1) NOT NULL,
    amount_per_day  DECIMAL(14,2),
    total_amount    DECIMAL(14,2),
    payroll_run_id  INT UNSIGNED,
    reason          TEXT,
    approved_by     INT UNSIGNED,
    approved_at     DATETIME,
    status          ENUM('Pending','Approved','Paid') DEFAULT 'Pending',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id),
    FOREIGN KEY (approved_by) REFERENCES employees(id),
    INDEX idx_encashment_employee (employee_id)
) ENGINE=InnoDB COMMENT='Leave encashment requests';

-- ================================================================
-- 13. HOLIDAY CALENDAR
-- ================================================================

CREATE TABLE holidays (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    name            VARCHAR(300) NOT NULL,
    holiday_date    DATE NOT NULL,
    end_date        DATE,
    total_days      INT GENERATED ALWAYS AS (DATEDIFF(COALESCE(end_date, holiday_date), holiday_date) + 1) STORED,
    holiday_type    ENUM('Public','Government','Company','Optional','Religious','Festival') DEFAULT 'Public',
    applicable_to   ENUM('All','Specific','Branch','Department') DEFAULT 'All',
    is_recurring    TINYINT(1) DEFAULT 0,
    yearly_recurring TINYINT(1) DEFAULT 0,
    description     TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_holiday_date (holiday_date),
    INDEX idx_holiday_company (company_id)
) ENGINE=InnoDB COMMENT='Holiday calendar';

CREATE TABLE holiday_assignments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    holiday_id      INT UNSIGNED NOT NULL,
    branch_id       INT UNSIGNED,
    department_id   INT UNSIGNED,
    FOREIGN KEY (holiday_id) REFERENCES holidays(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    UNIQUE KEY uk_holiday_assignment (holiday_id, branch_id, department_id)
) ENGINE=InnoDB COMMENT='Holiday assignments to branches/departments';

-- ================================================================
-- 14. PAYROLL SYSTEM
-- ================================================================

CREATE TABLE salary_components (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    code            VARCHAR(30) NOT NULL,
    name            VARCHAR(200) NOT NULL,
    type            ENUM('Earning','Deduction','Reimbursement','Bonus') NOT NULL,
    category        ENUM('Basic','Allowance','Bonus','PF','Tax','Insurance','Loan','Other') DEFAULT 'Other',
    calculation_type ENUM('Fixed','Percentage of Basic','Percentage of Gross','Formula','Custom') DEFAULT 'Fixed',
    default_value   DECIMAL(14,4) DEFAULT 0,
    formula_expression TEXT COMMENT 'For dynamic calculation',
    is_taxable      TINYINT(1) DEFAULT 0,
    is_pf_basis     TINYINT(1) DEFAULT 0,
    is_active       TINYINT(1) DEFAULT 1,
    show_in_slip    TINYINT(1) DEFAULT 1,
    display_order   INT DEFAULT 0,
    metadata        JSON,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_salary_component (company_id, code),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_component_type (type)
) ENGINE=InnoDB COMMENT='Salary components master';

CREATE TABLE employee_salary_structure (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    component_id    INT UNSIGNED NOT NULL,
    amount          DECIMAL(14,4) NOT NULL,
    effective_from  DATE NOT NULL,
    effective_to    DATE,
    is_percentage   TINYINT(1) DEFAULT 0,
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (component_id) REFERENCES salary_components(id),
    INDEX idx_salary_employee (employee_id),
    INDEX idx_salary_effective (effective_from, effective_to)
) ENGINE=InnoDB COMMENT='Employee-specific salary structure';

-- Table for payroll processing
CREATE TABLE payroll_runs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    fiscal_year_id  INT UNSIGNED NOT NULL,
    run_month       DATE NOT NULL COMMENT 'First day of month',
    run_label       VARCHAR(100),
    run_type        ENUM('Regular','Bonus','Advance','Adjustment') DEFAULT 'Regular',
    total_employees INT DEFAULT 0,
    total_gross     DECIMAL(16,2) DEFAULT 0,
    total_net       DECIMAL(16,2) DEFAULT 0,
    total_deductions DECIMAL(16,2) DEFAULT 0,
    status          ENUM('Draft','Processing','Calculated','Reviewed','Approved','Disbursed','Locked','Cancelled') DEFAULT 'Draft',
    approved_by     INT UNSIGNED,
    approved_at     DATETIME,
    disbursed_by    INT UNSIGNED,
    disbursed_at    DATETIME,
    notes           TEXT,
    created_by      INT UNSIGNED,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_payroll_run (company_id, run_month, run_type),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id),
    INDEX idx_payroll_status (status),
    INDEX idx_payroll_month (run_month)
) ENGINE=InnoDB COMMENT='Monthly payroll processing batch';

-- Salary slip table
CREATE TABLE payroll_salary_slips (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    run_id              INT UNSIGNED NOT NULL,
    employee_id         INT UNSIGNED NOT NULL,
    pay_month           DATE NOT NULL,
    basic_salary        DECIMAL(14,2) DEFAULT 0,
    total_earnings      DECIMAL(14,2) DEFAULT 0,
    total_deductions    DECIMAL(14,2) DEFAULT 0,
    gross_salary        DECIMAL(14,2) DEFAULT 0,
    taxable_income      DECIMAL(14,2) DEFAULT 0,
    income_tax          DECIMAL(14,2) DEFAULT 0,
    net_payable         DECIMAL(14,2) DEFAULT 0,
    working_days        INT DEFAULT 0,
    present_days        DECIMAL(5,1) DEFAULT 0,
    absent_days         DECIMAL(5,1) DEFAULT 0,
    leave_days          DECIMAL(5,1) DEFAULT 0,
    late_deduction      DECIMAL(14,2) DEFAULT 0,
    absent_deduction    DECIMAL(14,2) DEFAULT 0,
    overtime_amount     DECIMAL(14,2) DEFAULT 0,
    bonus_amount        DECIMAL(14,2) DEFAULT 0,
    advance_deduction   DECIMAL(14,2) DEFAULT 0,
    loan_deduction      DECIMAL(14,2) DEFAULT 0,
    pf_deduction        DECIMAL(14,2) DEFAULT 0,
    bank_name           VARCHAR(200),
    bank_account        VARCHAR(80),
    payment_method      ENUM('Bank','Cash','MFS','Cheque') DEFAULT 'Bank',
    payment_ref         VARCHAR(100),
    payment_date        DATE,
    status              ENUM('Draft','Approved','Paid','Cancelled') DEFAULT 'Draft',
    slip_data           JSON COMMENT 'Complete breakdown',
    generated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_salary_slip (run_id, employee_id),
    FOREIGN KEY (run_id) REFERENCES payroll_runs(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    INDEX idx_slip_employee (employee_id),
    INDEX idx_slip_month (pay_month),
    INDEX idx_slip_status (status)
) ENGINE=InnoDB COMMENT='Individual salary slips';

-- Payroll adjustments
CREATE TABLE payroll_adjustments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED NOT NULL,
    slip_id         BIGINT UNSIGNED,
    adjustment_type ENUM('Bonus','Increment','Deduction','Correction','Reimbursement') NOT NULL,
    amount          DECIMAL(14,2) NOT NULL,
    reason          TEXT,
    effective_month DATE NOT NULL,
    approved_by     INT UNSIGNED,
    approved_at     DATETIME,
    status          ENUM('Pending','Approved','Processed','Rejected') DEFAULT 'Pending',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (slip_id) REFERENCES payroll_salary_slips(id),
    FOREIGN KEY (approved_by) REFERENCES employees(id),
    INDEX idx_adjustment_employee (employee_id),
    INDEX idx_adjustment_month (effective_month)
) ENGINE=InnoDB COMMENT='Payroll adjustments and corrections';

-- ================================================================
-- 15. PERFORMANCE MANAGEMENT
-- ================================================================

CREATE TABLE appraisal_cycles (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    name            VARCHAR(200) NOT NULL,
    fiscal_year_id  INT UNSIGNED NOT NULL,
    cycle_type      ENUM('Annual','Semi-Annual','Quarterly','Monthly','Custom') DEFAULT 'Annual',
    review_period_from DATE,
    review_period_to DATE,
    start_date      DATE,
    end_date        DATE,
    status          ENUM('Planned','Active','Completed','Closed') DEFAULT 'Planned',
    metadata        JSON,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id),
    INDEX idx_appraisal_company (company_id)
) ENGINE=InnoDB COMMENT='Performance review periods';

CREATE TABLE performance_reviews (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cycle_id        INT UNSIGNED NOT NULL,
    employee_id     INT UNSIGNED NOT NULL,
    reviewer_id     INT UNSIGNED NOT NULL,
    review_type     ENUM('Self','Manager','Peer','Subordinate','HR') DEFAULT 'Manager',
    rating          DECIMAL(3,1),
    rating_label    VARCHAR(50),
    strengths       TEXT,
    improvements    TEXT,
    comments        TEXT,
    goals_achieved  JSON,
    recommendations TEXT,
    status          ENUM('Pending','In Progress','Submitted','Reviewed','Approved','Closed') DEFAULT 'Pending',
    submitted_at    DATETIME,
    reviewed_at     DATETIME,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_review (cycle_id, employee_id, review_type),
    FOREIGN KEY (cycle_id) REFERENCES appraisal_cycles(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (reviewer_id) REFERENCES employees(id),
    INDEX idx_review_employee (employee_id),
    INDEX idx_review_status (status)
) ENGINE=InnoDB COMMENT='Employee performance reviews';

CREATE TABLE kpis (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    name            VARCHAR(200) NOT NULL,
    category        VARCHAR(100),
    description     TEXT,
    target_type     ENUM('Numeric','Percentage','Yes/No','Rating') DEFAULT 'Numeric',
    target_value    VARCHAR(100),
    unit            VARCHAR(50),
    weightage       DECIMAL(5,2) DEFAULT 0,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_kpi_active (is_active)
) ENGINE=InnoDB COMMENT='Key Performance Indicators';

CREATE TABLE employee_kpi_scores (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_id       INT UNSIGNED NOT NULL,
    kpi_id          INT UNSIGNED NOT NULL,
    actual_value    VARCHAR(100),
    score           DECIMAL(5,2),
    comments        TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES performance_reviews(id),
    FOREIGN KEY (kpi_id) REFERENCES kpis(id),
    UNIQUE KEY uk_kpi_score (review_id, kpi_id)
) ENGINE=InnoDB COMMENT='KPI scores for reviews';

-- ================================================================
-- 16. RECRUITMENT MODULE
-- ================================================================

CREATE TABLE job_postings (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    department_id   INT UNSIGNED,
    designation_id  INT UNSIGNED,
    title           VARCHAR(300) NOT NULL,
    job_code        VARCHAR(50) UNIQUE,
    employment_type ENUM('Full-Time','Part-Time','Contractual','Intern','Freelance') DEFAULT 'Full-Time',
    vacancies       INT DEFAULT 1,
    description     LONGTEXT,
    requirements    LONGTEXT,
    responsibilities LONGTEXT,
    skills_required JSON,
    min_experience  DECIMAL(4,1) DEFAULT 0,
    max_experience  DECIMAL(4,1),
    min_salary      DECIMAL(14,2),
    max_salary      DECIMAL(14,2),
    salary_currency CHAR(3) DEFAULT 'BDT',
    posting_date    DATE,
    deadline        DATE,
    status          ENUM('Draft','Published','On Hold','Closed','Filled','Expired') DEFAULT 'Draft',
    posted_by       INT UNSIGNED,
    metadata        JSON,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (designation_id) REFERENCES designations(id),
    INDEX idx_job_status (status),
    INDEX idx_job_deadline (deadline)
) ENGINE=InnoDB COMMENT='Job vacancy announcements';

CREATE TABLE job_applicants (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id          INT UNSIGNED NOT NULL,
    applicant_no    VARCHAR(30) UNIQUE,
    full_name       VARCHAR(300) NOT NULL,
    email           VARCHAR(200),
    phone           VARCHAR(20) NOT NULL,
    address         TEXT,
    date_of_birth   DATE,
    gender          ENUM('Male','Female','Other'),
    current_company VARCHAR(300),
    current_designation VARCHAR(200),
    current_salary  DECIMAL(14,2),
    expected_salary DECIMAL(14,2),
    notice_period   INT DEFAULT 0,
    experience_years DECIMAL(4,1),
    resume_path     VARCHAR(500),
    cover_letter    TEXT,
    source          ENUM('Website','LinkedIn','Job Portal','Referral','Agency','Walk-in','Other'),
    referral_employee_id INT UNSIGNED,
    current_stage   VARCHAR(50),
    status          ENUM('Applied','Screening','Shortlisted','Interview','Assessment','Offer','Selected','Joined','Rejected','Withdrawn') DEFAULT 'Applied',
    rejection_reason TEXT,
    applied_date    DATE NOT NULL,
    notes           TEXT,
    metadata        JSON,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES job_postings(id),
    FOREIGN KEY (referral_employee_id) REFERENCES employees(id),
    INDEX idx_applicant_job (job_id),
    INDEX idx_applicant_status (status),
    INDEX idx_applicant_phone (phone),
    INDEX idx_applicant_email (email)
) ENGINE=InnoDB COMMENT='Job applicants/candidates';

CREATE TABLE interviews (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    applicant_id    INT UNSIGNED NOT NULL,
    interview_round TINYINT DEFAULT 1,
    scheduled_at    DATETIME NOT NULL,
    duration_minutes INT DEFAULT 60,
    mode            ENUM('In-Person','Phone','Video','Online') DEFAULT 'In-Person',
    venue           VARCHAR(300),
    meeting_link    VARCHAR(500),
    interviewers    JSON COMMENT 'List of interviewer IDs',
    status          ENUM('Scheduled','Completed','Cancelled','No Show','Rescheduled') DEFAULT 'Scheduled',
    result          ENUM('Pass','Fail','On Hold','Pending') DEFAULT 'Pending',
    feedback        TEXT,
    rating          DECIMAL(3,1),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES job_applicants(id),
    INDEX idx_interview_applicant (applicant_id),
    INDEX idx_interview_date (scheduled_at)
) ENGINE=InnoDB COMMENT='Interview schedules';

CREATE TABLE job_offers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    applicant_id    INT UNSIGNED NOT NULL UNIQUE,
    offered_basic   DECIMAL(14,2),
    offered_gross   DECIMAL(14,2),
    joining_date    DATE,
    offer_date      DATE,
    expiry_date     DATE,
    offer_letter_path VARCHAR(500),
    status          ENUM('Pending','Accepted','Declined','Expired','Revoked') DEFAULT 'Pending',
    response_date   DATE,
    response_notes  TEXT,
    metadata        JSON,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES job_applicants(id),
    INDEX idx_offer_status (status)
) ENGINE=InnoDB COMMENT='Job offer letters';

-- ================================================================
-- 17. TRAINING MANAGEMENT
-- ================================================================

CREATE TABLE training_programs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    title           VARCHAR(300) NOT NULL,
    description     TEXT,
    category        VARCHAR(150),
    training_type   ENUM('Technical','Soft Skills','Leadership','Compliance','Safety','Other') DEFAULT 'Technical',
    mode            ENUM('In-House','External','Online','Blended') DEFAULT 'In-House',
    trainer_name    VARCHAR(200),
    trainer_organization VARCHAR(300),
    venue           VARCHAR(300),
    start_date      DATE,
    end_date        DATE,
    duration_hours  DECIMAL(6,1),
    max_participants INT DEFAULT 0,
    budget          DECIMAL(14,2),
    cost_per_head   DECIMAL(14,2),
    status          ENUM('Planned','Approved','Ongoing','Completed','Cancelled') DEFAULT 'Planned',
    created_by      INT UNSIGNED,
    metadata        JSON,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    INDEX idx_training_status (status),
    INDEX idx_training_dates (start_date, end_date)
) ENGINE=InnoDB COMMENT='Training program catalog';

CREATE TABLE training_enrollments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    program_id      INT UNSIGNED NOT NULL,
    employee_id     INT UNSIGNED NOT NULL,
    enrollment_date DATE,
    attendance_percentage DECIMAL(5,2),
    pre_test_score  DECIMAL(5,2),
    post_test_score DECIMAL(5,2),
    result          ENUM('Pass','Fail','Incomplete','Exempt') DEFAULT 'Incomplete',
    certificate_issued TINYINT(1) DEFAULT 0,
    certificate_path VARCHAR(500),
    feedback_rating TINYINT,
    feedback_comments TEXT,
    status          ENUM('Enrolled','Attended','Completed','Dropped','Waitlisted') DEFAULT 'Enrolled',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_training_enrollment (program_id, employee_id),
    FOREIGN KEY (program_id) REFERENCES training_programs(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    INDEX idx_enrollment_employee (employee_id),
    INDEX idx_enrollment_result (result)
) ENGINE=InnoDB COMMENT='Employee training registration';

-- ================================================================
-- 18. ASSET MANAGEMENT
-- ================================================================

CREATE TABLE asset_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    name            VARCHAR(150) NOT NULL,
    parent_id       INT UNSIGNED,
    depreciation_rate DECIMAL(5,2) DEFAULT 0,
    useful_life_months INT DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (parent_id) REFERENCES asset_categories(id)
) ENGINE=InnoDB COMMENT='Asset categories';

CREATE TABLE assets (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    category_id     INT UNSIGNED,
    asset_code      VARCHAR(80) NOT NULL UNIQUE,
    name            VARCHAR(300) NOT NULL,
    brand           VARCHAR(150),
    model           VARCHAR(150),
    serial_number   VARCHAR(150),
    purchase_date   DATE,
    purchase_price  DECIMAL(14,2),
    warranty_expiry DATE,
    condition_status ENUM('New','Excellent','Good','Fair','Poor','Scrapped') DEFAULT 'Good',
    availability_status ENUM('Available','Assigned','Under Repair','Maintenance','Lost','Scrapped') DEFAULT 'Available',
    location        VARCHAR(300),
    assigned_to     INT UNSIGNED,
    notes           TEXT,
    metadata        JSON,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (category_id) REFERENCES asset_categories(id),
    FOREIGN KEY (assigned_to) REFERENCES employees(id),
    INDEX idx_asset_code (asset_code),
    INDEX idx_asset_status (availability_status),
    INDEX idx_asset_assigned (assigned_to)
) ENGINE=InnoDB COMMENT='Company asset inventory';

CREATE TABLE asset_assignments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id        INT UNSIGNED NOT NULL,
    employee_id     INT UNSIGNED NOT NULL,
    assigned_date   DATE NOT NULL,
    expected_return DATE,
    actual_return   DATE,
    condition_out   VARCHAR(50),
    condition_in    VARCHAR(50),
    assigned_by     INT UNSIGNED,
    received_by     INT UNSIGNED,
    notes           TEXT,
    status          ENUM('Assigned','Returned','Lost','Damaged') DEFAULT 'Assigned',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (assigned_by) REFERENCES employees(id),
    INDEX idx_assignment_asset (asset_id),
    INDEX idx_assignment_employee (employee_id),
    INDEX idx_assignment_status (status)
) ENGINE=InnoDB COMMENT='Asset allocation history';

-- ================================================================
-- 19. COMPLAINT & GRIEVANCE
-- ================================================================

CREATE TABLE grievance_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    name            VARCHAR(200) NOT NULL,
    description     TEXT,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id)
) ENGINE=InnoDB COMMENT='Complaint categories';

CREATE TABLE grievances (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED NOT NULL,
    employee_id     INT UNSIGNED NOT NULL,
    category_id     INT UNSIGNED,
    ticket_no       VARCHAR(30) UNIQUE,
    subject         VARCHAR(500) NOT NULL,
    description     TEXT NOT NULL,
    against_employee_id INT UNSIGNED,
    attachment_path VARCHAR(500),
    is_anonymous    TINYINT(1) DEFAULT 0,
    severity        ENUM('Low','Medium','High','Critical') DEFAULT 'Medium',
    status          ENUM('Submitted','Acknowledged','Under Investigation','Resolved','Closed','Rejected') DEFAULT 'Submitted',
    assigned_to     INT UNSIGNED,
    resolution      TEXT,
    resolved_at     DATETIME,
    closed_by       INT UNSIGNED,
    closed_at       DATETIME,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (category_id) REFERENCES grievance_categories(id),
    FOREIGN KEY (against_employee_id) REFERENCES employees(id),
    FOREIGN KEY (assigned_to) REFERENCES employees(id),
    INDEX idx_grievance_employee (employee_id),
    INDEX idx_grievance_status (status),
    INDEX idx_grievance_ticket (ticket_no)
) ENGINE=InnoDB COMMENT='Employee complaint/grievance tickets';

-- ================================================================
-- 20. SYSTEM SECURITY & AUDIT
-- ================================================================

CREATE TABLE roles (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id      INT UNSIGNED,
    name            VARCHAR(150) NOT NULL,
    description     TEXT,
    permissions     JSON COMMENT 'Stored as JSON for flexibility',
    is_system       TINYINT(1) DEFAULT 0,
    is_active       TINYINT(1) DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_role_name (company_id, name)
) ENGINE=InnoDB COMMENT='User role definitions';

CREATE TABLE user_accounts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id     INT UNSIGNED UNIQUE,
    username        VARCHAR(100) NOT NULL UNIQUE,
    email           VARCHAR(200) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    role_id         INT UNSIGNED NOT NULL,
    two_factor_secret VARCHAR(255),
    is_active       TINYINT(1) DEFAULT 1,
    is_locked       TINYINT(1) DEFAULT 0,
    must_change_password TINYINT(1) DEFAULT 1,
    last_login      DATETIME,
    last_login_ip   VARCHAR(45),
    login_count     INT DEFAULT 0,
    failed_attempts INT DEFAULT 0,
    locked_until    DATETIME,
    password_changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (role_id) REFERENCES roles(id),
    INDEX idx_user_username (username),
    INDEX idx_user_email (email),
    INDEX idx_user_active (is_active)
) ENGINE=InnoDB COMMENT='System login accounts';

CREATE TABLE audit_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED,
    employee_id     INT UNSIGNED,
    action          VARCHAR(100) NOT NULL,
    module          VARCHAR(100),
    table_name      VARCHAR(100),
    record_id       BIGINT UNSIGNED,
    old_values      JSON,
    new_values      JSON,
    changes         JSON GENERATED ALWAYS AS (
        JSON_OBJECT(
            'old', old_values,
            'new', new_values
        )
    ) STORED,
    ip_address      VARCHAR(45),
    user_agent      VARCHAR(500),
    session_id      VARCHAR(128),
    request_id      VARCHAR(64),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_table (table_name, record_id),
    INDEX idx_audit_action (action),
    INDEX idx_audit_date (created_at),
    INDEX idx_audit_request (request_id)
) ENGINE=InnoDB COMMENT='Complete audit trail';

-- ================================================================
-- 21. OPTIMIZED VIEWS
-- ================================================================

CREATE VIEW vw_employee_full AS
SELECT 
    e.id,
    e.employee_code,
    e.full_name,
    e.first_name,
    e.last_name,
    e.display_name,
    e.phone,
    e.phone_2,
    e.email,
    e.gender,
    e.date_of_birth,
    TIMESTAMPDIFF(YEAR, e.date_of_birth, CURDATE()) AS age,
    e.nationality,
    pi.personal_email,
    pi.blood_group,
    pi.marital_status,
    pi.nid_number,
    pi.passport_number,
    pi.tin_number,
    b.name AS branch_name,
    b.code AS branch_code,
    d.name AS department_name,
    d.code AS department_code,
    des.title AS designation,
    sg.name AS grade,
    sg.min_salary AS grade_min_salary,
    sg.max_salary AS grade_max_salary,
    s.name AS shift_name,
    s.start_time AS shift_start,
    s.end_time AS shift_end,
    m.full_name AS reports_to_name,
    m.employee_code AS reports_to_code,
    e.employment_type,
    e.joining_date,
    e.confirmation_date,
    e.probation_end_date,
    e.last_working_day,
    e.contract_end_date,
    TIMESTAMPDIFF(YEAR, e.joining_date, COALESCE(e.last_working_day, CURDATE())) AS service_years,
    TIMESTAMPDIFF(MONTH, e.joining_date, COALESCE(e.last_working_day, CURDATE())) AS service_months,
    e.status,
    eb.bank_name,
    eb.bank_account,
    eb.payment_method,
    e.portal_active,
    e.portal_last_login,
    e.created_at,
    e.updated_at
FROM employees e
LEFT JOIN employee_personal_info pi ON pi.employee_id = e.id
LEFT JOIN branches b ON b.id = e.branch_id
LEFT JOIN departments d ON d.id = e.department_id
LEFT JOIN designations des ON des.id = e.designation_id
LEFT JOIN salary_grades sg ON sg.id = e.grade_id
LEFT JOIN shifts s ON s.id = e.shift_id
LEFT JOIN employees m ON m.id = e.reports_to
LEFT JOIN employee_banking eb ON eb.employee_id = e.id AND eb.is_primary = 1
WHERE e.deleted_at IS NULL
COMMENT='Complete employee view with all related data';

CREATE VIEW vw_attendance_summary AS
SELECT 
    e.id AS employee_id,
    e.employee_code,
    e.full_name,
    d.name AS department,
    des.title AS designation,
    DATE_FORMAT(a.att_date, '%Y-%m') AS month_year,
    COUNT(DISTINCT a.att_date) AS total_days,
    SUM(CASE WHEN a.attendance_status = 'Present' THEN 1 ELSE 0 END) AS present_days,
    SUM(CASE WHEN a.attendance_status = 'Absent' THEN 1 ELSE 0 END) AS absent_days,
    SUM(CASE WHEN a.attendance_status = 'Late' THEN 1 ELSE 0 END) AS late_days,
    SUM(CASE WHEN a.attendance_status = 'Half Day' THEN 1 ELSE 0 END) AS half_days,
    SUM(a.overtime_minutes) / 60 AS overtime_hours,
    SUM(a.net_working_minutes) / 60 AS total_working_hours,
    AVG(CASE WHEN a.net_working_minutes > 0 THEN a.net_working_minutes END) / 60 AS avg_working_hours
FROM attendance a
JOIN employees e ON e.id = a.employee_id
JOIN departments d ON d.id = e.department_id
JOIN designations des ON des.id = e.designation_id
WHERE a.attendance_status != 'Weekend'
  AND a.attendance_status != 'Holiday'
  AND e.status = 'Active'
GROUP BY e.id, DATE_FORMAT(a.att_date, '%Y-%m')
ORDER BY month_year DESC;

CREATE VIEW vw_leave_balance_summary AS
SELECT 
    e.id AS employee_id,
    e.employee_code,
    e.full_name,
    lt.code AS leave_type,
    lt.name AS leave_name,
    elb.opening_balance,
    elb.earned_days,
    elb.used_days,
    elb.pending_days,
    elb.remaining_days,
    fy.label AS fiscal_year
FROM employee_leave_balance elb
JOIN employees e ON e.id = elb.employee_id
JOIN leave_types lt ON lt.id = elb.leave_type_id
JOIN fiscal_years fy ON fy.id = elb.fiscal_year_id
WHERE e.status = 'Active'
  AND fy.is_current = 1;

-- ================================================================
-- 22. STORED PROCEDURES
-- ================================================================

DELIMITER //

-- Calculate monthly attendance summary
CREATE PROCEDURE sp_calculate_monthly_attendance(
    IN p_employee_id INT,
    IN p_year_month DATE
)
BEGIN
    INSERT INTO attendance_monthly_summary 
    (employee_id, year_month, total_working_days, present_days, absent_days, 
     late_days, half_days, leave_days, holiday_days, weekend_days,
     overtime_hours, total_working_hours, total_late_minutes)
    SELECT 
        a.employee_id,
        DATE_FORMAT(a.att_date, '%Y-%m-01') AS year_month,
        COUNT(*) AS total_working_days,
        SUM(CASE WHEN a.attendance_status = 'Present' THEN 1 ELSE 0 END) AS present_days,
        SUM(CASE WHEN a.attendance_status = 'Absent' THEN 1 ELSE 0 END) AS absent_days,
        SUM(CASE WHEN a.attendance_status = 'Late' THEN 1 ELSE 0 END) AS late_days,
        SUM(CASE WHEN a.attendance_status = 'Half Day' THEN 1 ELSE 0 END) AS half_days,
        SUM(CASE WHEN a.attendance_status = 'On Leave' THEN 1 ELSE 0 END) AS leave_days,
        SUM(CASE WHEN a.attendance_status = 'Holiday' THEN 1 ELSE 0 END) AS holiday_days,
        SUM(CASE WHEN a.attendance_status = 'Weekend' THEN 1 ELSE 0 END) AS weekend_days,
        SUM(a.overtime_minutes) / 60 AS overtime_hours,
        SUM(a.net_working_minutes) / 60 AS total_working_hours,
        SUM(a.late_minutes) AS total_late_minutes
    FROM attendance a
    WHERE a.employee_id = p_employee_id
        AND DATE_FORMAT(a.att_date, '%Y-%m-01') = p_year_month
    GROUP BY a.employee_id, DATE_FORMAT(a.att_date, '%Y-%m-01')
    ON DUPLICATE KEY UPDATE
        total_working_days = VALUES(total_working_days),
        present_days = VALUES(present_days),
        absent_days = VALUES(absent_days),
        late_days = VALUES(late_days),
        half_days = VALUES(half_days),
        leave_days = VALUES(leave_days),
        holiday_days = VALUES(holiday_days),
        weekend_days = VALUES(weekend_days),
        overtime_hours = VALUES(overtime_hours),
        total_working_hours = VALUES(total_working_hours),
        total_late_minutes = VALUES(total_late_minutes),
        updated_at = CURRENT_TIMESTAMP;
END//

-- Process payroll for a month
CREATE PROCEDURE sp_process_payroll(
    IN p_company_id INT,
    IN p_run_month DATE,
    IN p_created_by INT
)
AS $$
DECLARE v_run_id INT;
BEGIN
    -- Create payroll run
    INSERT INTO payroll_runs (company_id, fiscal_year_id, run_month, status, created_by)
    VALUES (
        p_company_id,
        (SELECT id FROM fiscal_years WHERE company_id = p_company_id AND is_current = 1),
        p_run_month,
        'Processing',
        p_created_by
    );
    
    SET v_run_id = LAST_INSERT_ID();
    
    -- Generate salary slips for active employees
    INSERT INTO payroll_salary_slips 
    (run_id, employee_id, pay_month, basic_salary, working_days, present_days, absent_days)
    SELECT 
        v_run_id,
        e.id,
        p_run_month,
        COALESCE(ess.amount, 0),
        COALESCE(ams.total_working_days, 0),
        COALESCE(ams.present_days, 0),
        COALESCE(ams.absent_days, 0)
    FROM employees e
    LEFT JOIN employee_salary_structure ess 
        ON ess.employee_id = e.id 
        AND ess.component_id = (SELECT id FROM salary_components WHERE code = 'BASIC')
        AND ess.effective_from <= p_run_month
        AND (ess.effective_to IS NULL OR ess.effective_to >= p_run_month)
    LEFT JOIN attendance_monthly_summary ams 
        ON ams.employee_id = e.id 
        AND ams.year_month = DATE_FORMAT(p_run_month, '%Y-%m-01')
    WHERE e.company_id = p_company_id
        AND e.status = 'Active'
        AND (e.last_working_day IS NULL OR e.last_working_day >= p_run_month);
    
    -- Update run status
    UPDATE payroll_runs 
    SET status = 'Calculated', 
        total_employees = (SELECT COUNT(*) FROM payroll_salary_slips WHERE run_id = v_run_id)
    WHERE id = v_run_id;
    
    SELECT v_run_id AS run_id;
END;//
DELIMITER ;

-- ================================================================
-- 23. FUNCTIONS
-- ================================================================

DELIMITER //

CREATE FUNCTION fn_get_employee_leave_balance(
    p_employee_id INT,
    p_leave_type_id INT
) 
RETURNS DECIMAL(5,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_balance DECIMAL(5,2);
    
    SELECT remaining_days INTO v_balance
    FROM employee_leave_balance
    WHERE employee_id = p_employee_id
        AND leave_type_id = p_leave_type_id
        AND fiscal_year_id = (SELECT id FROM fiscal_years WHERE is_current = 1 LIMIT 1);
    
    RETURN COALESCE(v_balance, 0);
END//

CREATE FUNCTION fn_get_employee_age(p_employee_id INT) 
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_age INT;
    
    SELECT TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) INTO v_age
    FROM employees
    WHERE id = p_employee_id;
    
    RETURN v_age;
END//

DELIMITER ;

-- ================================================================
-- 24. TRIGGERS
-- ================================================================

DELIMITER //

-- Auto-update employee status based on last_working_day
CREATE TRIGGER tr_employee_status_update
BEFORE UPDATE ON employees
FOR EACH ROW
BEGIN
    IF NEW.last_working_day IS NOT NULL 
       AND NEW.last_working_day <= CURDATE() 
       AND NEW.status NOT IN ('Terminated', 'Resigned') THEN
        SET NEW.status = 'Terminated';
    END IF;
    
    IF NEW.confirmation_date IS NOT NULL 
       AND NEW.status = 'Probation' THEN
        SET NEW.status = 'Active';
    END IF;
END//

-- Log all changes to audit table
CREATE TRIGGER tr_employees_audit
AFTER UPDATE ON employees
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (user_id, employee_id, action, table_name, record_id, old_values, new_values)
    VALUES (
        NEW.created_by,
        NEW.id,
        'UPDATE',
        'employees',
        NEW.id,
        JSON_OBJECT(
            'status', OLD.status,
            'department_id', OLD.department_id,
            'designation_id', OLD.designation_id,
            'reports_to', OLD.reports_to,
            'employment_type', OLD.employment_type
        ),
        JSON_OBJECT(
            'status', NEW.status,
            'department_id', NEW.department_id,
            'designation_id', NEW.designation_id,
            'reports_to', NEW.reports_to,
            'employment_type', NEW.employment_type
        )
    );
END//

DELIMITER ;

-- ================================================================
-- 25. INITIAL SEED DATA
-- ================================================================

INSERT INTO companies (id, name, legal_name, industry, phone, email, address, city, timezone) VALUES
(1, 'Acme Corporation Ltd.', 'Acme Corporation Limited', 'Manufacturing', '+8801700000000', 'hr@acme.com.bd', 'House 12, Road 5, Gulshan-2', 'Dhaka', 'Asia/Dhaka');

INSERT INTO branches (company_id, code, name, address, city, is_head_office) VALUES
(1, 'HO-DHA', 'Head Office – Dhaka', 'House 12, Road 5, Gulshan-2', 'Dhaka', 1),
(1, 'BR-CTG', 'Branch – Chattogram', 'Agrabad, Port Area', 'Chattogram', 0);

INSERT INTO fiscal_years (company_id, label, start_date, end_date, is_current) VALUES
(1, '2024-2025', '2024-07-01', '2025-06-30', 0),
(1, '2025-2026', '2025-07-01', '2026-06-30', 1);

INSERT INTO departments (company_id, branch_id, code, name) VALUES
(1, 1, 'HR', 'Human Resources'),
(1, 1, 'IT', 'Information Technology'),
(1, 1, 'FIN', 'Finance'),
(1, 2, 'OPS', 'Operations');

INSERT INTO salary_components (company_id, code, name, type, calculation_type, default_value, is_taxable, display_order) VALUES
(1, 'BASIC', 'Basic Salary', 'Earning', 'Fixed', 0, 1, 1),
(1, 'HRA', 'House Rent Allowance', 'Earning', 'Percentage of Basic', 50, 0, 2),
(1, 'MEDICAL', 'Medical Allowance', 'Earning', 'Fixed', 1000, 0, 3),
(1, 'CONVEYANCE', 'Conveyance Allowance', 'Earning', 'Fixed', 500, 0, 4),
(1, 'PF_EMP', 'Provident Fund (Employee)', 'Deduction', 'Percentage of Basic', 10, 0, 10),
(1, 'PF_EMP', 'Provident Fund (Employer)', 'Earning', 'Percentage of Basic', 12, 0, 5),
(1, 'TAX', 'Income Tax', 'Deduction', 'Percentage of Gross', 0, 0, 11);

INSERT INTO leave_types (company_id, code, name, days_per_year, is_paid, carry_forward, max_carry_days) VALUES
(1, 'CL', 'Casual Leave', 10, 1, 0, 0),
(1, 'SL', 'Sick Leave', 14, 1, 0, 0),
(1, 'AL', 'Annual Leave', 20, 1, 1, 30);

INSERT INTO shifts (company_id, name, code, start_time, end_time, break_minutes, work_hours) VALUES
(1, 'Morning Shift', 'SH-MOR', '09:00:00', '18:00:00', 60, 8.0),
(1, 'Evening Shift', 'SH-EVE', '14:00:00', '23:00:00', 60, 8.0),
(1, 'Night Shift', 'SH-NGT', '22:00:00', '06:00:00', 60, 8.0);

INSERT INTO weekend_policies (company_id, name, is_default, friday, saturday) VALUES
(1, 'Friday-Saturday Weekend', 1, 1, 1);

INSERT INTO skill_categories (name) VALUES
('Technical Skills'),
('Soft Skills'),
('Management Skills'),
('Language Skills');

INSERT INTO roles (company_id, name, description, is_system) VALUES
(NULL, 'Super Admin', 'Full system access', 1),
(1, 'HR Manager', 'Complete HR module access', 0),
(1, 'Department Head', 'Department management access', 0),
(1, 'Team Lead', 'Team supervision access', 0),
(1, 'Employee', 'Self-service portal access', 0);

INSERT INTO document_categories (company_id, name, code, requires_expiry, is_mandatory) VALUES
(1, 'National ID', 'NID', 1, 1),
(1, 'Passport', 'PASSPORT', 1, 0),
(1, 'Educational Certificate', 'EDU', 0, 0),
(1, 'Medical Certificate', 'MEDICAL', 1, 0);

-- ================================================================
-- 26. INDEX PERFORMANCE TUNING
-- ================================================================

-- Composite indexes for common query patterns
CREATE INDEX idx_attendance_employee_date ON attendance(employee_id, att_date DESC);
CREATE INDEX idx_attendance_status_date ON attendance(attendance_status, att_date);
CREATE INDEX idx_employees_status_dept ON employees(status, department_id);
CREATE INDEX idx_employees_joining_date ON employees(joining_date DESC);
CREATE INDEX idx_payroll_slips_employee_month ON payroll_salary_slips(employee_id, pay_month DESC);
CREATE INDEX idx_leave_applications_employee_dates ON leave_applications(employee_id, from_date, to_date);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at DESC);

-- Full-text indexes for search
ALTER TABLE employees ADD FULLTEXT INDEX ft_employee_search (full_name, first_name, last_name, employee_code);
ALTER TABLE job_postings ADD FULLTEXT INDEX ft_job_search (title, description, requirements);

-- ================================================================
-- 27. DATABASE MAINTENANCE EVENTS
-- ================================================================

-- Event to archive old attendance records (run monthly)
DELIMITER //
CREATE EVENT ev_archive_old_attendance
ON SCHEDULE EVERY 1 MONTH
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    -- Move attendance records older than 3 years to archive table
    INSERT INTO attendance_archive 
    SELECT * FROM attendance 
    WHERE att_date < DATE_SUB(CURDATE(), INTERVAL 3 YEAR);
    
    DELETE FROM attendance 
    WHERE att_date < DATE_SUB(CURDATE(), INTERVAL 3 YEAR);
END//

-- Event to update monthly attendance summaries
CREATE EVENT ev_update_attendance_summary
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_emp_id INT;
    DECLARE cur CURSOR FOR SELECT DISTINCT employee_id FROM attendance WHERE att_date = CURDATE();
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO v_emp_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        CALL sp_calculate_monthly_attendance(v_emp_id, DATE_FORMAT(CURDATE(), '%Y-%m-01'));
    END LOOP;
    CLOSE cur;
END//

DELIMITER ;

-- ================================================================
-- 28. DATABASE STATISTICS VIEW
-- ================================================================

CREATE OR REPLACE VIEW vw_database_statistics AS
SELECT 
    (SELECT COUNT(*) FROM companies) AS total_companies,
    (SELECT COUNT(*) FROM branches) AS total_branches,
    (SELECT COUNT(*) FROM departments) AS total_departments,
    (SELECT COUNT(*) FROM employees WHERE status = 'Active') AS active_employees,
    (SELECT COUNT(*) FROM employees WHERE status IN ('Terminated','Resigned')) AS inactive_employees,
    (SELECT COUNT(*) FROM attendance WHERE att_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) AS last_30_days_attendance,
    (SELECT COUNT(*) FROM payroll_runs WHERE status = 'Approved') AS total_payroll_runs,
    (SELECT SUM(net_payable) FROM payroll_salary_slips WHERE pay_month >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) AS yearly_payroll_amount,
    (SELECT COUNT(*) FROM leave_applications WHERE status = 'Pending') AS pending_leave_requests,
    (SELECT COUNT(*) FROM job_applications WHERE status = 'Applied') AS pending_job_applications;

-- ================================================================
-- 29. SECURITY - SANITIZE AND VALIDATION
-- ================================================================

-- Add check constraints for data validation
ALTER TABLE employees ADD CONSTRAINT chk_joining_date CHECK (joining_date <= COALESCE(last_working_day, '9999-12-31'));
ALTER TABLE employees ADD CONSTRAINT chk_birth_date CHECK (date_of_birth < joining_date);
ALTER TABLE payroll_salary_slips ADD CONSTRAINT chk_net_payable CHECK (net_payable >= 0);
ALTER TABLE leave_applications ADD CONSTRAINT chk_leave_dates CHECK (from_date <= to_date);

-- ================================================================
-- 30. FINAL SETUP
-- ================================================================

SET FOREIGN_KEY_CHECKS = 1;

-- ================================================================
-- OPTIMIZATION SUMMARY:
-- ================================================================
-- ✓ Normalized employee data into 6 separate tables
-- ✓ Added proper indexes including composite and full-text
-- ✓ Implemented table partitioning for attendance
-- ✓ Added JSON fields for flexible data storage
-- ✓ Created materialized view patterns for performance
-- ✓ Added stored procedures for common operations
-- ✓ Implemented audit triggers for compliance
-- ✓ Added scheduled maintenance events
-- ✓ Added check constraints for data integrity
-- ✓ Optimized data types and reduced NULL storage
-- ================================================================

SELECT 'HRM Database Optimized Successfully!' AS Status;