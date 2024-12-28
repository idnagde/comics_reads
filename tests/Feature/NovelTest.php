<?php

use App\Models\Genre;
use Database\Seeders\GenreSeeder;
use function Pest\Laravel\postJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function() {
    $this->artisan('db:seed', ['--class' => GenreSeeder::class]);
});

describe('Novel Creation | /api/v1/novels', function () {

    it('should successfully create a novel with valid data for an author', function () {

        $actingUser = createAndLoginUser(assignAuthorRole: true);
        $accessToken = $actingUser['accessToken'];

        $genres = Genre::inRandomOrder()->take(3)->pluck('id')->toArray();

        $response = postJson('/api/v1/novels', [
            'title' => 'novel test',
            'synopsis' => 'synopsis test',
            'genres' => $genres
        ], ['Authorization' => "Bearer {$accessToken}"]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Novel created successfully.',
                'data' => [
                    'id' => true,
                    'title' => 'novel test',
                    'genres' => Genre::whereIn('id', $genres)->pluck('name')->toArray(),
                    'synopsis' => 'synopsis test',
                    'created_at' => true,
                    'updated_at' => true
                ]
            ]);
    });


    it('should fail to create a novel without required fields', function () {

        $actingUser = createAndLoginUser(assignAuthorRole: true);
        $accessToken = $actingUser['accessToken'];

        $response = postJson('/api/v1/novels', [], ['Authorization' => "Bearer {$accessToken}"]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'synopsis']);
    });

    it('should fail to create a novel with invalid genres', function () {
        $actingUser = createAndLoginUser(assignAuthorRole: true);
        $accessToken = $actingUser['accessToken'];

        $response = postJson('/api/v1/novels', [
            'title' => 'novel test',
            'synopsis' => 'synopsis test',
            'genres' => [999, 1000], // Invalid genre IDs
        ], ['Authorization' => "Bearer {$accessToken}"]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['genres.0', 'genres.1']);
    });

    it('should fail to create a novel if the user is not an author', function () {

        $actingUser = createAndLoginUser();
        $accessToken = $actingUser['accessToken'];

        $response = postJson('/api/v1/novels', [
            'title' => 'novel test',
            'synopsis' => 'synopsis test'
        ], ['Authorization' => "Bearer {$accessToken}"]);

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.'
            ]);
    });

    it('should fail to create a novel without authentication', function () {

        $response = postJson('/api/v1/novels', [
            'title' => 'novel test',
            'synopsis' => 'synopsis test'
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    });
});
