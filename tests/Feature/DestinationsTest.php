<?php

namespace Tests\Feature;

use App\Models\Dossier;
use App\Models\TalentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_onboarding_saves_destination_and_sets_dossier_country(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CANDIDATE]);
        Dossier::openFor($user);

        $this->actingAs($user)->post(route('onboarding.profile.store'), [
            'destination' => 'canada',
            'language_level' => 'advanced',
            'interests' => ['Informatique'],
        ])->assertRedirect(route('onboarding.preferences'));

        $this->assertSame('canada', $user->fresh()->destination);
        $this->assertSame('canada', $user->dossier->fresh()->country);
        $this->assertSame('Canada', $user->dossier->fresh()->countryName());
    }

    public function test_onboarding_rejects_unknown_destination(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CANDIDATE]);

        $this->actingAs($user)->post(route('onboarding.profile.store'), ['destination' => 'atlantide'])
            ->assertSessionHasErrors('destination');
    }

    public function test_talents_can_be_filtered_by_study_country(): void
    {
        $recruiter = User::factory()->create(['role' => User::ROLE_RECRUITER, 'onboarded_at' => now()]);

        foreach ([['Mariam', 'chine'], ['Boukary', 'maroc']] as [$name, $country]) {
            $graduate = User::factory()->create(['first_name' => $name, 'role' => User::ROLE_GRADUATE]);
            TalentProfile::create([
                'user_id' => $graduate->id, 'headline' => 'Diplômé', 'field' => 'Santé',
                'study_country' => $country, 'is_public' => true, 'verified_at' => now(),
            ]);
        }

        $this->actingAs($recruiter)->get(route('talents.index', ['pays' => 'maroc']))
            ->assertOk()
            ->assertSee('Boukary')
            ->assertDontSee('Mariam');
    }
}
