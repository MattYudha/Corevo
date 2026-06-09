<?php
use App\Http\Controllers\Crm\ContactController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('crm')
    ->name('crm.')
    ->group(function () {
        // Menu Contacts
        Route::resource('contacts', ContactController::class);
    });
