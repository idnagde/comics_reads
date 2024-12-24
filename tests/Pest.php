<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\postJson;

pest()->extend(Tests\TestCase::class)
    // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function validRegisterUserData(): array
{
    return [
        'name' => 'user test',
        'email' => 'usertest@mail.com',
        'password' => 'password123'
    ];
}

function loginUserData(array $data): array
{
    return [
        'email' => $data['email'],
        'password' => $data['password']
    ];
}

function createAndLoginUser(bool $assignAuthorRole = false): array
{
    $userData = validRegisterUserData();

    // Register user
    $registerResponse = postJson('/api/v1/register', $userData);
    $registerResponse->assertStatus(201);

    // Login user
    $loginResponse = postJson('/api/v1/login', loginUserData($userData));
    $loginResponse->assertStatus(200);

    $user = $loginResponse->json('data.user');
    $accessToken = $loginResponse->json('data.accessToken');

    // Assign author role if needed
    if ($assignAuthorRole && $accessToken) {
        $registerAuthorResponse = postJson('/api/v1/register-as-author', [], [
            'Authorization' => "Bearer {$accessToken}",
        ]);

        $registerAuthorResponse->assertStatus(200);
    }

    return [
        'user' => $user,
        'accessToken' => $accessToken,
    ];
}
