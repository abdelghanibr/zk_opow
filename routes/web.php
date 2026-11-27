<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\Biotime\PersonnelEmployee;
use App\Http\Controllers\Biotime\EmployeeController;
use App\Http\Controllers\Api\ZkBioEmployeeController;
use App\Http\Controllers\Biotime\EmployeeImportController;
use App\Http\Controllers\Biotime\AuthLoginController;
use App\Http\Controllers\Biotime\AttendanceController;
use App\Http\Controllers\Biotime\AuthUserController;

use App\Http\Controllers\Biotime\TvPunchController;
use App\Http\Controllers\Biotime\EmployeePhotoController ;
/*
|--------------------------------------------------------------------------
| TV Routes
|--------------------------------------------------------------------------
*/

// 📺 صفحة TV (HTML)
Route::get('/tv/last-punch', [TvPunchController::class, 'last'])
    ->name('tv.last.punch');

// 🔌 API JSON (بدون refresh)
Route::get('/tv/last-punch/json', [TvPunchController::class, 'lastJson'])
    ->name('tv.last.punch.json');
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------


| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/login', [AuthLoginController::class, 'showLoginForm'])
    ->name('login.form');

Route::post('/login', [AuthLoginController::class, 'login'])
    ->name('login.process');





Route::post('/logout', [AuthLoginController::class, 'logout'])
    ->name('logout');    

/* Création compte */
Route::resource(
    'biotime/auth-users',
    AuthUserController::class
)->names('biotime.auth-users');

Route::get('/employees/photo/download/{id}',  [EmployeePhotoController ::class, 'download']
)->name('employee.photo.download');

Route::middleware('auth.zk')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

Route::get('/attendance', [AttendanceController::class, 'index'])
    ->name('biotime.attendance.index');


Route::prefix('biotime/employees')->name('biotime.employees.')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('index');
    Route::get('/create', [EmployeeController::class, 'create'])->name('create');
    Route::post('/', [EmployeeController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('edit');
    Route::put('/{id}', [EmployeeController::class, 'update'])->name('update');



// web.php




Route::post(
    '/biotime/employees/api-store',
    [EmployeeController::class, 'storeViaApi']
)->name('biotime.employees.api.store');
});  

Route::delete('biotime/employees/{id}',
    [EmployeeController::class, 'destroy'])
    ->name('biotime.employees.destroy');

Route::put(
    '/biotime/employees/{empCode}/disable',
    [EmployeeController::class, 'disable']
)->name('biotime.employees.disable');
Route::post(
    '/biotime/import/complex/{complex}',
    [EmployeeImportController::class, 'importFromRemote']
)->name('biotime.import.complex');
Route::get(
    '/test-remote-api/{complex}',
    [EmployeeImportController::class, 'testRemoteApi']
);
// routes/web.php
// Preview (GET)
Route::get(
    '/biotime/employees/preview/{complex}',
    [EmployeeImportController::class, 'preview']
)->name('biotime.employees.preview');

// Import : l'URL ouverte directement (GET) renvoie vers l'aperçu
Route::get(
    '/biotime/employees/import/{complex}',
    fn ($complex) => redirect()->route('biotime.employees.preview', $complex)
)->name('biotime.employees.import');
Route::post(
            '/biotime/employees/import/{complex}',
            [EmployeeImportController::class, 'execute']
        )->name('biotime.import.execute');

Route::post(
    '/biotime/employees/import/{complex}/one',
    [EmployeeImportController::class, 'importOne']
)->name('biotime.import.one');


});


/*Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');
     Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    
     Route::resource('devices', DeviceController::class);

     Route::get('/devices/{device}/connect', [DeviceController::class, 'connect'])
     ->name('devices.connect');    
Route::get(
    'devices/{device}/zk-users',
    [DeviceController::class, 'fetchZkAttendance']
)->name('devices.zkUsers');

Route::post(
    'devices/{device}/import-selected-users',
    [DeviceController::class, 'importSelectedUsers']
)->name('devices.importSelectedUsers');




});*/

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Connexion PostgreSQL OK";
    } catch (\Exception $e) {
        return "❌ Erreur: " . $e->getMessage();
    }
});

Route::get('/zk/test', function () {
    $zk = new ZKTeco('10.32.18.10', 4370);
    return $zk->connect() ? 'CONNECTED' : 'FAILED';
});
Route::get('/phpinfo', function () {
    phpinfo();
});

/*
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



*/

