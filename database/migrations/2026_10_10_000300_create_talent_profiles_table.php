<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('headline');            // Ingénieure en génie logiciel
            $table->string('field', 40)->index();  // Informatique, Génie civil...
            $table->string('school')->nullable();
            $table->string('school_city')->nullable();
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->json('skills')->nullable();
            $table->json('languages')->nullable();
            $table->string('hsk_level', 10)->nullable();
            $table->string('availability', 20)->default('available'); // available | soon | employed
            $table->string('available_from')->nullable();
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->string('cv_path')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        Schema::create('talent_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('organization')->nullable();
            $table->string('period')->nullable();
            $table->string('kind', 20)->default('education'); // education | work
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talent_experiences');
        Schema::dropIfExists('talent_profiles');
    }
};
