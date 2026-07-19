<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('designation');
            $table->unsignedInteger('kpi_score')->default(0);
            $table->string('status')->default(Employee::STATUS_ACTIVE);
            $table->timestamps();

            $table->index('status');
            $table->index('kpi_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
