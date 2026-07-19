<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->enum('type', ['Earning', 'Deduction', 'Reimbursement', 'Bonus']);
            $table->enum('category', ['Basic', 'Allowance', 'Bonus', 'PF', 'Tax', 'Insurance', 'Loan', 'Other'])->default('Other');
            $table->enum('calculation_type', ['Fixed', 'Percentage of Basic', 'Percentage of Gross', 'Formula', 'Custom'])->default('Fixed');
            $table->decimal('default_value', 14, 4)->default(0);
            $table->text('formula_expression')->nullable()->comment('For dynamic calculation');
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_pf_basis')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_slip')->default(true);
            $table->integer('display_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('type', 'idx_component_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};