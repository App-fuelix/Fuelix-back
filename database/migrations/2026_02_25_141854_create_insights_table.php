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
        Schema::create('insights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('dashboard_id');
            $table->string('type');
            $table->text('description');
            $table->dateTime('generated_at');
            $table->timestamps();
            
            $table->foreign('dashboard_id')->references('id')->on('dashboards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};
