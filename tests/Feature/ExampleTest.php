<?php

namespace Tests\Feature;

use App\Mail\OtpCodeMail;
use App\Models\Conversation;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    /** Dernier code envoyé par e-mail à cette adresse */
    private function lastCodeSentTo(string $email): string
    {
        return Mail::sent(OtpCodeMail::class, fn ($m) => $m->hasTo($email))->last()->code;
    }

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('Revenir bâtir le Faso', false);
    }

    public function test_registration_requires_email_code_before_creating_account(): void
    {
        $this->post('/inscription', [
            'role' => User::ROLE_CANDIDATE,
            'first_name' => 'Awa',
            'last_name' => 'Ouédraogo',
            'email' => 'Awa@Example.com',
            'phone' => '70 11 22 33',
            'password' => 'motdepasse1',
            'terms' => '1',
        ])->assertRedirect(route('register.verify'));

        // Pas de compte tant que le code n'est pas saisi
        $this->assertDatabaseMissing('users', ['email' => 'awa@example.com']);
        $this->assertGuest();

        $code = $this->lastCodeSentTo('awa@example.com');
        Mail::assertSent(OtpCodeMail::class, fn ($m) => $m->purpose === OtpService::REGISTER);

        $this->post('/inscription/verification', ['code' => '000000' === $code ? '111111' : '000000'])->assertSessionHasErrors('code');
        $this->post('/inscription/verification', ['code' => $code])->assertRedirect(route('onboarding.profile'));

        $user = User::where('email', 'awa@example.com')->firstOrFail();
        $this->assertSame('+22670112233', $user->phone);
        $this->assertNotNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Conversation::superGroup()->hasMember($user));
        $this->assertNotNull($user->dossier);
    }

    public function test_registration_requires_an_email(): void
    {
        $this->post('/inscription', [
            'role' => User::ROLE_CANDIDATE,
            'first_name' => 'Awa',
            'last_name' => 'Ouédraogo',
            'phone' => '70 11 22 33',
            'password' => 'motdepasse1',
            'terms' => '1',
        ])->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    public function test_login_requires_password_then_email_code(): void
    {
        $user = User::factory()->create(['email' => 'issouf@example.com', 'password' => Hash::make('motdepasse1')]);

        $this->post('/connexion', ['email' => 'issouf@example.com', 'password' => 'motdepasse1'])
            ->assertRedirect(route('login.verify'));

        // Le mot de passe seul n'ouvre pas la session
        $this->assertGuest();
        $this->get('/accueil')->assertRedirect(route('login'));

        $code = $this->lastCodeSentTo('issouf@example.com');
        Mail::assertSent(OtpCodeMail::class, fn ($m) => $m->purpose === OtpService::LOGIN);

        $this->post('/connexion/verification', ['code' => $code])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_wrong_password_sends_no_code(): void
    {
        User::factory()->create(['email' => 'issouf@example.com', 'password' => Hash::make('motdepasse1')]);

        $this->post('/connexion', ['email' => 'issouf@example.com', 'password' => 'mauvais'])
            ->assertSessionHasErrors('email');

        Mail::assertNothingSent();
        $this->assertGuest();
    }

    public function test_registration_code_cannot_be_used_to_log_in(): void
    {
        $user = User::factory()->create(['email' => 'issouf@example.com', 'password' => Hash::make('motdepasse1')]);
        $registerCode = app(OtpService::class)->send('issouf@example.com', OtpService::REGISTER);

        $this->post('/connexion', ['email' => 'issouf@example.com', 'password' => 'motdepasse1']);
        $loginCode = $this->lastCodeSentTo('issouf@example.com');

        if ($registerCode !== $loginCode) {
            $this->post('/connexion/verification', ['code' => $registerCode])->assertSessionHasErrors('code');
            $this->assertGuest();
        }

        $this->post('/connexion/verification', ['code' => $loginCode]);
        $this->assertAuthenticatedAs($user);
    }
}
