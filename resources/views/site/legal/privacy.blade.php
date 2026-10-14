@php($contact = config('backtoburkina.contact'))
<x-site.legal title="Politique de confidentialité" description="Quelles données Back to Burkina collecte, pourquoi, combien de temps elles sont conservées et comment exercer vos droits." updated="[date à compléter]"
              :toc="['responsable' => 'Responsable du traitement', 'donnees' => 'Données collectées', 'finalites' => 'Utilisation', 'partage' => 'Partage', 'conservation' => 'Conservation', 'securite' => 'Sécurité', 'droits' => 'Vos droits', 'cookies' => 'Cookies']">
    <section id="responsable">
        <h2>Responsable du traitement</h2>
        <p>Les données personnelles collectées sur Back to Burkina sont traitées par <strong>Travel Express</strong>, {{ $contact['address'] }}. Pour toute question : <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>.</p>
        <p>Ce traitement respecte la loi burkinabè sur la protection des données à caractère personnel, sous le contrôle de la Commission de l’informatique et des libertés (CIL). [Numéro de déclaration ou d’autorisation CIL à compléter.]</p>
    </section>

    <section id="donnees">
        <h2>Données collectées</h2>
        <ul>
            <li><strong>Compte</strong> : nom, prénom, adresse e-mail, téléphone, mot de passe (enregistré chiffré), profil choisi.</li>
            <li><strong>Profil</strong> : date de naissance, ville, diplôme, domaines d’intérêt, pays visé, niveau de langue, photo (facultative), préférences de notification.</li>
            <li><strong>Dossier de candidature</strong> : documents déposés (passeport, diplômes, relevés de notes, etc.) et suivi des étapes.</li>
            <li><strong>Profil talent</strong> (diplômés) : parcours, compétences, langues, disponibilité, CV.</li>
            <li><strong>Messagerie</strong> : messages et fichiers échangés dans la communauté et les conversations.</li>
            <li><strong>Formulaire de contact</strong> : nom, e-mail, téléphone (facultatif) et message.</li>
            <li><strong>Données techniques</strong> : adresse IP et journaux de connexion, pour la sécurité du service.</li>
        </ul>
    </section>

    <section id="finalites">
        <h2>Utilisation de vos données</h2>
        <ul>
            <li>Créer et sécuriser votre compte (code de vérification envoyé par e-mail à l’inscription et à chaque connexion).</li>
            <li>Constituer et suivre votre dossier de candidature auprès des universités.</li>
            <li>Vous permettre d’échanger avec votre conseiller et avec la communauté.</li>
            <li>Présenter votre profil aux recruteurs, si vous êtes diplômé et que vous le rendez visible.</li>
            <li>Répondre à vos demandes envoyées depuis le formulaire de contact.</li>
            <li>Vous informer de l’avancement de votre dossier, selon les canaux que vous avez choisis.</li>
        </ul>
    </section>

    <section id="partage">
        <h2>Partage des données</h2>
        <p>Vos données ne sont jamais vendues. Elles sont accessibles :</p>
        <ul>
            <li>à l’équipe Travel Express, pour le suivi de votre dossier ;</li>
            <li>aux universités et organismes de bourses auxquels votre candidature est transmise, avec votre accord ;</li>
            <li>aux recruteurs inscrits, pour les seuls profils talents rendus visibles et vérifiés (votre téléphone et votre e-mail ne sont pas affichés) ;</li>
            <li>aux prestataires techniques nécessaires au service (hébergement, envoi des e-mails), tenus à la confidentialité.</li>
        </ul>
    </section>

    <section id="conservation">
        <h2>Durée de conservation</h2>
        <p>Les données du compte et du dossier sont conservées tant que votre compte est actif, puis [durée à compléter] après sa suppression ou votre dernière connexion. Les codes de vérification expirent après quelques minutes. Les messages du formulaire de contact sont conservés [durée à compléter].</p>
    </section>

    <section id="securite">
        <h2>Sécurité</h2>
        <p>Les échanges avec le site sont chiffrés (HTTPS), les mots de passe sont enregistrés sous forme chiffrée, et l’accès au compte demande un code envoyé par e-mail. Les documents de dossier sont stockés dans un espace privé, non publié sur le site.</p>
    </section>

    <section id="droits">
        <h2>Vos droits</h2>
        <p>Vous pouvez à tout moment accéder à vos données, les faire corriger ou supprimer, vous opposer à leur utilisation ou demander à en recevoir une copie. Écrivez-nous depuis la <a href="{{ route('contact') }}">page Contact</a> ou à <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a> ; nous pourrons vous demander de justifier de votre identité.</p>
        <p>Vous pouvez aussi adresser une réclamation à la Commission de l’informatique et des libertés (CIL) du Burkina Faso.</p>
    </section>

    <section id="cookies">
        <h2>Cookies</h2>
        <p>Le site utilise uniquement les cookies nécessaires à son fonctionnement : maintien de votre connexion et protection des formulaires. Aucun cookie publicitaire ni de mesure d’audience tierce n’est déposé.</p>
    </section>
</x-site.legal>
