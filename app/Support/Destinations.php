<?php

namespace App\Support;

/**
 * Pays de destination des études. La clé (slug) est enregistrée en base
 * (users.destination, dossiers.country, talent_profiles.study_country).
 */
class Destinations
{
    public const OTHER = 'autre';

    /** @return array<string, array{name: string, code: string, languages: string, about: string, scholarships: string}> */
    public static function all(): array
    {
        return [
            'chine' => [
                'name' => 'Chine', 'code' => 'CN', 'languages' => 'Chinois, anglais',
                'about' => 'Des universités reconnues en ingénierie, médecine et sciences ; une année de langue est souvent proposée avant le cursus.',
                'scholarships' => 'Bourses du gouvernement chinois (CSC), des provinces et des universités',
            ],
            'france' => [
                'name' => 'France', 'code' => 'FR', 'languages' => 'Français',
                'about' => 'Études en français, universités publiques et grandes écoles. La candidature passe par la procédure Études en France de Campus France.',
                'scholarships' => 'Bourses du gouvernement français et des établissements',
            ],
            'canada' => [
                'name' => 'Canada', 'code' => 'CA', 'languages' => 'Français, anglais',
                'about' => 'Universités et collèges en français (Québec) ou en anglais. Pour le Québec, un certificat d’acceptation (CAQ) précède le permis d’études.',
                'scholarships' => 'Bourses des universités et des provinces',
            ],
            'maroc' => [
                'name' => 'Maroc', 'code' => 'MA', 'languages' => 'Français, arabe',
                'about' => 'Une destination proche et francophone, avec de nombreux étudiants d’Afrique de l’Ouest.',
                'scholarships' => 'Bourses de coopération de l’AMCI',
            ],
            'turquie' => [
                'name' => 'Turquie', 'code' => 'TR', 'languages' => 'Turc, anglais',
                'about' => 'Des universités modernes ; les boursiers suivent généralement une année de turc avant leur formation.',
                'scholarships' => 'Bourses du gouvernement turc (Türkiye Bursları)',
            ],
            'russie' => [
                'name' => 'Russie', 'code' => 'RU', 'languages' => 'Russe, anglais',
                'about' => 'Formations réputées en médecine et en ingénierie, souvent précédées d’une année préparatoire de russe.',
                'scholarships' => 'Bourses du gouvernement russe',
            ],
            'allemagne' => [
                'name' => 'Allemagne', 'code' => 'DE', 'languages' => 'Allemand, anglais',
                'about' => 'Des universités publiques aux frais de scolarité faibles ou nuls dans la plupart des régions, fortes en ingénierie.',
                'scholarships' => 'Bourses du DAAD et des fondations',
            ],
            'belgique' => [
                'name' => 'Belgique', 'code' => 'BE', 'languages' => 'Français, néerlandais',
                'about' => 'Des universités francophones à taille humaine, réputées en sciences, santé et agronomie.',
                'scholarships' => 'Bourses de coopération et des universités',
            ],
            'etats-unis' => [
                'name' => 'États-Unis', 'code' => 'US', 'languages' => 'Anglais',
                'about' => 'Un large choix d’universités et de programmes, avec des tests d’anglais et parfois d’admission à préparer.',
                'scholarships' => 'Bourses des universités, programme Fulbright',
            ],
            'senegal' => [
                'name' => 'Sénégal', 'code' => 'SN', 'languages' => 'Français',
                'about' => 'Universités et grandes écoles de la sous-région, en français et proches de chez vous.',
                'scholarships' => 'Bourses des établissements et de coopération régionale',
            ],
        ];
    }

    /** Liste pour les menus déroulants : [slug => nom], « Autre pays » en dernier */
    public static function options(): array
    {
        return array_map(fn ($d) => $d['name'], self::all()) + [self::OTHER => 'Autre pays'];
    }

    public static function name(?string $slug): ?string
    {
        return $slug ? (self::options()[$slug] ?? null) : null;
    }
}
