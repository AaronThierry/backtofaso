<?php

namespace Tests\Feature;

use App\Models\Dossier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function member(): User
    {
        return User::factory()->create(['role' => User::ROLE_CANDIDATE, 'onboarded_at' => now(), 'password' => Hash::make('motdepasse1')]);
    }

    public function test_member_updates_personal_information(): void
    {
        $user = $this->member();

        $this->actingAs($user)->put(route('account.profile'), [
            'first_name' => 'Awa', 'last_name' => 'Ouédraogo', 'phone' => '70 99 88 77',
            'city' => 'Bobo-Dioulasso', 'destination' => 'france', 'language_level' => 'fluent',
        ])->assertRedirect()->assertSessionHas('status');

        $user->refresh();
        $this->assertSame('+22670998877', $user->phone);
        $this->assertSame('france', $user->destination);
    }

    public function test_member_changes_photo(): void
    {
        Storage::fake('public');
        $user = $this->member();

        $this->actingAs($user)->post(route('account.photo'), ['avatar' => UploadedFile::fake()->image('moi.jpg')])->assertRedirect();
        Storage::disk('public')->assertExists($user->fresh()->avatar_path);

        $this->actingAs($user)->delete(route('account.photo.delete'))->assertRedirect();
        $this->assertNull($user->fresh()->avatar_path);
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = $this->member();

        $this->actingAs($user)->put(route('account.password'), ['current_password' => 'faux', 'password' => 'nouveau123', 'password_confirmation' => 'nouveau123'])
            ->assertSessionHasErrorsIn('password', 'current_password');

        $this->actingAs($user)->put(route('account.password'), ['current_password' => 'motdepasse1', 'password' => 'nouveau123', 'password_confirmation' => 'nouveau123'])
            ->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('nouveau123', $user->fresh()->password));
    }

    public function test_member_deletes_account_with_confirmation(): void
    {
        $user = $this->member();
        Dossier::openFor($user);

        $this->actingAs($user)->delete(route('account.destroy'), ['password' => 'motdepasse1', 'confirmation' => 'oui'])
            ->assertSessionHasErrorsIn('deletion', 'confirmation');

        $this->actingAs($user)->delete(route('account.destroy'), ['password' => 'motdepasse1', 'confirmation' => 'supprimer'])
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('dossiers', ['user_id' => $user->id]);
    }
}
