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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // A student may in principle be enrolled more than once (e.g. a
            // later re-admission); each enrollment is its own record with
            // its own admission ID, course and intake.
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();

            // Auto-generated 8-digit ID: 4-digit intake year + 4-digit sequence.
            $table->string('admission_id', 8)->unique();

            $table->foreignUuid('admission_year_id')->constrained('admission_years')->restrictOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->restrictOnDelete();

            $table->string('level');
            $table->string('entry_type');
            $table->enum('semester', ['Spring', 'Summer', 'Autumn']);
            $table->date('declared_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
