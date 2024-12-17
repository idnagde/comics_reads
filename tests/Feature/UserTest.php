<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

describe('User Registration | /api/v1/register', function () {

    it('should successfully register a user with valid data', function () {
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
                    'created_at' => true,
                ]
            ]);
    });

    it('should fail to register a user when the email is already taken', function () {
        $data = [
            'name' =>  'user test',
            'email' => 'usertest@mail.com',
            'password' => 'password123'
        ];

        postJson('/api/v1/register', $data);

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

    it('should fail to register a user when required fields are missing', function () {
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


describe('User Login | /api/v1/login', function () {
    $register = [
        'name' => 'user test',
        'email' => 'usertest@mail.com',
        'password' => 'password123'
    ];

    it('should successfully log in a user with valid credentials', function () use (&$register) {

        // register
        postJson('/api/v1/register', $register);

        // login
        $login = [
            'email' => $register['email'],
            'password' => $register['password']
        ];

        $response = postJson('/api/v1/login', $login);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Login successful.',
                'data' => [
                    'user' => [
                        'id' => true,
                        'name' => $register['name'],
                        'email' => $register['email'],
                        'created_at' => true
                    ],
                    'accessToken' => true
                ]
            ]);
    });

    it('should fail to log in a user with invalid credentials', function () use ($register) {

        // register
        postJson('/api/v1/register', $register);

        // login
        $login = [
            'email' => $register['email'],
            'password' => 'wrongpassword'
        ];

        $response = postJson('/api/v1/login', $login);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Login failed.',
                'errors' => [
                    'credential' => 'Invalid credentials'
                ]
            ]);
    });
});

describe('User List | /api/v1/users', function () {
    it('should return paginated user list', function () {
        User::factory(15)->create();

        $response = getJson('/api/v1/users');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'created_at']
                ],
                'links',
                'meta'
            ])
            ->assertJsonPath('meta.total', 15);
    });

    it('should filter users by search query', function () {
        User::factory()->create(['name' => 'user test alpha']);
        User::factory()->create(['name' => 'user test beta']);

        $response = getJson('/api/v1/users?search=alpha');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'user test alpha');
    });
});
