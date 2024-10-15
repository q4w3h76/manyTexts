<?php

namespace Tests\Feature\Api\V1;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Resources\TextCollection;
use App\Http\Resources\TextResource;
use App\Models\Text;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class TextTest extends TestCase
{
    use RefreshDatabase;

    private const BASE_URL = '/api/v1/texts/';

    public function setUp(): void
    {
        parent::setUp();
        $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    /** @test */
    public function response_for_route_texts_show()
    {
        // create a new text
        $text = $this->createText();
        $response = $this->get(self::BASE_URL . $text['slug']);
        // checking the response
        $response->assertOk();
        $received_text = $response->json()['data'];

        $this->assertEquals($text, $received_text);
    }

    /** @test */
    public function response_for_route_texts_index()
    {
        // create a new texts with users
        $this->artisan('db:seed');
        $texts = Text::public()->paginate(15);
        $texts = json_encode(new TextCollection($texts));
        $response = $this->get(self::BASE_URL);
        // checking the response
        $response->assertOk();
        $received_texts = json_encode($response->json());

        $this->assertEquals($texts, $received_texts);
    }

    /** @test */
    public function a_text_can_be_stored_by_unauth_user()
    {
        $response = $this->storeText();
        // checking the response
        $response->assertCreated();
        $this->assertDatabaseCount('texts', 1);
        // get stored text
        $text = Text::first();
        // checking create job
        if($text->expiration != 0)
            $this->assertDatabaseCount('jobs', 1);
        
        $text = (new TextResource($text))->toArray(request());
        $this->assertEquals($response->json()['data'], $text);
    }

    /** @test */
    public function a_text_can_be_stored_by_auth_user()
    {
        $user = $this->createUserGetUserWithToken();
        $response = $this->storeText($user);
        // checking the response
        $response->assertCreated();
        $this->assertDatabaseCount('texts', 1);
        // get stored text
        $text = Text::first();
        // checking create job
        if($text->expiration != 0)
            $this->assertDatabaseCount('jobs', 1);

        $text = (new TextResource($text))->toArray(request());
        $this->assertEquals($response->json()['data'], $text);
    }

    /** @test */
    public function a_text_can_be_updated_by_unauth_user()
    {
        $response = $this->updateText();
        // checking the response
        $response->assertForbidden();
        // get text
        $text = (new TextResource(Text::first()))->toArray(request());
        $this->assertNotEquals($response->json()['data'] ?? null, $text);
    }

    /** @test */
    public function a_text_can_be_updated_by_auth_user()
    {
        $user = $this->createUserGetUserWithToken();
        $response = $this->updateText($user);
        // checking the response
        $response->assertOk();
        // get updated text
        $text = (new TextResource(Text::first()))->toArray(request());
        $this->assertEquals($response->json()['data'], $text);
    }

    /** @test */
    public function a_text_can_be_deleted_by_unauth_user()
    {
        $response = $this->deleteText();
        // checking the response
        $response->assertStatus(403);
        $this->assertDatabaseCount('texts', 1);
    }

    /** @test */
    public function a_text_can_be_deleted_by_auth_user()
    {
        $user = $this->createUserGetUserWithToken();
        $response = $this->deleteText($user);
        // checking the response
        $response->assertStatus(204);
        $this->assertDatabaseEmpty('texts');
    }

    private function storeText(array $user = null): TestResponse
    {
        if($user != null) {
            $this->withHeader('Authorization', $user['token']);
        }
        $data = [
            'title' => 'Lorem Title',
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'tags' => ['popular', 'hype', 'news'],
            'is_public' => true,
            'expiration' => 0,
        ];
        $response = $this->postJson(self::BASE_URL, $data);
        return $response;
    }

    private function updateText(array $user = null): TestResponse
    {
        if($user != null) {
            $this->withHeader('Authorization', $user['token']);
        }
        $text = $this->createText($user['id'] ?? null);
        // adding a new tag
        $tags = json_decode($text['tags']);
        array_push($tags, 'updated');
        // date for update new text
        $data = [
            'title' => 'updated ' . $text['title'],
            'text' => 'updated ' . $text['text'],
            'tags' => $tags,
            'is_public' => false,
        ];
        // request with token
        $response = $this->patchJson(self::BASE_URL . $text['slug'], $data);
        return $response;
    }

    private function deleteText(array $user = null): TestResponse
    {
        if($user != null) {
            $this->withHeader('Authorization', $user['token']);
        }
        $text = $this->createText($user['id'] ?? null);
        // request with token
        $response = $this->delete(self::BASE_URL . $text['slug']);
        return $response;
    }

    private function createUserGetUserWithToken(): array
    {
        $user = User::factory(1)->create()->first();
        $token = $user->createToken('TestToken')->plainTextToken;
        $user->token = 'Bearer ' .  $token;
        return $user->toArray();
    }

    private function createText(int $user_id = null, bool $is_public = true): array
    {
        $text = Text::factory(1)->sequence([
            'is_public' => $is_public,
            'user_id' => $user_id,
        ])->create()->first();
        return (new TextResource($text))->toArray(request());
    }
}
