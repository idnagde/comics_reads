<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

describe('User Registration | /api/v1/register', function () {

    it('should successfully register a user with valid data', function () {
        $response = postJson('/api/v1/register', validRegisterUserData());

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

        postJson('/api/v1/register', validRegisterUserData());

        $response = postJson('/api/v1/register', validRegisterUserData());

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

        $response = postJson('/api/v1/register', []);

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

    it('should successfully log in a user with valid credentials', function () {

        // register
        $registerData = validRegisterUserData();
        postJson('/api/v1/register', $registerData);

        $response = postJson('/api/v1/login', loginUserData($registerData));

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Login successful.',
                'data' => [
                    'user' => [
                        'id' => true,
                        'name' => $registerData['name'],
                        'email' => $registerData['email'],
                        'created_at' => true
                    ],
                    'accessToken' => true
                ]
            ]);
    });

    it('should fail to log in a user with invalid credentials', function () {

        // register
        postJson('/api/v1/register', validRegisterUserData());

        $invalidLogin = [
            'email' => validRegisterUserData()['email'],
            'password' => 'wrongpassword'
        ];

        $response = postJson('/api/v1/login', $invalidLogin);

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

    it('should require authentication to access the user list', function () {
        $response = getJson('/api/v1/users');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    });

    it('should return paginated user list', function () {
        $accessToken = createAndLoginUser()['accessToken'];

        User::factory(14)->create();

        $response = getJson('/api/v1/users', ['Authorization' => "Bearer {$accessToken}"]);

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
        $accessToken = createAndLoginUser()['accessToken'];

        $users = User::factory(10)->create();
        $user = $users[4];

        $response = getJson('/api/v1/users?search=' . $user['name'], ['Authorization' => "Bearer {$accessToken}"]);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', $user['name']);
    });
});

describe('User Detail | /api/v1/users/${id}', function () {

    it('should require authentication to access the user detail', function () {

        $response = getJson('/api/v1/users/1');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    });

    it('should fetch single user by ID', function () {
        $accessToken = createAndLoginUser()['accessToken'];

        $users = User::factory(10)->create();
        $user = $users->first();

        $response = getJson('/api/v1/users/' . $user['id'], ['Authorization' => "Bearer {$accessToken}"]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'User retrieved successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'created_at' => $user['created_at']
                ]
            ]);
    });

    it('should return 404 if user is not found', function () {
        $actingUser = createAndLoginUser();
        $accessToken = $actingUser['accessToken'];
        $notFoundId = $actingUser['user']['id'] + 1;

        $response = getJson('/api/v1/users/' . $notFoundId, ['Authorization' => "Bearer {$accessToken}"]);

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => 'User not found.',
            ]);
    });
});
