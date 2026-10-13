<?php

namespace App\Support;

/** Questions fréquentes, partagées entre l'accueil et la page FAQ */
class Faq
{
    /** @return array<string, array{icon: string, items: array<int, array{0: string, 1: string}>}> */
    public static function all(): array
    {
        return [
            'Partir étudier' => ['icon' => 'plane', 'items' => [
                ['Faut-il parler chinois pour partir ?', 'Pas forcément. Certains programmes sont enseignés en anglais, d’autres commencent par une année de langue chinoise. Votre conseiller vous oriente selon votre profil.'],
                ['Quel niveau faut-il pour être admis ?', 'Il faut en général le Bac pour une licence, une licence pour un master et un master pour un doctorat. Les universités regardent aussi vos notes, votre projet d’études et, selon le programme, votre niveau d’anglais ou de chinois.'],
                ['Combien de temps prend la procédure ?', 'Cela dépend de l’université et de la période de l’année. Votre conseiller vous donne un calendrier précis dès l’ouverture du dossier. Comptez plusieurs mois entre le dépôt du dossier et le départ.'],
                ['Quand faut-il commencer ?', 'Le plus tôt possible. La plupart des rentrées ont lieu en septembre, et les candidatures se font en général entre novembre et avril. Certaines universités proposent aussi une rentrée en mars.'],
            ]],
            'Bourses et frais' => ['icon' => 'graduation', 'items' => [
                ['Existe-t-il des bourses ?', 'Oui : bourses du gouvernement chinois, bourses des provinces et des villes, bourses des universités elles-mêmes, et bourses de langue chinoise. Les conditions changent chaque année ; votre conseiller vous indique celles ouvertes à votre profil.'],
                ['Quels sont les frais d’accompagnement ?', '[À compléter par Travel Express : frais de dossier, frais d’accompagnement, modalités de paiement.]'],
                ['Combien coûte la vie en Chine ?', 'Cela dépend beaucoup de la ville : les grandes métropoles côtières sont plus chères que les villes de l’intérieur. Le logement sur le campus permet de limiter les dépenses. Votre conseiller vous aide à préparer un budget réaliste.'],
            ]],
            'Votre dossier' => ['icon' => 'folder', 'items' => [
                ['Quels documents faut-il préparer ?', 'Un passeport valide, vos diplômes et relevés de notes, un certificat médical, un extrait de casier judiciaire, une lettre de motivation et des photos d’identité. Pour un master ou un doctorat, on demande aussi des lettres de recommandation.'],
                ['Comment suivre l’avancement de mon dossier ?', 'Dans votre espace Back to Burkina, sur le web ou l’application : chaque étape est affichée, vous déposez vos documents en ligne et vous voyez lesquels sont validés.'],
                ['Qu’est-ce que le formulaire JW202 ?', 'C’est le formulaire officiel délivré par l’université après votre admission. Avec la lettre d’admission, il est indispensable pour demander le visa étudiant (X1) à l’ambassade de Chine.'],
            ]],
            'Après les études' => ['icon' => 'briefcase', 'items' => [
                ['Je suis diplômé, comment rejoindre le vivier de talents ?', 'Créez un compte « Diplômé de retour », complétez votre profil et ajoutez votre diplôme. L’équipe Travel Express le vérifie avant de le publier.'],
                ['Les recruteurs voient-ils mes coordonnées ?', 'Votre téléphone et votre e-mail ne sont pas affichés sur votre profil : les recruteurs vous contactent par la messagerie de la plateforme. Attention, si vous ajoutez votre CV, ils peuvent le télécharger avec les informations qu’il contient.'],
            ]],
            'Votre compte' => ['icon' => 'user', 'items' => [
                ['Pourquoi un code par e-mail à chaque connexion ?', 'Pour protéger votre compte et vos documents. Même si quelqu’un connaissait votre mot de passe, il ne pourrait pas se connecter sans le code envoyé à votre adresse.'],
                ['Je ne reçois pas le code, que faire ?', 'Regardez dans les courriers indésirables (spam), puis demandez un nouveau code après une minute. Si le problème continue, contactez-nous.'],
            ]],
        ];
    }

    /** Les questions mises en avant sur l'accueil */
    public static function featured(): array
    {
        $all = self::all();

        return [
            $all['Partir étudier']['items'][0],
            $all['Partir étudier']['items'][2],
            $all['Bourses et frais']['items'][0],
            $all['Après les études']['items'][0],
        ];
    }
}
