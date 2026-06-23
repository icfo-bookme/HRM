<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Models\SkillCategory;

class SkillCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Programming Languages', 'description' => 'PHP, JavaScript, Python, Java, C#, C++, TypeScript, Ruby, Go, Rust', 'is_active' => 1],
            ['name' => 'Web Development', 'description' => 'Laravel, React, Vue.js, Angular, Node.js, Django, ASP.NET, jQuery', 'is_active' => 1],
            ['name' => 'Database Management', 'description' => 'MySQL, PostgreSQL, MongoDB, Oracle, SQL Server, Redis, Elasticsearch', 'is_active' => 1],
            ['name' => 'DevOps & Cloud', 'description' => 'Docker, Kubernetes, AWS, Azure, Google Cloud, CI/CD, Jenkins, Terraform', 'is_active' => 1],
            ['name' => 'Mobile Development', 'description' => 'Flutter, React Native, Android (Kotlin), iOS (Swift), Ionic', 'is_active' => 1],
            ['name' => 'Networking & Security', 'description' => 'Network Administration, Cybersecurity, Firewall, VPN, Penetration Testing', 'is_active' => 1],
            ['name' => 'UI/UX Design', 'description' => 'Figma, Adobe XD, Sketch, Wireframing, Prototyping, User Research', 'is_active' => 1],
            ['name' => 'Project Management', 'description' => 'PMP, Agile, Scrum, Kanban, JIRA, Risk Management, Stakeholder Management', 'is_active' => 1],
            ['name' => 'Accounting & Finance', 'description' => 'Financial Reporting, Budgeting, Taxation, Audit, QuickBooks, ERP', 'is_active' => 1],
            ['name' => 'Human Resources', 'description' => 'Recruitment, Payroll Management, Performance Management, Training & Development', 'is_active' => 1],
            ['name' => 'Marketing & Sales', 'description' => 'Digital Marketing, SEO, SEM, Social Media Marketing, CRM, Brand Management', 'is_active' => 1],
            ['name' => 'Supply Chain & Logistics', 'description' => 'Inventory Management, Procurement, Warehousing, Shipping, ERP Systems', 'is_active' => 1],
            ['name' => 'Data Science & Analytics', 'description' => 'Machine Learning, Python, R, Tableau, Power BI, Statistics, Big Data', 'is_active' => 1],
            ['name' => 'Communication', 'description' => 'Written Communication, Verbal Communication, Presentation Skills, Negotiation', 'is_active' => 1],
            ['name' => 'Leadership', 'description' => 'Team Management, Decision Making, Mentoring, Strategic Planning, Conflict Resolution', 'is_active' => 1],
            ['name' => 'Quality Assurance', 'description' => 'Manual Testing, Automated Testing, Selenium, Test Planning, Bug Tracking', 'is_active' => 1],
            ['name' => 'Graphic Design', 'description' => 'Adobe Photoshop, Illustrator, InDesign, Canva, Motion Graphics, Video Editing', 'is_active' => 1],
            ['name' => 'Microsoft Office', 'description' => 'Excel (Advanced), PowerPoint, Word, Outlook, SharePoint, VBA Macros', 'is_active' => 1],
            ['name' => 'ERP & Business Systems', 'description' => 'SAP, Oracle ERP, Odoo, Tally, Salesforce, Zoho', 'is_active' => 1],
            ['name' => 'Customer Service', 'description' => 'Call Center, Help Desk, Ticketing Systems, Client Relationship, Complaint Handling', 'is_active' => 1],
        ];

        foreach ($categories as $category) {
            SkillCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Skill categories seeded successfully!');
    }
}