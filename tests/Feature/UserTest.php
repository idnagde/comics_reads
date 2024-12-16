<?php

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
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
            ->assertJson(
                fn(AssertableJson $json) =>
                $json
                    ->where('message', 'User registered Successfully.')
                    ->has(
                        'data',
                        fn(AssertableJson $json) =>
                        $json
                            ->where('id', 1)
                            ->where('name', 'user test')
                            ->where('email', 'usertest@mail.com')
                            ->etc()
                    )
            );
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
            ->assertJson(
                fn(AssertableJson $json) =>
                $json
                    ->where('message', 'Validation failed')
                    ->has('errors.email')->where('errors.email.0', 'The email has already been taken.')
            );
    });

    it('fails when required fields are missing', function () {
        $data = [];

        $response = postJson('/api/v1/register', $data);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json
                    ->where('message', 'Validation failed')
                    ->has('errors.name')->where('errors.name.0', 'The name field is required.')
                    ->has('errors.email')->where('errors.email.0', 'The email field is required.')
                    ->has('errors.password')->where('errors.password.0', 'The password field is required.')
            );
    });
});
