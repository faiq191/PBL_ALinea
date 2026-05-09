<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->enum('status', ['pending', 'dipinjam', 'dikembalikan', 'ditolak'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->enum('status', ['pending', 'dipinjam', 'dikembalikan'])->default('pending')->change();
        });
    }
};