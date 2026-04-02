<?php

use App\Http\Controllers\admin\MangerController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\Manager\ClientController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\ReceptionistController;
use App\Http\Controllers\Receptionist\ApprovedClientController;
use App\Http\Controllers\Receptionist\ClientReservationController;
use App\Http\Controllers\Receptionist\DashboardController as ReceptionistDashboardController;
use App\Http\Controllers\Receptionist\PendingClientController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StatisticsController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\ApprovedClient;
use App\Http\Middleware\Manger;
use App\Http\Middleware\Receptionist;
use App\Http\Middleware\User;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    $featuredRooms = Room::available()
        ->with('floor:id,number')
        ->select('id', 'number', 'capacity', 'price_cents', 'floor_id')
        ->orderBy('price_cents')
        ->limit(3)
        ->get();

    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'availableRoomsCount' => Room::available()->count(),
        'featuredRooms' => $featuredRooms,
    ]);
})->name('home');

Route::middleware(['auth', 'verified', User::class])->group(function () {
    Route::get('dashboard', function (Request $request) {
        if (! $request->user()->is_approved) {
            return redirect()->route('pending-approval');
        }

        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('pending-approval', function (Request $request) {
        if ($request->user()->is_approved) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('PendingApproval');
    })->name('pending-approval');

    Route::middleware(ApprovedClient::class)->group(function () {
        Route::get('/rooms', [RoomController::class, 'publicIndex'])->name('rooms.index');
        Route::get('/rooms/{room}/reserve', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/my-reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    });
});

Route::middleware(['auth', 'verified', Manger::class])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::get('/dashboard', ManagerDashboardController::class)->name('dashboard');

        Route::resource('floors', FloorController::class)->except(['show']);
        Route::resource('rooms', RoomController::class)->except(['show']);

        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/statistics/gender', [StatisticsController::class, 'gender'])->name('statistics.gender');
        Route::get('/statistics/revenue', [StatisticsController::class, 'revenue'])->name('statistics.revenue');
        Route::get('/statistics/countries', [StatisticsController::class, 'countries'])->name('statistics.countries');
        Route::get('/statistics/top-clients', [StatisticsController::class, 'topClients'])->name('statistics.top-clients');

        Route::get('/receptionists', [ReceptionistController::class, 'index'])->name('receptionists.index');
        Route::get('/receptionists/create', [ReceptionistController::class, 'create'])->name('receptionists.create');
        Route::post('/receptionists', [ReceptionistController::class, 'store'])->name('receptionists.store');
        Route::get('/receptionists/{receptionist}/edit', [ReceptionistController::class, 'edit'])->name('receptionists.edit');
        Route::put('/receptionists/{receptionist}', [ReceptionistController::class, 'update'])->name('receptionists.update');
        Route::delete('/receptionists/{receptionist}', [ReceptionistController::class, 'destroy'])->name('receptionists.destroy');
        Route::patch('/receptionists/{receptionist}/ban', [ReceptionistController::class, 'ban'])->name('receptionists.ban');
        Route::patch('/receptionists/{receptionist}/unban', [ReceptionistController::class, 'unban'])->name('receptionists.unban');

        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
        Route::patch('/clients/{client}/approve', [ClientController::class, 'approve'])->name('clients.approve');
    });

Route::middleware(['auth', 'verified', Receptionist::class])
    ->prefix('receptionist')
    ->name('receptionist.')
    ->group(function () {
        Route::get('/dashboard', ReceptionistDashboardController::class)->name('dashboard');

        Route::get('/clients/pending', [PendingClientController::class, 'index'])->name('clients.pending');
        Route::patch('/clients/{client}/approve', [PendingClientController::class, 'approve'])->name('clients.approve');

        Route::get('/clients/approved', [ApprovedClientController::class, 'index'])->name('clients.approved');

        Route::get('/clients/reservations', [ClientReservationController::class, 'index'])->name('clients.reservations');
    });

Route::middleware(['auth', Admin::class, 'verified'])->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [StatisticsController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('users.toggle-ban');
        Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('users/{user}/toggle-role', [UserController::class, 'changRole'])->name('users.toggle-role');
        Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::resource('managers', MangerController::class);
        Route::resource('floors', FloorController::class)->except(['show']);
        Route::resource('rooms', RoomController::class)->except(['show']);
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/statistics/gender', [StatisticsController::class, 'gender'])->name('statistics.gender');
        Route::get('/statistics/revenue', [StatisticsController::class, 'revenue'])->name('statistics.revenue');
        Route::get('/statistics/countries', [StatisticsController::class, 'countries'])->name('statistics.countries');
        Route::get('/statistics/top-clients', [StatisticsController::class, 'topClients'])->name('statistics.top-clients');
        Route::get('/receptionists', [ReceptionistController::class, 'index'])->name('receptionists.index');
        Route::get('/receptionists/create', [ReceptionistController::class, 'create'])->name('receptionists.create');
        Route::post('/receptionists', [ReceptionistController::class, 'store'])->name('receptionists.store');
        Route::get('/receptionists/{receptionist}/edit', [ReceptionistController::class, 'edit'])->name('receptionists.edit');
        Route::put('/receptionists/{receptionist}', [ReceptionistController::class, 'update'])->name('receptionists.update');
        Route::delete('/receptionists/{receptionist}', [ReceptionistController::class, 'destroy'])->name('receptionists.destroy');
        Route::patch('/receptionists/{receptionist}/ban', [ReceptionistController::class, 'ban'])->name('receptionists.ban');
        Route::patch('/receptionists/{receptionist}/unban', [ReceptionistController::class, 'unban'])->name('receptionists.unban');
    });

require __DIR__.'/settings.php';
