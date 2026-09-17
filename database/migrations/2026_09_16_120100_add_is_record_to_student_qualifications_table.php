<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distinguishes the "Academic Qualifications" rows (Document Type fixed
     * to Academic, with faculty/institute/score) from the "Qualification
     * Description" rows (their own educational board, description, images).
     * Both are `is_highest = false`.
     */
    public function up(): void
    {
        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->boolean('is_record')->default(false)->after('is_highest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->dropColumn('is_record');
        });
    }
};
