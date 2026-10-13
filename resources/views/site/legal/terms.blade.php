@php($contact = config('backtoburkina.contact'))
<x-site.legal title="Conditions d’utilisation" description="Les règles d’utilisation de la plateforme Back to Burkina : compte, dossier, communauté, vivier de talents et responsabilités." updated="[date à compléter]"
              :toc="['objet' => 'Objet', 'compte' => 'Votre compte', 'accompagnement' => 'Accompagnement', 'communaute' => 'Communauté', 'talents' => 'Vivier de talents', 'contenus' => 'Vos contenus', 'responsabilite' => 'Responsabilité', 'modification' => 'Modifications']">
    <section id="objet">
        <h2>Objet</h2>
        <p>Les présentes conditions encadrent l’utilisation de la plateforme Back to Burkina, éditée par Travel Express, sur le web et sur l’application. Créer un compte vaut acceptation de ces conditions.</p>
    </section>

    <section id="compte">
        <h2>Votre compte</h2>
        <ul>
            <li>Vous vous engagez à fournir des informations exactes et à les tenir à jour.</li>
            <li>Votre compte est personnel. Vous êtes responsable de la confidentialité de votre mot de passe et des codes reçus par e-mail : ne les communiquez à personne.</li>
            <li>Prévenez-nous sans attendre si vous pensez que quelqu’un utilise votre compte.</li>
        </ul>
    </section>

    <section id="accompagnement">
        <h2>Accompagnement</h2>
        <p>Travel Express vous aide à préparer et à suivre votre candidature. L’admission, l’attribution d’une bourse et la délivrance du visa relèvent des universités, des organismes de bourses et des autorités consulaires : Travel Express ne peut pas les garantir.</p>
        <p>Les conditions financières de l’accompagnement font l’objet d’un accord séparé. [À compléter par Travel Express.]</p>
    </section>

    <section id="communaute">
        <h2>Communauté et messagerie</h2>
        <p>La communauté est un espace d’entraide. En y participant, vous vous engagez à rester courtois et à ne publier :</p>
        <ul>
            <li>aucun contenu injurieux, discriminatoire, violent ou illégal ;</li>
            <li>aucune publicité ou sollicitation non désirée ;</li>
            <li>aucune information personnelle sur une autre personne sans son accord ;</li>
            <li>aucune fausse offre d’emploi ou de bourse.</li>
        </ul>
        <p>Les thèmes Annonces et Bourses sont réservés à l’équipe Travel Express. L’équipe peut retirer un contenu ou suspendre un compte qui ne respecte pas ces règles.</p>
    </section>

    <section id="talents">
        <h2>Vivier de talents</h2>
        <p>Les diplômés peuvent rendre leur profil visible des recruteurs. Il n’apparaît qu’après vérification par Travel Express. Les recruteurs s’engagent à n’utiliser les profils que pour des offres réelles et à contacter les candidats par la messagerie de la plateforme.</p>
    </section>

    <section id="contenus">
        <h2>Vos contenus</h2>
        <p>Vous restez propriétaire des documents, messages et informations que vous publiez. Vous autorisez Travel Express à les utiliser dans la seule mesure nécessaire au service (traitement de votre dossier, affichage de votre profil, fonctionnement de la messagerie).</p>
    </section>

    <section id="responsabilite">
        <h2>Responsabilité</h2>
        <p>Travel Express met tout en œuvre pour assurer le bon fonctionnement de la plateforme, sans pouvoir garantir une disponibilité permanente. Chaque membre est responsable des contenus qu’il publie.</p>
    </section>

    <section id="modification">
        <h2>Modifications</h2>
        <p>Ces conditions peuvent évoluer. En cas de changement important, les membres en sont informés. Pour toute question : <a href="{{ route('contact') }}">page Contact</a> ou <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>.</p>
    </section>
</x-site.legal>
