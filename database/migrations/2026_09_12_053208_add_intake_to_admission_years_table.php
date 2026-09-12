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
        Schema::table('admission_years', function (Blueprint $table) {
            $table->enum('intake', ['Spring', 'Autumn'])->default('Spring')->after('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_years', function (Blueprint $table) {
            $table->dropColumn('intake');
        });
    }
};
