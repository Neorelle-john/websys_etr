<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttencdanceController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('login', [LoginController::class, 'loginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::resource('employees', EmployeeController::class);
Route::get('employees/{id}/download-qr', [EmployeeController::class, 'downloadQrCode'])->name('employees.downloadQrCode');
Route::get('/export-form', [AttencdanceController::class, 'showExportForm'])->name('export.form');

Route::get('/export-individual', [AttencdanceController::class, 'exportIndividual'])->name('export.individual');
Route::view('/scan', 'scan.scanView');
Route::post('/save-attendance', [AttencdanceController::class, 'store']);
Route::get('/dashboard', [AttencdanceController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [EmployeeController::class, 'logout'])->name('logout');
Route::get('/monthly-report', [AttencdanceController::class, 'viewMonthlyReport']);
Route::get('/attendance/report', [AttencdanceController::class, 'viewMonthlyReport'])->name('attendance.report');
Route::get('/download-grouped-excel', [AttencdanceController::class, 'downloadGroupedSheet'])->name('attendance.excel');
