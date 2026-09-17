<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * An admin-set example of how a qualification description should be
     * written for this educational board, shown as a hint on the student
     * form once the board is selected.
     */
    public function up(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->text('format_hint')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn('format_hint');
        });
    }
};
