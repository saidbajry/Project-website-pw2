<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SuggestController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExecutiveController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

// ---------- Publik: katalog, detail, simulasi kredit ----------
Route::get('/', [CatalogController::class, 'index'])->name('katalog');
Route::get('/kendaraan/{vehicle}', [CatalogController::class, 'show'])->name('kendaraan');
Route::post('/kendaraan/{vehicle}/minat', [CatalogController::class, 'interest'])->name('kendaraan.minat');

Route::get('/suggest/katalog', [SuggestController::class, 'catalog'])->name('suggest.katalog')->middleware('throttle:90,1');

// ---------- Autentikasi (pelanggan, staf/admin, pemilik dealer) ----------
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'form'])->name('login')->defaults('portal', 'customer');
    Route::post('/masuk', [AuthController::class, 'login'])->defaults('portal', 'customer');
    Route::get('/daftar', [AuthController::class, 'registerForm'])->name('daftar');
    Route::post('/daftar', [AuthController::class, 'register']);
    Route::get('/sim/masuk', [AuthController::class, 'form'])->name('sim.login')->defaults('portal', 'staff');
    Route::post('/sim/masuk', [AuthController::class, 'login'])->defaults('portal', 'staff');
    Route::get('/lupa-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'email'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'form'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
    Route::get('/eksekutif/masuk', [AuthController::class, 'form'])->name('eksekutif.login')->defaults('portal', 'owner');
    Route::post('/eksekutif/masuk', [AuthController::class, 'login'])->defaults('portal', 'owner');
});
Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');
    Route::get('/akun/password', [AccountController::class, 'form'])->name('akun.password');
    Route::put('/akun/password', [AccountController::class, 'update'])->name('akun.password.update');
});

// ---------- Portal pelanggan ----------
Route::middleware('role:customer')->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/garasi', [CustomerController::class, 'garage'])->name('garasi');
    Route::post('/garasi', [CustomerController::class, 'garageStore'])->name('garasi.simpan');
    Route::patch('/garasi/{garasi}', [CustomerController::class, 'garageKm'])->name('garasi.km');
    Route::get('/booking', [CustomerController::class, 'booking'])->name('booking');
    Route::post('/booking', [CustomerController::class, 'bookingStore'])->name('booking.simpan');
    Route::patch('/booking/{booking}/batal', [CustomerController::class, 'bookingCancel'])->name('booking.batal');
    Route::get('/riwayat', [CustomerController::class, 'history'])->name('riwayat');
    Route::get('/prediksi', [CustomerController::class, 'prediction'])->name('prediksi');
});

// ---------- SIM internal (staf/admin) ----------
Route::middleware('role:staff')->prefix('sim')->name('sim.')->group(function () {
    Route::get('/', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/crm', [StaffController::class, 'crm'])->name('crm');
    Route::patch('/crm/{prospect}', [StaffController::class, 'crmUpdate'])->name('crm.update');
    Route::get('/bengkel', [StaffController::class, 'workshop'])->name('bengkel');
    Route::patch('/bengkel/booking/{booking}/konfirmasi', [StaffController::class, 'bookingConfirm'])->name('booking.konfirmasi');
    Route::post('/bengkel/booking/{booking}/wo', [StaffController::class, 'woCreate'])->name('wo.buat');
    Route::get('/bengkel/wo/{wo}', [StaffController::class, 'woShow'])->name('wo.show');
    Route::patch('/bengkel/wo/{wo}', [StaffController::class, 'woUpdate'])->name('wo.update');
    Route::post('/bengkel/wo/{wo}/part', [StaffController::class, 'woAddPart'])->name('wo.part');
    Route::get('/inventaris', [StaffController::class, 'inventory'])->name('inventaris');
    Route::post('/inventaris', [StaffController::class, 'partStore'])->name('inventaris.simpan');
    Route::patch('/inventaris/{part}/restok', [StaffController::class, 'restock'])->name('inventaris.restok');
    Route::patch('/inventaris/po/{po}/{aksi}', [StaffController::class, 'poAction'])->name('po.aksi');
    Route::get('/suggest/suku-cadang', [SuggestController::class, 'parts'])->name('suggest.suku');
    Route::get('/cms', [StaffController::class, 'cms'])->name('cms');
    Route::get('/cms/kendaraan/baru', [StaffController::class, 'vehicleForm'])->name('cms.kendaraan.baru');
    Route::get('/cms/kendaraan/{vehicle}/ubah', [StaffController::class, 'vehicleForm'])->name('cms.kendaraan.ubah');
    Route::post('/cms/kendaraan/{vehicle?}', [StaffController::class, 'vehicleSave'])->name('cms.kendaraan.simpan');
    Route::delete('/cms/kendaraan/{vehicle}', [StaffController::class, 'vehicleDelete'])->name('cms.kendaraan.hapus');
    Route::post('/cms/post', [StaffController::class, 'postStore'])->name('cms.post.simpan');
    Route::patch('/cms/post/{post}', [StaffController::class, 'postToggle'])->name('cms.post.toggle');
    Route::delete('/cms/post/{post}', [StaffController::class, 'postDelete'])->name('cms.post.hapus');
});

// ---------- Dashboard eksekutif (pemilik dealer) ----------
Route::middleware('role:owner')->prefix('eksekutif')->name('eksekutif.')->group(function () {
    Route::get('/', [ExecutiveController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan', [ExecutiveController::class, 'report'])->name('laporan');
});
