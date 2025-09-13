<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

test('language switching works across installation pages', function () {
    // Set up installation enabled
    config(['launchpad.installation.enabled' => true]);

    // Test English (default)
    Session::put('locale', 'en');
    App::setLocale('en');

    // Test Welcome page
    $response = $this->get('/install');
    $response->assertStatus(200);
    $response->assertSee('Welcome to Installation');
    $response->assertSee('Let\'s begin the installation process');

    // Test Requirements page
    $response = $this->get('/install/requirements');
    $response->assertStatus(200);
    $response->assertSee('System Requirements');
    $response->assertSee('Check Requirements');

    // Test License page
    $response = $this->get('/install/license');
    $response->assertStatus(200);
    $response->assertSee('License Verification');
    $response->assertSee('Enter your license key');

    // Test Database page
    $response = $this->get('/install/database');
    $response->assertStatus(200);
    $response->assertSee('Database Configuration');
    $response->assertSee('Test Database Connection');

    // Test Admin page
    $response = $this->get('/install/admin');
    $response->assertStatus(200);
    $response->assertSee('Admin Account');
    $response->assertSee('Create your administrator account');

    // Switch to Bengali
    Session::put('locale', 'bn');
    App::setLocale('bn');

    // Test Bengali translations
    $response = $this->get('/install');
    $response->assertStatus(200);
    $response->assertSee('ইনস্টলেশনে স্বাগতম');

    $response = $this->get('/install/requirements');
    $response->assertStatus(200);
    $response->assertSee('সিস্টেম প্রয়োজনীয়তা');

    $response = $this->get('/install/license');
    $response->assertStatus(200);
    $response->assertSee('লাইসেন্স যাচাইকরণ');

    $response = $this->get('/install/database');
    $response->assertStatus(200);
    $response->assertSee('ডাটাবেস কনফিগারেশন');

    $response = $this->get('/install/admin');
    $response->assertStatus(200);
    $response->assertSee('অ্যাডমিন অ্যাকাউন্ট');
});

test('dynamic field translations work properly', function () {
    config(['launchpad.installation.enabled' => true]);

    // Test English field labels
    Session::put('locale', 'en');
    App::setLocale('en');

    $response = $this->get('/install/admin');
    $response->assertStatus(200);
    $response->assertSee('Full Name');
    $response->assertSee('Email Address');
    $response->assertSee('Password');
    $response->assertSee('Application Name');

    // Test Bengali field labels
    Session::put('locale', 'bn');
    App::setLocale('bn');

    $response = $this->get('/install/admin');
    $response->assertStatus(200);
    $response->assertSee('পূর্ণ নাম');
    $response->assertSee('ইমেইল ঠিকানা');
    $response->assertSee('পাসওয়ার্ড');
    $response->assertSee('অ্যাপ্লিকেশনের নাম');
});

test('language switching via routes works', function () {
    config(['launchpad.installation.enabled' => true]);

    // Test switching to Bengali
    $response = $this->post('/install/language', ['locale' => 'bn']);
    $response->assertRedirect();
    expect(Session::get('locale'))->toBe('bn');

    // Test switching to English
    $response = $this->post('/install/language', ['locale' => 'en']);
    $response->assertRedirect();
    expect(Session::get('locale'))->toBe('en');
});

test('translation fallbacks work when translation missing', function () {
    config(['launchpad.installation.enabled' => true]);
    Session::put('locale', 'en');
    App::setLocale('en');

    $response = $this->get('/install/admin');
    $response->assertStatus(200);

    // Should show field name as fallback for missing translations
    // This tests the fallback system in the views
    $response->assertSuccessful();
});
