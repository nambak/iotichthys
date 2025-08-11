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
    Route::get('users', \App\Livewire\Users\Index::class)->name('users.index');
    Route::get('roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
    Route::get('permissions', \App\Livewire\Permissions\Index::class)->name('permissions.index');
    Route::get('permissions/{permission}', \App\Livewire\Permissions\Show::class)->name('permissions.show');
    Route::get('categories', \App\Livewire\Category\Index::class)->name('category.index');
    Route::get('categories/{category}', \App\Livewire\Category\Show::class)->name('category.show');
});

require __DIR__ . '/auth.php';
