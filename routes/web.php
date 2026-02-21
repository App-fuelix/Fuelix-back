<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reset-password', function () {
    return view('reset-password');
})->name('password.reset.form');

Route::get('/test-email', function () {
    Mail::raw('Test email from Fuelix - Mailtrap is working!', function ($message) {
        $message->to('test@example.com')
                ->subject('Mailtrap Test - Success');
    });
    
    return 'Test email sent! Check your Mailtrap inbox.';
});
