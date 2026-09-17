<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each qualification can now carry several scanned image documents
     * instead of the single `document_path` it used to have.
     */
    public function up(): void
    {
        Schema::create('student_qualification_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_qualification_id')->constrained('student_qualifications')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_filename');
            $table->timestamps();
        });

        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->dropColumn('document_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->string('document_path')->nullable();
        });

        Schema::dropIfExists('student_qualification_documents');
    }
};
