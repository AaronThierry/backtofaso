<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 20)->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // candidate | student_abroad | graduate | recruiter | agent | admin
            $table->string('role', 20)->default('candidate')->index();
            $table->date('birth_date')->nullable();
            $table->string('city')->nullable();
            $table->string('last_degree', 30)->nullable();
            $table->json('interests')->nullable();
            $table->string('chinese_level', 30)->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('notify_push')->default(true);
            $table->boolean('notify_sms')->default(true);
            $table->boolean('notify_whatsapp')->default(true);
            $table->boolean('notify_email')->default(false);
            $table->timestamp('onboarded_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
