<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Qualification description rows (the repeatable list under "Qualification
     * Description") only carry an educational board and description/documents,
     * not an institute, so `institute_name` is no longer always submitted.
     */
    public function up(): void
    {
        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->string('institute_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->string('institute_name')->nullable(false)->change();
        });
    }
};
