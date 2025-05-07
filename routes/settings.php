<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\PersonalAccessTokensController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance');

    Route::get('settings/tokens', [PersonalAccessTokensController::class, 'index'])->name('tokens.index');
    Route::post('settings/tokens', [PersonalAccessTokensController::class, 'store'])->name('tokens.store');
    Route::delete('settings/tokens/{token}', [PersonalAccessTokensController::class, 'destroy'])->name('tokens.destroy');
    Route::get('settings/tokens/{token}', [PersonalAccessTokensController::class, 'show'])->name('tokens.show');
    Route::get('settings/tokens/{token}/regenerate', [PersonalAccessTokensController::class, 'regenerate'])->name('tokens.regenerate');
    Route::get('settings/tokens/{token}/revoke', [PersonalAccessTokensController::class, 'revoke'])->name('tokens.revoke');
});
