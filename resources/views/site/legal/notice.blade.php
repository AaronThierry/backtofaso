@php($contact = config('backtoburkina.contact'))
<x-site.legal title="Mentions légales" description="Éditeur, hébergeur et propriété intellectuelle du site Back to Burkina, un service Travel Express." updated="[date à compléter]"
              :toc="['editeur' => 'Éditeur du site', 'publication' => 'Directeur de la publication', 'hebergement' => 'Hébergement', 'propriete' => 'Propriété intellectuelle', 'responsabilite' => 'Responsabilité', 'contact' => 'Contact']">
    <section id="editeur">
        <h2>Éditeur du site</h2>
        <p>Le site Back to Burkina est édité par <strong>Travel Express</strong>, [forme juridique à compléter], dont le siège est situé à {{ $contact['address'] }}.</p>
        <ul>
            <li>Immatriculation (RCCM) : [à compléter]</li>
            <li>Identifiant financier unique (IFU) : [à compléter]</li>
            <li>E-mail : <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></li>
            @if ($contact['phone'])<li>Téléphone : {{ $contact['phone'] }}</li>@endif
        </ul>
    </section>

    <section id="publication">
        <h2>Directeur de la publication</h2>
        <p>Yikonpin Armel Razak Bakoua, directeur général de Travel Express.</p>
    </section>

    <section id="hebergement">
        <h2>Hébergement</h2>
        <p>Le site est hébergé par <strong>Hostinger International Ltd.</strong>, 61 Lordou Vironos Street, 6023 Larnaca, Chypre — <a href="https://www.hostinger.com" target="_blank" rel="noopener">hostinger.com</a>.</p>
    </section>

    <section id="propriete">
        <h2>Propriété intellectuelle</h2>
        <p>L’ensemble des contenus du site (textes, logo, éléments graphiques, mise en page) est la propriété de Travel Express, sauf mention contraire. Toute reproduction ou réutilisation, totale ou partielle, sans autorisation écrite préalable est interdite.</p>
        <p>Les contenus publiés par les membres (messages, profils, documents) restent sous leur responsabilité.</p>
    </section>

    <section id="responsabilite">
        <h2>Responsabilité</h2>
        <p>Travel Express s’efforce de fournir des informations exactes et à jour, notamment sur les formations, les bourses et les démarches. Ces informations sont données à titre indicatif : les conditions d’admission, de bourse et de visa sont fixées par les universités et les autorités concernées, et peuvent changer.</p>
        <p>Les liens vers d’autres sites sont fournis pour information ; Travel Express n’est pas responsable de leur contenu.</p>
    </section>

    <section id="contact">
        <h2>Contact</h2>
        <p>Pour toute question sur le site, écrivez-nous depuis la <a href="{{ route('contact') }}">page Contact</a> ou à <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>.</p>
    </section>
</x-site.legal>
