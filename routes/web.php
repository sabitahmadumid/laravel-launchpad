<?php

use Illuminate\Support\Facades\Route;
use SabitAhmad\LaravelLaunchpad\Http\Controllers\InstallationController;
use SabitAhmad\LaravelLaunchpad\Http\Controllers\UpdateController;

// Installation Routes
Route::group([
    'prefix' => config('launchpad.installation.route_prefix', 'install'),
    'middleware' => array_merge(
        config('launchpad.installation.route_middleware', ['web']),
        ['redirect.if.installed']
    ),
], function () {
    Route::get('/', [InstallationController::class, 'welcome'])->name('launchpad.install.welcome');
    Route::get('/requirements', [InstallationController::class, 'requirements'])->name('launchpad.install.requirements');
    Route::post('/requirements/check', [InstallationController::class, 'checkRequirements'])->name('launchpad.install.requirements.check');
    
    Route::get('/license', [InstallationController::class, 'license'])->name('launchpad.install.license');
    Route::post('/license/verify', [InstallationController::class, 'verifyLicense'])->name('launchpad.install.license.verify');
    
    Route::get('/database', [InstallationController::class, 'database'])->name('launchpad.install.database');
    Route::post('/database/test', [InstallationController::class, 'testDatabase'])->name('launchpad.install.database.test');
    
    Route::get('/admin', [InstallationController::class, 'admin'])->name('launchpad.install.admin');
    Route::post('/admin/create', [InstallationController::class, 'createAdmin'])->name('launchpad.install.admin.create');
    
    Route::get('/final', [InstallationController::class, 'final'])->name('launchpad.install.final');
    Route::post('/complete', [InstallationController::class, 'complete'])->name('launchpad.install.complete');
    
    Route::get('/success', [InstallationController::class, 'success'])->name('launchpad.install.success');
});

// Update Routes
Route::group([
    'prefix' => config('launchpad.update.route_prefix', 'update'),
    'middleware' => config('launchpad.update.route_middleware', ['web']),
], function () {
    Route::get('/', [UpdateController::class, 'welcome'])->name('launchpad.update.welcome');
    Route::get('/requirements', [UpdateController::class, 'requirements'])->name('launchpad.update.requirements');
    Route::post('/requirements/check', [UpdateController::class, 'checkRequirements'])->name('launchpad.update.requirements.check');
    
    Route::get('/license', [UpdateController::class, 'license'])->name('launchpad.update.license');
    Route::post('/license/verify', [UpdateController::class, 'verifyLicense'])->name('launchpad.update.license.verify');
    
    Route::get('/update', [UpdateController::class, 'update'])->name('launchpad.update.update');
    Route::post('/run', [UpdateController::class, 'runUpdate'])->name('launchpad.update.run');
    
    Route::get('/success', [UpdateController::class, 'success'])->name('launchpad.update.success');
});
