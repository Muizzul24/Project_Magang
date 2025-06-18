<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubstansiController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\DasarSuratController;
use App\Http\Controllers\ParafSuratController;

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| DASHBOARD ROUTE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('substansis', SubstansiController::class);
    Route::post('/users', [UserController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| ADMIN AND OPERATOR ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,operator'])->group(function () {
    // Agenda khusus (arsip dan arsip otomatis)
    Route::get('/agendas/arsip', [AgendaController::class, 'arsip'])->name('agendas.arsip');
    Route::post('/agendas/arsip', [AgendaController::class, 'arsipAgendaTerlewat'])->name('agendas.arsipAgendaTerlewat');
    
    // Tambahkan route ini di file routes/web.php
    Route::delete('/agendas/{agenda}/delete-file/{index}', [AgendaController::class, 'deleteSurat'])->name('agendas.delete-file');

    // API untuk mengambil pegawai berdasarkan substansi (dipakai AJAX)
    Route::get('/agendas/getPegawaiBySubstansi/{substansiId}', [AgendaController::class, 'getPegawaiBySubstansi']);
    Route::get('/pegawais/by-substansi/{substansi}', [PegawaiController::class, 'getBySubstansi']);
    Route::get('/getPenandatanganBySubstansi/{id}', [PegawaiController::class, 'getPenandatanganBySubstansi']);
    Route::get('/getPenandatanganBySubstansi/{id}', [SuratTugasController::class, 'getPenandatanganBySubstansi']);

    // Surat Tugas: CRUD dan API
    Route::resource('surat_tugas', SuratTugasController::class);
    Route::get('surat_tugas/getPegawaiBySubstansi/{substansi_id}', [SuratTugasController::class, 'getPegawaiBySubstansi'])->name('surat_tugas.getPegawaiBySubstansi');

    // Dasar Surat: CRUD
    Route::resource('dasarSurat', DasarSuratController::class);
    Route::resource('parafSurat', ParafSuratController::class);

    // Resource untuk agenda, pegawai, substansi
    Route::resource('agendas', AgendaController::class)->whereNumber('agenda');
    Route::resource('pegawais', PegawaiController::class);
});

/*
|--------------------------------------------------------------------------
| AGENDA PUBLIC (Untuk Semua Yang Login - Hanya LIHAT agenda)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,operator,anggota'])->group(function () {
    Route::get('/agendas', [AgendaController::class, 'index'])->name('agendas.index');
    Route::get('/agendas/{agenda}', [AgendaController::class, 'show'])->name('agendas.show');
    Route::get('/agendas/{agenda}/surat-tugas', [AgendaController::class, 'SuratTugas'])->name('agendas.surat_tugas');
    Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender.index');
});

/*
|--------------------------------------------------------------------------
| WELCOME PAGE
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});