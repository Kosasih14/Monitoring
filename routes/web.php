<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\HistoryController;

Route::prefix('history')->group(function () {
    Route::get('{studentId}', [HistoryController::class, 'index'])->name(name: 'history');
    Route::post('{studentId}', [HistoryController::class, 'store'])->name('history.store');
    Route::get('/history/{id}', [HistoryController::class, 'index'])->name('history');
});
Route::post('/history/{id}/reset-saldo', [HistoryController::class, 'resetOnlySaldo'])->name('history.resetOnlySaldo');

// Route untuk hapus semua transaksi (DELETE method)
Route::delete('/history/{id}/delete-transactions', [HistoryController::class, 'deleteAllTransactions'])->name('history.deleteAllTransactions');

Route::prefix('firebase')->name('firebase.')->group(function () {
    Route::get('/', [FirebaseController::class, 'index'])->name('index');
    Route::get('/transaksi/{id}', [FirebaseController::class, 'transaksi'])->name('transaksi');
    Route::post('/transaksi/{id}', [FirebaseController::class, 'storeTransaksi'])->name('transaksi.store');
});

Route::prefix('api')->group(function () {
    Route::get('student/{id}/balance', [StudentController::class, 'getBalance']);
    Route::get('student/{id}/balance-firebase', [FirebaseController::class, 'getStudentBalance']);
    Route::post('recalculate-balances', [StudentController::class, 'recalculateAllBalances']);
});

Route::get('/students/{id}/get-saldo', [StudentController::class, 'getBalance'])->name('students.getSaldo');
Route::get('/students/recalculate', [StudentController::class, 'recalculateAllBalances'])->name('students.recalculate');

Route::get('/report/{studentId}', [ReportController::class, 'show'])->name('report.show');
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');


// Halaman Home (sebelum login)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman Login (frontend dengan Firebase)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Rute logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute dashboard (setelah login, disesuaikan nanti)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
