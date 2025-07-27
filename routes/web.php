<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', \App\Livewire\Settings\Profile::class)->name('settings.profile');
    Route::get('settings/password', \App\Livewire\Settings\Password::class)->name('settings.password');
    Route::get('settings/appearance', \App\Livewire\Settings\Appearance::class)->name('settings.appearance');

    Route::get('organizations', \App\Livewire\Organization\Index::class)->name('organization.index');
    Route::get('organizations/{organization}', \App\Livewire\Organization\Show::class)->name('organization.show');
    Route::get('teams', \App\Livewire\Teams\Index::class)->name('teams.index');
    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
});

require __DIR__ . '/auth.php';
