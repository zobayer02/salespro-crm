<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reengagement_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('customer_assignment_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('channel', 20)->default('email')->index();
            $table->string('status', 30)->default('sent')->index();
            $table->string('subject');
            $table->text('message');
            $table->text('failure_reason')->nullable();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reengagement_logs');
    }
};
