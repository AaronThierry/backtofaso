<?php

namespace App\Console\Commands;

use App\Models\TalentProfile;
use Illuminate\Console\Command;

/**
 * Vérification des profils talents en attendant le back-office.
 *   php artisan talents:verify                 liste les profils en attente
 *   php artisan talents:verify 3               vérifie le profil n° 3
 *   php artisan talents:verify jean@exemple.bf vérifie le profil de cette adresse
 *   php artisan talents:verify 3 --retirer     retire la vérification
 */
class VerifyTalents extends Command
{
    protected $signature = 'talents:verify {profil? : Numéro du profil ou e-mail du diplômé} {--retirer : Retirer la vérification}';

    protected $description = 'Lister les profils talents en attente, ou vérifier un profil pour le rendre visible';

    public function handle(): int
    {
        $key = $this->argument('profil');

        if (! $key) {
            $pending = TalentProfile::with('user')->whereNull('verified_at')->orderBy('id')->get();
            if ($pending->isEmpty()) {
                $this->info('Aucun profil en attente de vérification.');

                return self::SUCCESS;
            }

            $this->table(['N°', 'Nom', 'E-mail', 'Titre', 'Pays des études', 'Visible si vérifié'], $pending->map(fn (TalentProfile $p) => [
                $p->id, $p->user->name, $p->user->email, $p->headline, $p->countryName() ?? '—', $p->is_public ? 'oui' : 'non (masqué par le diplômé)',
            ]));
            $this->line('Pour vérifier : php artisan talents:verify <numéro>');

            return self::SUCCESS;
        }

        $profile = is_numeric($key)
            ? TalentProfile::with('user')->find($key)
            : TalentProfile::with('user')->whereHas('user', fn ($q) => $q->where('email', mb_strtolower($key)))->first();

        if (! $profile) {
            $this->error("Aucun profil talent pour « {$key} ».");

            return self::FAILURE;
        }

        $profile->update(['verified_at' => $this->option('retirer') ? null : now()]);

        $this->info($this->option('retirer')
            ? "Vérification retirée : {$profile->user->name} n’est plus visible des recruteurs."
            : "Profil vérifié : {$profile->user->name}".($profile->is_public ? ' est maintenant visible dans le vivier et sur l’accueil.' : ' (il reste masqué tant que le diplômé ne le rend pas visible).'));

        return self::SUCCESS;
    }
}
