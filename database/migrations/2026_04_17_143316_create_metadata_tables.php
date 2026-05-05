<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('demographics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('years', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadata_tables');
    }
};
