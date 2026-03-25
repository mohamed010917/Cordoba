<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\Manger;
use App\Http\Middleware\User;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');
// user routes
Route::middleware(['auth', 'verified' , User::class])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});


// manger routes

Route::middleware(['auth', Manger::class , 'verified'])->prefix('manager')
->name('manager.')
->group(function (){
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
}) ; 

// admin routes
Route::middleware(['auth', Admin::class , 'verified'])->prefix('admin')
->name('admin.')
->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
}) ;



require __DIR__.'/settings.php';
