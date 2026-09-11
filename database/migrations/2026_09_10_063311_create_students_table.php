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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Auto-generated 8-digit ID: 4-digit intake year + 4-digit sequence.
            $table->string('admission_id', 8)->unique();

            $table->foreignUuid('admission_year_id')->constrained('admission_years')->restrictOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->restrictOnDelete();

            // Personal details.
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('certificate_name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('dob_bs')->nullable();
            $table->date('dob_ad');
            $table->string('citizenship_number')->nullable()->unique();
            $table->date('citizenship_issued_date')->nullable();
            $table->string('passport_number')->nullable()->unique();
            $table->date('passport_issued_date')->nullable();
            $table->date('declared_date');
            $table->string('photo_path')->nullable();

            // Course / intake details.
            $table->string('level');
            $table->string('entry_type');
            $table->enum('semester', ['Spring', 'Summer', 'Autumn']);
            $table->string('group');
            $table->string('biometric_id')->nullable();
            $table->string('university_registration_no')->nullable();

            // Contact details.
            $table->string('permanent_address');
            $table->string('corresponding_address');
            $table->string('mobile');
            $table->string('email_1');
            $table->string('email_2')->nullable();

            // Parent / guardian details.
            $table->string('father_full_name');
            $table->string('father_mobile');
            $table->string('father_email')->nullable();
            $table->string('mother_full_name');
            $table->string('mother_mobile');
            $table->string('mother_email')->nullable();
            $table->string('guardian_full_name')->nullable();
            $table->string('guardian_contact')->nullable();
            $table->string('guardian_email')->nullable();

            // Academic summary (the "primary" qualification).
            $table->string('highest_qualification');
            $table->string('awarding_body');
            $table->text('qualification_description')->nullable();

            // Medical background.
            $table->boolean('has_disorder')->default(false);
            $table->boolean('is_drug_abuser')->default(false);
            $table->boolean('has_criminal_record')->default(false);
            $table->boolean('has_communicable_disease')->default(false);
            $table->boolean('is_minor_requiring_consent')->default(false);

            $table->string('signature_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
