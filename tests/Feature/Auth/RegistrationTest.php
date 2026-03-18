<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private string $endpoint = '/register';

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name'                  => 'John Doe',
            'email'                 => 'john.doe@example.com',
            'password'              => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ], $overrides);
    }

    // -------------------------------------------------------------------------
    // Happy path
    // -------------------------------------------------------------------------

    public function test_successful_registration_returns_201(): void
    {
        Mail::fake();
        Event::fake([Registered::class]);

        $response = $this->postJson($this->endpoint, $this->validPayload());

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'user' => ['id', 'name', 'email'],
                 ])
                 ->assertJson([
                     'status'  => 'success',
                     'message' => 'User registered successfully. Verification email sent.',
                 ]);
    }

    public function test_successful_registration_stores_user_in_database(): void
    {
        Mail::fake();
        Event::fake([Registered::class]);

        $this->postJson($this->endpoint, $this->validPayload());

        $this->assertDatabaseHas('users', [
            'name'  => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
    }

    public function test_successful_registration_fires_registered_event(): void
    {
        Mail::fake();
        Event::fake([Registered::class]);

        $this->postJson($this->endpoint, $this->validPayload());

        Event::assertDispatched(Registered::class, function (Registered $event) {
            return $event->user->email === 'john.doe@example.com';
        });
    }

    public function test_response_user_matches_database_record(): void
    {
        Mail::fake();
        Event::fake([Registered::class]);

        $response = $this->postJson($this->endpoint, $this->validPayload());

        $user = User::where('email', 'john.doe@example.com')->first();

        $response->assertJson([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Validation failures — 422
    // -------------------------------------------------------------------------

    public function test_missing_name_returns_422(): void
    {
        $response = $this->postJson($this->endpoint, $this->validPayload(['name' => '']));

        $response->assertStatus(422)
                 ->assertJson(['status' => 'error', 'message' => 'Validation failed.'])
                 ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_missing_email_returns_422(): void
    {
        $response = $this->postJson($this->endpoint, $this->validPayload(['email' => '']));

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_invalid_email_format_returns_422(): void
    {
        $response = $this->postJson($this->endpoint, $this->validPayload(['email' => 'not-an-email']));

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_uppercase_email_returns_422(): void
    {
        // rule: lowercase
        $response = $this->postJson($this->endpoint, $this->validPayload(['email' => 'John.Doe@Example.COM']));

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_duplicate_email_returns_422(): void
    {
        Mail::fake();
        Event::fake([Registered::class]);

        // First registration succeeds
        $this->postJson($this->endpoint, $this->validPayload());

        // Second registration with same email should fail
        $response = $this->postJson($this->endpoint, $this->validPayload());

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_missing_password_returns_422(): void
    {
        $response = $this->postJson($this->endpoint, $this->validPayload([
            'password'              => '',
            'password_confirmation' => '',
        ]));

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['password']]);
    }

    public function test_password_confirmation_mismatch_returns_422(): void
    {
        $response = $this->postJson($this->endpoint, $this->validPayload([
            'password_confirmation' => 'DifferentPass999!',
        ]));

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['password']]);
    }

    public function test_weak_password_returns_422(): void
    {
        $response = $this->postJson($this->endpoint, $this->validPayload([
            'password'              => '123',
            'password_confirmation' => '123',
        ]));

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['password']]);
    }

    public function test_name_exceeding_max_length_returns_422(): void
    {
        $response = $this->postJson($this->endpoint, $this->validPayload([
            'name' => str_repeat('a', 256),
        ]));

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors' => ['name']]);
    }

    // -------------------------------------------------------------------------
    // Honeypot
    // -------------------------------------------------------------------------

    public function test_honeypot_filled_returns_fake_success_and_does_not_create_user(): void
    {
        Log::shouldReceive('warning')
           ->once()
           ->with('Honeypot triggered', \Mockery::type('array'));

        $response = $this->postJson($this->endpoint, $this->validPayload([
            'website' => 'http://spam.example.com',
        ]));

        $response->assertStatus(201)
                 ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('users', ['email' => 'john.doe@example.com']);
    }

    public function test_honeypot_empty_does_not_trigger(): void
    {
        Mail::fake();
        Event::fake([Registered::class]);

        $response = $this->postJson($this->endpoint, $this->validPayload([
            'website' => '',
        ]));

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
    }
}
