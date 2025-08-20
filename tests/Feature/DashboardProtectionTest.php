<?php

namespace AndreasElia\Analytics\Tests\Feature;

use AndreasElia\Analytics\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;

class DashboardProtectionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function dashboard_is_accessible_when_protection_is_disabled()
    {
        Config::set('analytics.protected', false);
        
        $response = $this->get('/analytics');
        
        $response->assertStatus(200);
    }

    #[Test]
    public function dashboard_requires_authentication_when_protection_is_enabled()
    {
        Config::set('analytics.protected', true);
        
        $response = $this->get('/analytics');
        
        $response->assertStatus(302);
    }

    #[Test]
    public function authenticated_user_can_access_protected_dashboard()
    {
        Config::set('analytics.protected', true);
        
        $user = $this->createUser();
        $response = $this->actingAs($user)->get('/analytics');
        
        $response->assertStatus(200);
    }

    #[Test]
    public function custom_protection_middleware_is_applied()
    {
        Config::set('analytics.protected', true);
        Config::set('analytics.protection_middleware', ['auth', 'verified']);
        
        $user = $this->createUser(['email_verified_at' => null]);
        $response = $this->actingAs($user)->get('/analytics');
        
        $response->assertStatus(302);
    }

    private function createUser($attributes = [])
    {
        return \Illuminate\Foundation\Auth\User::factory()->create($attributes);
    }
}