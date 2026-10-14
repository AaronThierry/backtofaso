<?php

namespace Tests\Feature;

use App\Mail\GroupInvitationMail;
use App\Mail\OtpCodeMail;
use App\Models\Conversation;
use App\Models\GroupInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GroupInvitationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Conversation $group;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();

        $this->admin = User::factory()->create(['first_name' => 'Thierry', 'role' => User::ROLE_GRADUATE, 'onboarded_at' => now()]);
        $this->group = Conversation::create(['type' => Conversation::GROUP, 'name' => 'Bobo', 'is_public' => false, 'created_by' => $this->admin->id]);
        $this->group->users()->attach($this->admin->id, ['role' => 'admin']);
    }

    /** Envoie une invitation et renvoie le lien reçu par e-mail */
    private function invite(string $email): string
    {
        $this->actingAs($this->admin)->post(route('groups.invitations.store', $this->group), ['emails' => $email])->assertSessionHas('status');

        return Mail::sent(GroupInvitationMail::class, fn ($m) => $m->hasTo($email))->last()->url;
    }

    private function tokenOf(string $url): string
    {
        return basename(parse_url($url, PHP_URL_PATH));
    }

    public function test_admin_sends_personal_invitation_and_only_hash_is_stored(): void
    {
        $url = $this->invite('Awa@Example.com');
        $token = $this->tokenOf($url);

        $invitation = GroupInvitation::sole();
        $this->assertSame('awa@example.com', $invitation->email);
        $this->assertNotSame($token, $invitation->token_hash);
        $this->assertSame(GroupInvitation::hashToken($token), $invitation->token_hash);

        // L'e-mail se génère et contient le lien personnel
        $html = Mail::sent(GroupInvitationMail::class)->last()->render();
        $this->assertStringContainsString('Rejoindre le groupe', $html);
        $this->assertStringContainsString($token, $html);
    }

    public function test_simple_member_cannot_invite(): void
    {
        $member = User::factory()->create(['onboarded_at' => now()]);
        $this->group->users()->attach($member->id, ['role' => 'member']);

        $this->actingAs($member)->post(route('groups.invitations.store', $this->group), ['emails' => 'x@example.com'])->assertForbidden();
        Mail::assertNothingSent();
    }

    public function test_invited_account_joins_once_with_the_link(): void
    {
        $awa = User::factory()->create(['email' => 'awa@example.com', 'onboarded_at' => now()]);
        $url = $this->invite('awa@example.com');
        $token = $this->tokenOf($url);
        auth()->logout();

        $this->get($url)->assertOk()->assertSee('Se connecter pour rejoindre', false);
        $this->actingAs($awa)->get($url)->assertOk()->assertSee('Rejoindre le groupe', false);

        $this->actingAs($awa)->post(route('invitations.accept', $token))->assertRedirect(route('chat.show', $this->group));
        $this->assertTrue($this->group->hasMember($awa));

        // Lien à usage unique
        $this->actingAs($awa)->post(route('invitations.accept', $token))->assertStatus(410);
    }

    public function test_link_is_refused_for_another_account(): void
    {
        $url = $this->invite('awa@example.com');
        $intruder = User::factory()->create(['email' => 'autre@example.com', 'onboarded_at' => now()]);

        $this->actingAs($intruder)->get($url)->assertOk()->assertSee('Changer de compte', false);
        $this->actingAs($intruder)->post(route('invitations.accept', $this->tokenOf($url)))->assertForbidden();
        $this->assertFalse($this->group->hasMember($intruder));
    }

    public function test_new_account_with_invited_email_joins_the_group_automatically(): void
    {
        $this->invite('nouveau@example.com');
        auth()->logout();

        $this->post('/inscription', [
            'role' => User::ROLE_CANDIDATE, 'first_name' => 'Nouveau', 'last_name' => 'Membre',
            'email' => 'nouveau@example.com', 'phone' => '70 55 44 33', 'password' => 'motdepasse1', 'terms' => '1',
        ]);
        $code = Mail::sent(OtpCodeMail::class, fn ($m) => $m->hasTo('nouveau@example.com'))->last()->code;
        $this->post('/inscription/verification', ['code' => $code]);

        $user = User::where('email', 'nouveau@example.com')->firstOrFail();
        $this->assertTrue($this->group->hasMember($user));
        $this->assertNotNull(GroupInvitation::sole()->accepted_at);
    }

    public function test_expired_and_replaced_links_no_longer_work(): void
    {
        $first = $this->invite('awa@example.com');
        $second = $this->invite('awa@example.com');
        auth()->logout();

        // Le renvoi remplace l'ancien lien
        $this->get($first)->assertOk()->assertSee('n’est pas valable', false);

        GroupInvitation::query()->update(['expires_at' => now()->subDay()]);
        $this->get($second)->assertOk()->assertSee('Cette invitation a expiré', false);
    }
}
