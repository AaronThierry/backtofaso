<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\User;
use App\Support\Phone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Création du compte super-administrateur (ou promotion d'un compte existant).
 *   php artisan admin:create
 *   php artisan admin:create --promote=jean@exemple.bf
 *   php artisan admin:create --role=agent      (compte conseiller)
 * Le mot de passe est demandé de façon masquée. L'adresse doit être une vraie boîte :
 * chaque connexion demande un code envoyé par e-mail.
 */
class CreateAdmin extends Command
{
    protected $signature = 'admin:create {--promote= : E-mail d’un compte existant à promouvoir} {--role=admin : admin ou agent (conseiller)}';

    protected $description = 'Créer le compte administrateur (ou conseiller) de l’équipe Travel Express';

    public function handle(): int
    {
        $role = $this->option('role');
        if (! in_array($role, [User::ROLE_ADMIN, User::ROLE_AGENT], true)) {
            $this->error('Rôle inconnu : utilisez --role=admin ou --role=agent.');

            return self::FAILURE;
        }

        if ($email = $this->option('promote')) {
            $user = User::where('email', mb_strtolower(trim($email)))->first();
            if (! $user) {
                $this->error("Aucun compte avec l’adresse {$email}.");

                return self::FAILURE;
            }
            $user->forceFill(['role' => $role, 'onboarded_at' => $user->onboarded_at ?? now()])->save();
            $this->joinCommunity($user);
            $this->info("{$user->name} est maintenant ".($role === User::ROLE_ADMIN ? 'administrateur' : 'conseiller').'.');

            return self::SUCCESS;
        }

        $this->line('Création d’un compte '.($role === User::ROLE_ADMIN ? 'administrateur' : 'conseiller').'.');
        $this->warn('Utilisez une adresse e-mail réelle : chaque connexion demande un code envoyé à cette adresse.');

        $data = [
            'first_name' => $this->ask('Prénom'),
            'last_name' => $this->ask('Nom'),
            'email' => mb_strtolower(trim((string) $this->ask('Adresse e-mail'))),
            'phone' => Phone::normalize((string) $this->ask('Téléphone (ex. 70 00 00 00)')),
            'password' => $this->secret('Mot de passe (8 caractères minimum, dont un chiffre)'),
        ];
        $confirmation = $this->secret('Confirmez le mot de passe');

        $validator = Validator::make($data + ['password_confirmation' => $confirmation], [
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'regex:/^\+\d{8,15}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->numbers()],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $user = User::forceCreate([
            ...$data,
            'password' => Hash::make($data['password']),
            'role' => $role,
            'email_verified_at' => now(),
            'onboarded_at' => now(),
        ]);
        $this->joinCommunity($user);

        $this->info("Compte créé : {$user->email}. Connectez-vous sur ".url('/connexion').', puis ouvrez '.url('/admin').'.');

        return self::SUCCESS;
    }

    private function joinCommunity(User $user): void
    {
        Conversation::superGroup()->users()->syncWithoutDetaching([$user->id => ['role' => 'admin']]);
    }
}
