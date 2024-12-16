<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

describe('User Registration | /api/v1/register', function () {

    it('succeeds with valid data', function () {
        $data = [
            'name' =>  'user test',
            'email' => 'usertest@mail.com',
            'password' => 'password123'
        ];

        $response = postJson('/api/v1/register', $data);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'User registered Successfully.',
                'data' => [
                    'id' => 1,
                    'name' => 'user test',
                    'email' => 'usertest@mail.com',
                ]
            ]);
    });

    it('fails when email is already registered', function () {
        $data = [
            'name' =>  'user test',
            'email' => 'usertest@mail.com',
            'password' => 'password123'
        ];

        User::create($data);

        $response = postJson('/api/v1/register', $data);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Validation failed',
                'errors' => [
                    'email' => ['The email has already been taken.']
                ]
            ]);
    });

    it('fails when required fields are missing', function () {
        $data = [];

        $response = postJson('/api/v1/register', $data);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Validation failed',
                'errors' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);
    });
});
