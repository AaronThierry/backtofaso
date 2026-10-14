<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La plateforme couvre toutes les destinations, plus seulement la Chine :
 *  - users : pays visé (destination) ; « niveau de chinois » devient un niveau dans la langue d'enseignement
 *  - dossiers : pays de destination
 *  - talent_profiles : pays des études ; « niveau HSK » devient une certification de langue libre
 *  - domaine « Langue chinoise » renommé « Langues et traduction »
 */
return new class extends Migration
{
    private const LEVELS = ['none' => 'beginner', 'hsk1-2' => 'beginner', 'hsk3-4' => 'intermediate', 'hsk5-6' => 'advanced'];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('destination', 40)->nullable()->after('interests');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('chinese_level', 'language_level');
        });
        foreach (self::LEVELS as $old => $new) {
            DB::table('users')->where('language_level', $old)->update(['language_level' => $new]);
        }

        Schema::table('dossiers', function (Blueprint $table) {
            $table->string('country', 40)->nullable()->after('university');
        });

        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->string('study_country', 40)->nullable()->after('school_city');
        });
        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->renameColumn('hsk_level', 'language_cert');
        });
        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->string('language_cert', 60)->nullable()->change();
        });
        // Les profils existants ont tous été formés en Chine
        DB::table('talent_profiles')->whereNull('study_country')->update(['study_country' => 'chine']);

        $this->renameField('Langue chinoise', 'Langues et traduction');
    }

    public function down(): void
    {
        $this->renameField('Langues et traduction', 'Langue chinoise');

        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->renameColumn('language_cert', 'hsk_level');
        });
        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->dropColumn('study_country');
        });
        Schema::table('dossiers', function (Blueprint $table) {
            $table->dropColumn('country');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('language_level', 'chinese_level');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('destination');
        });
    }

    private function renameField(string $from, string $to): void
    {
        DB::table('talent_profiles')->where('field', $from)->update(['field' => $to]);

        DB::table('users')->whereNotNull('interests')->orderBy('id')->each(function ($user) use ($from, $to) {
            $interests = json_decode($user->interests, true);
            if (is_array($interests) && in_array($from, $interests, true)) {
                $interests = array_map(fn ($i) => $i === $from ? $to : $i, $interests);
                DB::table('users')->where('id', $user->id)->update(['interests' => json_encode($interests)]);
            }
        });
    }
};
