<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    public function test_public_pages_load(): void
    {
        foreach ([
            '/' => 'Revenir bâtir le Faso',
            '/etudier-en-chine' => 'Les pièces à préparer',
            '/entreprises' => 'Recrutez des talents formés en Chine',
            '/questions-frequentes' => 'Qu’est-ce que le formulaire JW202',
            '/contact' => 'Écrivez-nous',
            '/mentions-legales' => 'Éditeur du site',
            '/confidentialite' => 'Vos droits',
            '/conditions-utilisation' => 'Communauté et messagerie',
        ] as $url => $text) {
            $this->get($url)->assertOk()->assertSee($text, false);
        }
    }

    public function test_sitemap_lists_public_pages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('study'), false);
    }

    public function test_unknown_page_shows_branded_404(): void
    {
        $this->get('/page-qui-n-existe-pas')->assertNotFound()->assertSee('Cette page s’est perdue en chemin.', false);
    }

    public function test_contact_form_saves_message_and_notifies_agency(): void
    {
        config(['backtoburkina.contact.email' => 'agence@example.com']);

        $this->post('/contact', [
            'subject' => 'candidature',
            'name' => 'Awa Ouédraogo',
            'email' => 'awa@example.com',
            'message' => 'Bonjour, je voudrais partir en licence d’informatique.',
            'consent' => '1',
        ])->assertRedirect(route('contact'))->assertSessionHas('sent');

        $this->assertDatabaseHas('contact_messages', ['email' => 'awa@example.com', 'subject' => 'candidature']);
        Mail::assertSent(ContactMessageMail::class, fn ($m) => $m->hasTo('agence@example.com') && $m->hasReplyTo('awa@example.com'));
    }

    public function test_contact_form_ignores_bots(): void
    {
        $this->post('/contact', [
            'subject' => 'autre', 'name' => 'Robot', 'email' => 'robot@example.com',
            'message' => 'Achetez nos produits miracles maintenant.', 'consent' => '1',
            'website' => 'https://spam.example.com',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_contact_form_validates_input(): void
    {
        $this->post('/contact', ['subject' => 'autre', 'name' => '', 'email' => 'pas-un-email', 'message' => 'court'])
            ->assertSessionHasErrors(['name', 'email', 'message', 'consent']);

        $this->assertDatabaseCount('contact_messages', 0);
    }
}
