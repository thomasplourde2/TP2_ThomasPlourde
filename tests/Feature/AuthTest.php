<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    public function test_registerValidData201(): void
    {
        $user = [
            'login' => 'Carlos42',
            'password' => 'chat456',
            'email' => 'carldup12@gmail.com',
            'last_name' => 'Dupuis', 
            'first_name' => 'Carl'
        ];
        $response = $this->post('/api/signup', $user);
        $this->assertDatabaseHas('users',[
            'login' => 'Carlos42'
        ]);
        $response->assertStatus(CREATED);
    }

    public function test_registerWrongEmailFormat500(): void
    {
        $user = [
            'login' => 'Carlos42',
            'password' => 'chat456',
            'email' => 'cheval',
            'last_name' => 'Dupuis', 
            'first_name' => 'Carl'
        ];
        $response = $this->post('/api/signup', $user);
        $response->assertStatus(SERVER_ERROR);
    }

    public function test_registerInvalidData422(): void
    {
        $user = [
            'login' => 'Carlos42',
            'password' => 'chat456',
            'email' => 'carldup12@gmail.com',
            'last_name' => 'Dupuis', 
            'first_name' => 'Carl'
        ];
        $this->post('/api/signup', $user);
        $response = $this->post('/api/signup', $user);
        $response->assertStatus(INVALID_DATA);
    }

    public function test_loginOk200(): void
    {
        $user = [
            'login' => 'Carlos42',
            'password' => 'chat456',
            'email' => 'carldup12@gmail.com',
            'last_name' => 'Dupuis', 
            'first_name' => 'Carl'
        ];
        $loginInfos = [
            'login' => 'Carlos42',
            'password' => 'chat456'
        ];
        $this->post('/api/signup', $user);
        $response = $this->post('/api/signin', $loginInfos);
        $createdUser = User::where('login', 'Carlos42')->first();
        $this->assertAuthenticated();
        $this->assertEquals(1, $createdUser->tokens()->count());
        $this->assertArrayHasKey('token', $response->json());
        $response->assertStatus(OK);
    }

    public function test_loginBadInfosInLoginNoTokenReturned(): void
    {
        $user = [
            'login' => 'Carlos42',
            'password' => 'chat456',
            'email' => 'carldup12@gmail.com',
            'last_name' => 'Dupuis', 
            'first_name' => 'Carl'
        ];
        $loginInfos = [
            'login' => 'Charles2',
            'password' => 'chien87'
        ];
        $this->post('/api/signup', $user);
        $this->post('/api/signin', $loginInfos);
        $createdUser = User::where('login', 'Carlos42')->first();
        $this->assertEquals(0, $createdUser->tokens()->count());
    }

    public function test_signout204(): void
    {
           
        $user = [
            'login' => 'Carlos42',
            'password' => 'chat456',
            'email' => 'carldup12@gmail.com',
            'last_name' => 'Dupuis', 
            'first_name' => 'Carl'
        ];
        $loginInfos = [
            'login' => 'Carlos42',
            'password' => 'chat456'
        ];
        $this->post('/api/signup', $user);
        $this->post('/api/signin', $loginInfos);
        $response = $this->post('/api/signout');
        $this->assertDatabaseHas('users',[
            'email' => 'carldup12@gmail.com'
        ]);
        $createdUser = User::where('login', 'Carlos42')->first();
        $this->assertEquals(0, $createdUser->tokens()->count());
        $response->assertStatus(NO_CONTENT);
    }

    public function test_signinRoutesBombarding(): void
    {
        Sanctum::actingAs(
            User::factory()->create(), ['*']
            );
            
        for($i = 0; $i < 6; $i++){
            $response = $this->post('/api/signin');
        }

        $response->assertStatus(TOO_MANY_REQUEST);
    }

    public function test_signinRoutesBombardingToLimit(): void
    {
        Sanctum::actingAs(
            User::factory()->create(), ['*']
            );
            
        for($i = 0; $i < 5; $i++){
            $response = $this->post('/api/signin');
        }

        $response->assertStatus(SERVER_ERROR);
    }

    public function test_signupRoutesBombarding(): void
    {
        Sanctum::actingAs(
            User::factory()->create(), ['*']
            );
            
        for($i = 0; $i < 6; $i++){
            $response = $this->post('/api/signup');
        }

        $response->assertStatus(TOO_MANY_REQUEST);
    }

    public function test_signupRoutesBombardingToLimit(): void
    {
        Sanctum::actingAs(
            User::factory()->create(), ['*']
            );
            
        for($i = 0; $i < 5; $i++){
            $response = $this->post('/api/signup');
        }

        $response->assertStatus(SERVER_ERROR);
    }

    public function test_signoutRoutesBombarding(): void
    {
        Sanctum::actingAs(
            User::factory()->create(), ['*']
            );
            
        for($i = 0; $i < 6; $i++){
            $response = $this->post('/api/signout');
        }

        $response->assertStatus(TOO_MANY_REQUEST);
    }

    public function test_signoutRoutesBombardingToLimit(): void
    {
        Sanctum::actingAs(
            User::factory()->create(), ['*']
            );
            
        for($i = 0; $i < 5; $i++){
            $response = $this->post('/api/signout');
        }

        $response->assertStatus(NO_CONTENT);
    }
}
