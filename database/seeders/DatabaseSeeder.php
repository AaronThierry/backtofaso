<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Dossier;
use App\Models\DossierDocument;
use App\Models\TalentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Données de démonstration. Mot de passe de tous les comptes : password
     */
    public function run(): void
    {
        $super = Conversation::superGroup();

        // --- Équipe Travel Express ---
        $admin = User::factory()->create([
            'first_name' => 'Admin', 'last_name' => 'Travel Express',
            'phone' => env('ADMIN_PHONE', '+22670000000'),
            'email' => env('ADMIN_EMAIL', 'admin@backtoburkina.bf'),
            'role' => User::ROLE_ADMIN,
        ]);
        $agent = User::factory()->create([
            'first_name' => 'Conseiller', 'last_name' => 'Travel Express',
            'phone' => '+22670000001', 'email' => 'conseil@backtoburkina.bf',
            'role' => User::ROLE_AGENT,
        ]);

        // --- Candidate de démonstration ---
        $awa = User::factory()->create([
            'first_name' => 'Awa', 'last_name' => 'Ouédraogo', 'phone' => '+22670000002', 'email' => 'awa@exemple.bf',
            'role' => User::ROLE_CANDIDATE, 'last_degree' => 'Bac', 'interests' => ['Informatique'],
            'destination' => 'canada', 'language_level' => 'advanced',
        ]);
        $dossier = Dossier::openFor($awa);
        $dossier->update(['advisor_id' => $agent->id, 'program' => 'Licence en informatique', 'country' => 'canada', 'current_step' => 3]);
        $dossier->documents()->where('type', 'passport')->update(['status' => DossierDocument::VALIDATED, 'submitted_at' => now()->subDays(6)]);
        $dossier->documents()->where('type', 'diploma')->update(['status' => DossierDocument::VALIDATED, 'submitted_at' => now()->subDays(6)]);
        $dossier->documents()->where('type', 'transcripts')->update(['status' => DossierDocument::TO_COMPLETE, 'note' => 'Il manque le relevé de terminale.', 'submitted_at' => now()->subDays(3)]);

        // --- Étudiant à l'étranger ---
        $issouf = User::factory()->create([
            'first_name' => 'Issouf', 'last_name' => 'Sawadogo', 'phone' => '+22670000003', 'email' => 'issouf@exemple.bf',
            'role' => User::ROLE_STUDENT_ABROAD, 'destination' => 'chine',
        ]);

        // --- Diplômés du vivier de talents ---
        $talents = [
            ['Mariam', 'Kaboré', 'Commerce', 'Master en commerce international', 'chine', 'HSK 6', ['Français', 'Chinois', 'Anglais'], 'available', ['Négociation', 'Import-export', 'Logistique']],
            ['Boukary', 'Traoré', 'Santé', 'Médecin généraliste', 'maroc', null, ['Français', 'Arabe'], 'available', ['Médecine générale', 'Urgences']],
            ['Salif', 'Zongo', 'Génie civil', 'Ingénieur génie civil', 'france', null, ['Français', 'Anglais'], 'soon', ['Béton armé', 'AutoCAD', 'Suivi de chantier']],
            ['Aminata', 'Compaoré', 'Informatique', 'Ingénieure en génie logiciel', 'canada', 'IELTS 7', ['Français', 'Anglais'], 'available', ['Développement web', 'Applications mobiles', 'Bases de données']],
        ];
        $graduates = [];
        foreach ($talents as $i => [$first, $last, $field, $headline, $country, $cert, $languages, $availability, $skills]) {
            $user = User::factory()->create([
                'first_name' => $first, 'last_name' => $last, 'phone' => '+2267000001'.$i, 'email' => 'talent'.($i + 1).'@exemple.bf',
                'role' => User::ROLE_GRADUATE,
            ]);
            $profile = TalentProfile::create([
                'user_id' => $user->id, 'headline' => $headline, 'field' => $field,
                'school' => '[Université]', 'school_city' => '[Ville]', 'study_country' => $country, 'graduation_year' => 2025,
                'skills' => $skills, 'languages' => $languages, 'language_cert' => $cert,
                'availability' => $availability, 'available_from' => $availability === 'soon' ? '[mois]' : null,
                'location' => 'Bobo-Dioulasso', 'verified_at' => now(),
                'bio' => 'Diplômé(e) à l’étranger, de retour au Burkina et disponible pour des missions dans mon domaine.',
            ]);
            $profile->experiences()->createMany([
                ['title' => $headline, 'organization' => '[Université], [Ville], '.\App\Support\Destinations::name($country), 'period' => '2021 – 2025', 'kind' => 'education', 'position' => 0],
                ['title' => 'Stage', 'organization' => '[Entreprise], [Ville]', 'period' => '6 mois', 'kind' => 'work', 'position' => 1],
            ]);
            $graduates[] = $user;
        }

        $recruiter = User::factory()->create([
            'first_name' => 'Recruteur', 'last_name' => 'Démo', 'phone' => '+22670000020', 'email' => 'recruteur@exemple.bf',
            'role' => User::ROLE_RECRUITER,
        ]);

        // --- Super groupe : tout le monde ---
        $all = User::all();
        $super->users()->syncWithoutDetaching($all->mapWithKeys(fn ($u) => [
            $u->id => ['role' => $u->isStaff() ? 'admin' : 'member'],
        ])->all());

        $super->messages()->create(['user_id' => $agent->id, 'topic' => 'annonces', 'is_pinned' => true,
            'body' => 'Les inscriptions pour la rentrée sont ouvertes. Consultez le calendrier des dépôts de dossier.']);
        $super->messages()->create(['user_id' => $agent->id, 'topic' => 'annonces',
            'body' => 'Séance d’information à l’agence de Bobo-Dioulasso le [date] à [heure]. Ouverte aux parents.']);
        $super->messages()->create(['user_id' => $issouf->id, 'topic' => 'entraide',
            'body' => 'Quelqu’un sait combien de temps l’université met à envoyer les documents pour le visa après l’admission ?']);
        $super->messages()->create(['user_id' => $graduates[0]->id, 'topic' => 'entraide',
            'body' => 'Pour moi c’était quelques semaines. Demande à ton conseiller de relancer l’université.']);
        $super->messages()->create(['user_id' => $recruiter->id, 'topic' => 'emplois',
            'body' => 'Nous recrutons un développeur web à Bobo-Dioulasso. Anglais apprécié. Postulez via le vivier de talents.']);
        $super->messages()->create(['user_id' => $admin->id, 'topic' => 'bourses',
            'body' => 'Nouvelle bourse ouverte : [nom de la bourse], date limite le [date].']);

        // --- Un groupe privé ---
        $group = Conversation::create(['type' => Conversation::GROUP, 'name' => 'Étudiants à [Ville]', 'created_by' => $issouf->id]);
        $group->users()->attach([$issouf->id => ['role' => 'admin'], $awa->id => ['role' => 'member'], $graduates[0]->id => ['role' => 'member']]);
        $group->messages()->create(['user_id' => $issouf->id, 'body' => 'Bienvenue à tous ! Qui a déjà ouvert un compte bancaire ici ?']);

        // --- Conversation privée conseiller ↔ Awa ---
        $direct = Conversation::create(['type' => Conversation::DIRECT, 'created_by' => $agent->id]);
        $direct->users()->attach([$agent->id, $awa->id]);
        $direct->messages()->create(['user_id' => $agent->id, 'body' => 'Bonjour Awa, votre passeport et votre diplôme ont bien été validés.']);
    }
}
