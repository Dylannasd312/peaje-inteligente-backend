
<?php
use App\Http\Controllers\AgenteWebController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\PasoController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\CasetaController;
use App\Http\Controllers\TarifaController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Vehículos
    Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::post('/vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::patch('/vehiculos/{vehiculo}/estado', [VehiculoController::class, 'cambiarEstado'])->name('vehiculos.estado');

    // Control de pasos
    Route::get('/pasos', [PasoController::class, 'index'])->name('pasos.index');
    Route::post('/pasos/procesar', [PasoController::class, 'procesar'])->name('pasos.procesar');
    Route::get('/pasos/{paso}', [PasoController::class, 'show'])->name('pasos.show');
    Route::get('/pasos/{paso}/evidencia', [PasoController::class, 'evidencia'])->name('pasos.evidencia');

    // Alertas
    Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::patch('/alertas/{alerta}/atender', [AlertaController::class, 'atender'])->name('alertas.atender');

    // Casetas
    Route::get('/casetas', [CasetaController::class, 'index'])->name('casetas.index');
    Route::post('/casetas', [CasetaController::class, 'store'])->name('casetas.store');
    Route::put('/casetas/{caseta}', [CasetaController::class, 'update'])->name('casetas.update');
    Route::patch('/casetas/{caseta}/estado', [CasetaController::class, 'cambiarEstado'])->name('casetas.estado');

    // Tarifas
    Route::get('/tarifas', [TarifaController::class, 'index'])->name('tarifas.index');
    Route::post('/tarifas', [TarifaController::class, 'store'])->name('tarifas.store');
    Route::put('/tarifas/{tarifa}', [TarifaController::class, 'update'])->name('tarifas.update');
    Route::patch('/tarifas/{tarifa}/estado', [TarifaController::class, 'cambiarEstado'])->name('tarifas.estado');

       Route::get('/agente', [AgenteWebController::class, 'index'])
        ->name('agente.index');

    Route::post('/agente/chat', [AgenteWebController::class, 'enviar'])
        ->name('agente.enviar');

    Route::delete('/agente/chat', [AgenteWebController::class, 'limpiar'])
        ->name('agente.limpiar');
});
