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
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('type_id')->nullable()->constrained()->after('user_id');
            $table->foreignId('year_id')->nullable()->constrained()->after('type_id');
            $table->foreignId('demographic_id')->nullable()->constrained()->after('year_id');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropForeign(['year_id']);
            $table->dropForeign(['demographic_id']);
            $table->dropColumn(['type_id', 'year_id', 'demographic_id']);
        });
    }
};
