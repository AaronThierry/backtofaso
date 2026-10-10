<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('program')->nullable();
            $table->string('university')->nullable();
            $table->string('city')->nullable();
            $table->unsignedSmallInteger('intake_year')->nullable();
            // 1 Inscription, 2 Documents, 3 Admission, 4 JW202, 5 Visa, 6 Départ
            $table->unsignedTinyInteger('current_step')->default(2);
            $table->string('status', 20)->default('open'); // open | completed | cancelled
            $table->timestamps();
        });

        Schema::create('dossier_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40); // passport, diploma, transcripts, medical...
            $table->string('label');
            // missing | pending | to_complete | validated
            $table->string('status', 20)->default('missing');
            $table->string('path')->nullable();
            $table->string('original_name')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['dossier_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossier_documents');
        Schema::dropIfExists('dossiers');
    }
};
