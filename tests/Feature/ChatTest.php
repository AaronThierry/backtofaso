<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function member(array $attrs = []): User
    {
        return User::factory()->create(['role' => User::ROLE_CANDIDATE, 'onboarded_at' => now(), ...$attrs]);
    }

    private function direct(User $a, User $b): Conversation
    {
        $conversation = Conversation::create(['type' => Conversation::DIRECT, 'created_by' => $a->id]);
        $conversation->users()->attach([$a->id, $b->id]);

        return $conversation;
    }

    public function test_conversation_page_shows_messages_with_member_info(): void
    {
        [$awa, $issouf] = [$this->member(['first_name' => 'Awa']), $this->member(['first_name' => 'Issouf'])];
        $conversation = $this->direct($awa, $issouf);
        $conversation->messages()->create(['user_id' => $issouf->id, 'body' => 'Bonjour Awa, voici le lien https://exemple.bf']);

        $this->actingAs($awa)->get(route('chat.show', $conversation))
            ->assertOk()
            ->assertSee('Bonjour Awa', false)
            ->assertSee('Conversation privée : seuls vous et Issouf', false);
    }

    public function test_author_can_delete_own_message_but_not_others(): void
    {
        [$awa, $issouf] = [$this->member(), $this->member()];
        $conversation = $this->direct($awa, $issouf);
        $mine = $conversation->messages()->create(['user_id' => $awa->id, 'body' => 'À supprimer']);
        $theirs = $conversation->messages()->create(['user_id' => $issouf->id, 'body' => 'Pas à moi']);

        $this->actingAs($awa)->deleteJson(route('chat.destroy', [$conversation, $theirs]))->assertForbidden();
        $this->actingAs($awa)->deleteJson(route('chat.destroy', [$conversation, $mine]))->assertOk();

        $this->assertDatabaseMissing('messages', ['id' => $mine->id]);
        $this->assertDatabaseHas('messages', ['id' => $theirs->id]);
    }

    public function test_polling_returns_new_messages_and_seen_status(): void
    {
        [$awa, $issouf] = [$this->member(), $this->member()];
        $conversation = $this->direct($awa, $issouf);
        $first = $conversation->messages()->create(['user_id' => $awa->id, 'body' => 'Salut']);
        $conversation->users()->updateExistingPivot($issouf->id, ['last_read_at' => now()->addMinute()]);
        $conversation->messages()->create(['user_id' => $issouf->id, 'body' => 'Bien reçu']);

        $this->actingAs($awa)->getJson(route('chat.messages', [$conversation, 'after' => $first->id]))
            ->assertOk()
            ->assertJsonCount(1, 'messages')
            ->assertJsonPath('messages.0.body', 'Bien reçu')
            ->assertJsonPath('messages.0.dayLabel', 'Aujourd’hui');

        $this->assertNotNull($this->actingAs($awa)->getJson(route('chat.messages', $conversation))->json('seenUntil'));
    }

    public function test_older_messages_can_be_loaded(): void
    {
        [$awa, $issouf] = [$this->member(), $this->member()];
        $conversation = $this->direct($awa, $issouf);
        $ids = collect(range(1, 3))->map(fn ($i) => $conversation->messages()->create(['user_id' => $awa->id, 'body' => "Message {$i}"])->id);

        $this->actingAs($awa)->getJson(route('chat.messages', [$conversation, 'before' => $ids->last()]))
            ->assertOk()
            ->assertJsonCount(2, 'messages')
            ->assertJsonPath('hasMore', false);
    }

    public function test_members_search_excludes_current_user(): void
    {
        $awa = $this->member(['first_name' => 'Awa']);
        $this->member(['first_name' => 'Aminata']);

        $this->actingAs($awa)->getJson(route('chat.members', ['q' => 'A']))
            ->assertOk()
            ->assertJsonFragment(['name' => User::where('first_name', 'Aminata')->first()->name])
            ->assertJsonMissing(['id' => $awa->id]);
    }

    public function test_member_can_leave_a_group(): void
    {
        $awa = $this->member();
        $group = Conversation::create(['type' => Conversation::GROUP, 'name' => 'Étudiants à Montréal', 'is_public' => true, 'created_by' => $awa->id]);
        $group->users()->attach($awa->id, ['role' => 'admin']);

        $this->actingAs($awa)->post(route('groups.leave', $group))->assertRedirect(route('chat.index'));
        $this->assertFalse($group->hasMember($awa));
    }
}
