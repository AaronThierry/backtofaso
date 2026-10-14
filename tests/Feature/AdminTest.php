<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Dossier;
use App\Models\DossierDocument;
use App\Models\TalentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role, 'onboarded_at' => now()]);
    }

    public function test_members_cannot_open_the_back_office(): void
    {
        $this->actingAs($this->user(User::ROLE_CANDIDATE))->get('/admin')->assertForbidden();
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_staff_pages_load(): void
    {
        $candidate = $this->user(User::ROLE_CANDIDATE);
        $dossier = Dossier::openFor($candidate);
        TalentProfile::create(['user_id' => $this->user(User::ROLE_GRADUATE)->id, 'headline' => 'Médecin', 'field' => 'Santé']);
        ContactMessage::create(['name' => 'Awa', 'email' => 'awa@example.com', 'subject' => 'autre', 'message' => 'Bonjour, une question.']);

        $agent = $this->user(User::ROLE_AGENT);
        foreach (['/admin', '/admin/dossiers', route('admin.dossiers.show', $dossier), '/admin/talents', '/admin/messages-contact'] as $url) {
            $this->actingAs($agent)->get($url)->assertOk();
        }

        // Les utilisateurs et rôles sont réservés aux administrateurs
        $this->actingAs($agent)->get('/admin/utilisateurs')->assertForbidden();
        $this->actingAs($this->user(User::ROLE_ADMIN))->get('/admin/utilisateurs')->assertOk();
    }

    public function test_staff_reviews_documents_and_assigns_advisor(): void
    {
        Storage::fake('local');
        $candidate = $this->user(User::ROLE_CANDIDATE);
        $dossier = Dossier::openFor($candidate);
        $doc = $dossier->documents()->first();
        $path = UploadedFile::fake()->create('passeport.pdf', 20)->store('dossiers/'.$dossier->id, 'local');
        $doc->update(['path' => $path, 'status' => DossierDocument::PENDING, 'submitted_at' => now()]);
        $agent = $this->user(User::ROLE_AGENT);

        $this->actingAs($agent)->get(route('admin.documents.download', $doc))->assertOk();

        $this->actingAs($agent)->post(route('admin.documents.review', $doc), ['decision' => 'complete'])->assertSessionHasErrors('note');
        $this->actingAs($agent)->post(route('admin.documents.review', $doc), ['decision' => 'complete', 'note' => 'Page illisible.'])->assertRedirect();
        $this->assertSame(DossierDocument::TO_COMPLETE, $doc->fresh()->status);

        $this->actingAs($agent)->post(route('admin.documents.review', $doc), ['decision' => 'validate'])->assertRedirect();
        $this->assertSame(DossierDocument::VALIDATED, $doc->fresh()->status);

        $this->actingAs($agent)->put(route('admin.dossiers.update', $dossier), [
            'current_step' => 3, 'status' => 'open', 'advisor_id' => $agent->id, 'country' => 'canada', 'program' => 'Licence en informatique',
        ])->assertRedirect();
        $this->assertSame($agent->id, $dossier->fresh()->advisor_id);
        $this->assertSame(3, (int) $dossier->fresh()->current_step);

        // Un candidat ne peut pas télécharger les pièces via le back-office
        $this->actingAs($candidate)->get(route('admin.documents.download', $doc))->assertForbidden();
    }

    public function test_staff_verifies_talents_and_handles_contacts(): void
    {
        $agent = $this->user(User::ROLE_AGENT);
        $profile = TalentProfile::create(['user_id' => $this->user(User::ROLE_GRADUATE)->id, 'headline' => 'Médecin', 'field' => 'Santé']);
        $message = ContactMessage::create(['name' => 'Awa', 'email' => 'awa@example.com', 'subject' => 'autre', 'message' => 'Bonjour, une question.']);

        $this->actingAs($agent)->post(route('admin.talents.verify', $profile), ['verify' => 1]);
        $this->assertNotNull($profile->fresh()->verified_at);

        $this->actingAs($agent)->post(route('admin.contacts.toggle', $message));
        $this->assertNotNull($message->fresh()->handled_at);
    }

    public function test_admin_changes_roles_but_not_his_own(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);
        $member = $this->user(User::ROLE_GRADUATE);

        $this->actingAs($admin)->put(route('admin.users.role', $member), ['role' => User::ROLE_AGENT])->assertRedirect();
        $this->assertSame(User::ROLE_AGENT, $member->fresh()->role);

        $this->actingAs($admin)->put(route('admin.users.role', $admin), ['role' => User::ROLE_CANDIDATE])->assertStatus(422);
        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertStatus(422);
    }

    public function test_admin_create_command_promotes_an_existing_account(): void
    {
        $member = User::factory()->create(['email' => 'chef@example.com', 'role' => User::ROLE_CANDIDATE]);

        $this->artisan('admin:create', ['--promote' => 'chef@example.com'])->assertSuccessful();
        $this->assertSame(User::ROLE_ADMIN, $member->fresh()->role);
    }
}
