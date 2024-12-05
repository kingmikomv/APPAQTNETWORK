<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IPController;
use App\Http\Controllers\MKController;
use App\Http\Controllers\OLTController;
use App\Http\Controllers\VPNController;
use App\Http\Controllers\DepanController;
use App\Http\Controllers\UndianController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DepanController::class, 'index'])->name('indexdepan');



Auth::routes(['verify' => true, 'reset' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// VPN Routes
Route::group(['prefix' => '/home/datavpn', 'middleware' => ['auth', 'verified']], function() {
    Route::controller(VPNController::class)->group(function () {
        Route::get('/', 'index')->name('datavpn');
        Route::post('/uploadvpn', 'uploadvpn')->name('uploadvpn');
        Route::delete('/{id}', 'hapusvpn')->name('hapusvpn');
    });
});

// MIKROTIK Routes
Route::group(['prefix' => '/home/datamikrotik', 'middleware' => ['auth', 'verified']], function() {
    Route::controller(MKController::class)->group(function () {
        Route::get('/', 'index')->name('datamikrotik');
        Route::post('/tambahmikrotik', 'tambahmikrotik')->name('tambahmikrotik');
        Route::get('/aksesmikrotik', 'aksesMikrotik')->name('aksesmikrotik');
        Route::get('/masukmikrotik', 'masukmikrotik')->name('masukmikrotik');
        Route::get('/dashboardmikrotik', 'dashboardmikrotik')->name('dashboardmikrotik');
        Route::post('/keluarmikrotik', 'keluarmikrotik')->name('keluarmikrotik');
        Route::get('/monitoring/active-connection', 'getActiveConnection')->name('active-connection');
        Route::get('/monitoring/active-connection/traffic', 'getTrafficData')->name('mikrotik.traffic');
        Route::post('/monitoring/add-firewall-rule', 'addFirewallRule')->name('addFirewallRule');
        Route::post('/monitoring/restartmodem', 'restartmodem')->name('restartmodem');
        Route::get('/edit/{id}', 'edit')->name('mikrotik.edit');
        Route::post('/{id}/update', 'update')->name('mikrotik.update');
        Route::delete('/delete/{id}', 'destroy')->name('mikrotik.delete');
    });
});

// OLT Routes
Route::group(['prefix' => '/home/dataolt', 'middleware' => ['auth', 'verified']], function() {
    Route::controller(OLTController::class)->group(function () {
        Route::get('/', 'index')->name('dataolt');
        Route::post('/tambaholt', 'tambaholt')->name('tambaholt');
        Route::get('/aksesolt', 'aksesOLT')->name('aksesolt');
        Route::get('/{id}/hapusolt', 'hapusolt')->name('hapusolt');
    });
});

// Additional MIKROTIK Routes for IPController
Route::group(['prefix' => '/home/datamikrotik/ppp', 'middleware' => ['auth', 'verified']], function() {
    Route::controller(IPController::class)->group(function () {
        Route::get('/aksessecret', 'aksessecret')->name('aksessecret');
        Route::post('/aksessecret/add-secret', 'store')->name('store');
        Route::delete('/aksessecret/secrets/{id}', 'destroy')->name('secrets.destroy');
    });
});

Route::group(['prefix' => '/home/datamikrotik/iface', 'middleware' => ['auth', 'verified']], function() {
    Route::controller(IPController::class)->group(function () {
        Route::get('/aksesnightbore', 'aksesnightbore')->name('aksesnightbore');
    });
});

Route::group(['prefix' => '/home/datamikrotik/iface/', 'middleware' => ['auth', 'verified']], function() {
    Route::controller(IPController::class)->group(function () {
        Route::get('/aksesinterface', 'aksesinterface')->name('aksesinterface');
        Route::post('/aksesinterface/{id}/enable', 'enable')->name('interface.enable');
        Route::post('/aksesinterface/{id}/disable', 'disable')->name('interface.disable');
    });
});

Route::group(['prefix' => '/home/datamikrotik/monitoring', 'middleware' => ['auth', 'verified']], function() {
    Route::controller(IPController::class)->group(function () {
        Route::get('/aksesschedule', 'aksesschedule')->name('aksesschedule');
        // Route::post('/aksesinterface/{id}/enable', 'enable')->name('interface.enable');
        // Route::post('/aksesinterface/{id}/disable', 'disable')->name('interface.disable');
    });
});

Route::group(['prefix' => '/home/datamikrotik/hotspot/', 'middleware' => ['auth', 'verified']], function() {
    Route::controller(IPController::class)->group(function () {
        Route::get('/aksesactivehotspot', 'aksesactivehotspot')->name('aksesactivehotspot');
        Route::post('/disconnect-hotspot', 'disconnectHotspot')->name('disconnect.hotspot');
        Route::get('/aksesuserhotspot', 'aksesuserhotspot')->name('aksesuserhotspot');
        Route::post('/generateHotspot', 'generateHotspot')->name('generateHotspot');

    });
});

Route::group(['prefix' => '/home/undian', 'middleware' => ['auth', 'verified', 'can:isAdmin']], function() {
    Route::controller(UndianController::class)->group(function () {
        Route::get('/', 'index')->name('undianadmin');
        Route::post('/buatundian', 'buatundian')->name('buatundian');
    });
});

// MIKROTIK CPU and Status Routes
Route::get('/mikrotik/cpu-load/{ipmikrotik}', [MKController::class, 'getCpuLoad'])->middleware(['auth', 'verified']);
Route::get('/mikrotik/current-time/{ipmikrotik}', [MKController::class, 'getCurrentTime']);
Route::get('/mikrotik/interfaces', [MKController::class, 'dashboardmikrotik']);
Route::get('/mikrotik/traffic', [MKController::class, 'getTraffic']);
Route::get('/mikrotik/uptime/{ipmikrotik}', [MKController::class, 'getUptime']);