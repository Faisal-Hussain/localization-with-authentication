<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Mockery;

/**
 * Class JWTAuthControllerTest
 *
 * This class contains feature tests for the JWT authentication system.
 * It tests user registration, login, fetching authenticated user data, and logout functionalities.
 */
class JWTAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    /**
     * Setup method for initializing common test dependencies.
     * Creates a test user and generates a JWT token.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $this->token = JWTAuth::fromUser($this->user);
    }

    /**
     * Test case for user registration.
     *
     * This test ensures that a new user can be registered successfully via API.
     * It checks if the response contains a valid JSON structure with user data and a token.
     */
    public function test_it_can_register_a_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register-user', $userData);

        $response
            ->assertStatus(201)
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    /**
     * Test case for user login.
     *
     * This test verifies that a user can successfully log in using valid credentials.
     * It mocks the JWTAuth attempt method and checks if the response returns a valid token.
     */
    public function test_it_can_login_a_user()
    {
        $credentials = ['email' => $this->user->email, 'password' => 'password123'];

        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with($credentials)
            ->andReturn($this->token);

        $response = $this->postJson('/api/login', $credentials);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    /**
     * Test case for handling invalid login attempts.
     *
     * This test ensures that an error message is returned when a user tries to log in with invalid credentials.
     */
    public function test_it_returns_error_on_invalid_login()
    {
        $invalidCredentials = ['email' => $this->user->email, 'password' => 'wrongpassword'];

        JWTAuth::shouldReceive('attempt')->once()->andReturn(false);

        $response = $this->postJson('/api/login', $invalidCredentials);

        $response
            ->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    /**
     * Test case for fetching the authenticated user's data.
     *
     * This test checks if the API correctly returns the authenticated user's details when a valid token is provided.
     */
    public function test_it_can_fetch_authenticated_user()
    {
        JWTAuth::shouldReceive('parseToken->authenticate')->twice()->andReturn($this->user);

        $response = $this
            ->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->getJson('/api/get-current-user');

        $response
            ->assertStatus(200)
            ->assertJson(['data' => ['user' => ['email' => $this->user->email]]]);
    }

    /**
     * Test case for logging out an authenticated user.
     *
     * This test ensures that an authenticated user can log out successfully and the token is invalidated.
     */
    public function test_it_can_logout_a_user()
    {
        JWTAuth::shouldReceive('parseToken')->once()->andReturnSelf();
        JWTAuth::shouldReceive('authenticate')->once()->andReturn((object) ['id' => 1]);
        JWTAuth::shouldReceive('getToken')->once()->andReturn('mocked_token');
        JWTAuth::shouldReceive('invalidate')->once()->andReturn(true);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}"
        ])->postJson('/api/logout');

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
    }

    /**
     * Tear down method to close Mockery after each test.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
