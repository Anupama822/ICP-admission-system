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

            // Current course / intake, kept here (as well as on the
            // enrollment record itself) so listings and lookups don't need
            // to join through `enrollments` just to show them.
            $table->foreignUuid('admission_year_id')->constrained('admission_years')->restrictOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->restrictOnDelete();
            $table->string('group');
            $table->string('biometric_id')->nullable();
            $table->string('university_registration_no')->nullable();

            // Personal details.
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('certificate_name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('dob_bs')->nullable();
            $table->date('dob_ad');
            $table->string('citizenship_number')->nullable()->unique();
            $table->string('citizenship_issued_date')->nullable();
            $table->string('passport_number')->nullable()->unique();
            $table->string('passport_issued_date')->nullable();
            $table->string('photo_path')->nullable();

            // Contact details.
            $table->string('permanent_address');
            $table->string('corresponding_address')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email_1')->nullable();
            $table->string('email_2')->nullable();

            // Parent / guardian details.
            $table->string('father_full_name')->nullable();
            $table->string('father_mobile')->nullable();
            $table->string('father_email')->nullable();
            $table->string('mother_full_name')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('guardian_full_name')->nullable();
            $table->string('guardian_contact')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('guardian_email')->nullable();

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
