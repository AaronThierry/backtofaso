<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('Revenir bâtir le Faso', false);
    }

    public function test_registration_flow_creates_user_and_joins_super_group(): void
    {
        config(['backtoburkina.otp.show_in_dev' => true]);
        $this->app['env'] = 'local';

        $this->post('/inscription', [
            'role' => User::ROLE_CANDIDATE,
            'first_name' => 'Awa',
            'last_name' => 'Ouédraogo',
            'phone' => '70 11 22 33',
            'password' => 'motdepasse1',
            'terms' => '1',
        ])->assertRedirect(route('register.verify'));

        $code = session('dev_code');
        $this->assertNotNull($code);

        $this->post('/inscription/verification', ['code' => $code])->assertRedirect(route('onboarding.profile'));

        $user = User::where('phone', '+22670112233')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Conversation::superGroup()->hasMember($user));
        $this->assertNotNull($user->dossier);
    }
}
