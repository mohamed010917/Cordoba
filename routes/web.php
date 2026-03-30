<?php

use App\Http\Controllers\admin\MangerController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Manger;
use App\Http\Middleware\Receptionist;
use App\Http\Middleware\User;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified', User::class])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}/reserve', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/my-reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

Route::middleware(['auth', Manger::class, 'verified'])->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
        Route::resource('floors', FloorController::class)->except(['show']);
    });

Route::middleware(['auth', Receptionist::class, 'verified'])->prefix('receptionist')
    ->name('receptionist.')
    ->group(function () {
        Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
    });

Route::middleware(['auth', Admin::class, 'verified'])->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('users.toggle-ban');
        Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('users/{user}/toggle-role', [UserController::class, 'changRole'])->name('users.toggle-role');
        Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::resource('mangers', MangerController::class);
    });

require __DIR__.'/settings.php';
