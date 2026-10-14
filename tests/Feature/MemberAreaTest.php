<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Dossier;
use App\Models\TalentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Chaque page de l'espace membre s'affiche pour chaque type de profil */
class MemberAreaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Conversation::superGroup()->messages()->create([
            'user_id' => User::factory()->create(['role' => User::ROLE_AGENT])->id,
            'topic' => 'annonces', 'body' => 'Bienvenue dans la communauté.',
        ]);
    }

    private function member(string $role): User
    {
        $user = User::factory()->create(['role' => $role, 'onboarded_at' => now(), 'destination' => 'canada']);
        Conversation::superGroup()->users()->syncWithoutDetaching([$user->id => ['role' => 'member']]);

        return $user;
    }

    public function test_candidate_pages_load(): void
    {
        $user = $this->member(User::ROLE_CANDIDATE);
        Dossier::openFor($user);

        foreach (['/accueil' => 'document(s) à fournir', '/dossier' => 'Documents', '/compte' => 'Sécurité', '/chat' => 'Messages', '/talents' => 'Vivier de talents'] as $url => $text) {
            $this->actingAs($user)->get($url)->assertOk()->assertSee($text, false);
        }
    }

    public function test_graduate_pages_load(): void
    {
        $user = $this->member(User::ROLE_GRADUATE);
        $profile = TalentProfile::create(['user_id' => $user->id, 'headline' => TalentProfile::DEFAULT_HEADLINE, 'field' => 'Santé', 'study_country' => 'maroc']);

        $this->actingAs($user)->get('/accueil')->assertOk()->assertSee('Complétez votre profil talent', false);
        $this->actingAs($user)->get('/compte')->assertOk()->assertSee('Profil talent');
        $this->actingAs($user)->get(route('talents.show', $profile))->assertOk()->assertSee('Formé(e) en', false)->assertSee('Maroc');
        $this->actingAs($user)->get(route('talents.edit'))->assertOk();
    }

    public function test_recruiter_and_staff_dashboards_load(): void
    {
        $graduate = $this->member(User::ROLE_GRADUATE);
        TalentProfile::create(['user_id' => $graduate->id, 'headline' => 'Médecin', 'field' => 'Santé', 'study_country' => 'chine', 'is_public' => true, 'verified_at' => now()]);

        $this->actingAs($this->member(User::ROLE_RECRUITER))->get('/accueil')->assertOk()->assertSee('Talents récemment vérifiés', false);
        $this->actingAs($this->member(User::ROLE_ADMIN))->get('/accueil')->assertOk()->assertSee('Profils à vérifier', false);
    }

    public function test_talent_completion_counts_filled_items(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_GRADUATE]);
        $profile = TalentProfile::create(['user_id' => $user->id, 'headline' => TalentProfile::DEFAULT_HEADLINE, 'field' => 'Santé', 'is_public' => false]);

        $this->assertSame(0, $profile->load('user')->completion()['percent']);

        $profile->update(['headline' => 'Médecin généraliste', 'skills' => ['A', 'B', 'C'], 'is_public' => true]);
        $this->assertSame(38, $profile->fresh()->load('user')->completion()['percent']);
    }
}
