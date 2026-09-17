<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The highest qualification only collects the educational board and
     * awarding body now, so `faculty` is no longer always submitted.
     */
    public function up(): void
    {
        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->string('faculty')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_qualifications', function (Blueprint $table) {
            $table->string('faculty')->nullable(false)->change();
        });
    }
};
