<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `subject` is renamed to `faculty` to match the new terminology, and
     * every qualification can now carry a free-text description plus a flag
     * marking the single "highest qualification" entry.
     */
    public function up(): void
    {
        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->renameColumn('subject', 'faculty');
        });

        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->text('qualification_description')->nullable()->after('score_type');
            $table->boolean('is_highest')->default(false)->after('qualification_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->dropColumn(['qualification_description', 'is_highest']);
        });

        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->renameColumn('faculty', 'subject');
        });
    }
};
