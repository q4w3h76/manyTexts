<?php

namespace Tests\Feature\Api\V1;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Resources\TextCollection;
use App\Http\Resources\TextResource;
use App\Models\Text;
use App\Models\User;
use Database\Seeders\TextsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TextTest extends TestCase
{
    use RefreshDatabase;

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
        Text::factory(1)->sequence([
            'is_public' => true,
        ])->create();
        $text = Text::first();
        $text = (new TextResource($text))->toArray(request());
        $slug = $text['slug'];
        $response = $this->get('/api/v1/posts/' . $slug);
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
        $response = $this->get('/api/v1/posts');
        // checking the response
        $response->assertOk();
        $received_texts = json_encode($response->json());
        $this->assertEquals($texts, $received_texts);
    }

    /** @test */
    public function a_text_can_be_stored()
    {
        $data = [
            'title' => 'Lorem Title',
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'tags' => ['popular', 'hype', 'news'],
            'is_public' => true,
            'expiration' => 0,
        ];
        $response = $this->postJson('/api/v1/posts', $data);
        // checking the response
        $response->assertCreated();
        $this->assertDatabaseCount('texts', 1);
        if($data['expiration'] != 0)
            $this->assertDatabaseCount('jobs', 1);
        $text = new TextResource(Text::first());
        $this->assertEquals(json_encode($response->json()['data']), json_encode($text));
    }

    /** @test */
    public function a_text_can_be_updated_by_auth_user()
    {
        // create a new with token
        User::factory(1)->create();
        $user = User::first();
        $token = $user->createToken('TestToken')->plainTextToken;
        // create a new text
        Text::factory(1)->sequence([
            'is_public' => true,
            'user_id' => $user->id,
        ])->create();
        $text = Text::first();
        $text = (new TextResource($text))->toArray(request());
        // date for update new text
        $slug = $text['slug'];
        $tags = json_decode($text['tags']);
        array_push($tags, 'updated');
        $data = [
            'title' => 'updated ' . $text['title'],
            'text' => 'updated ' . $text['text'],
            'tags' => $tags,
            'is_public' => false,
        ];
        // request with token
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->patchJson('/api/v1/posts/' . $slug, $data);
        // checking the response
        $response->assertOk();
        // get updated text
        $text = Text::first();
        $text = (new TextResource($text))->toArray(request());
        $this->assertEquals(json_encode($response->json()['data']), json_encode($text));
    }

    /** @test */
    public function a_text_can_be_deleted_by_auth_user()
    {
        // create a new with token
        User::factory(1)->create();
        $user = User::first();
        $token = $user->createToken('TestToken')->plainTextToken;
        // create a new text
        Text::factory(1)->sequence([
            'user_id' => $user->id,
        ])->create();
        $text = Text::first();
        $slug = $text->slug;
        // request with token
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->delete('/api/v1/posts/' . $slug);
        // checking the response
        $response->assertStatus(204);
        $this->assertDatabaseEmpty('texts');
    }
}
