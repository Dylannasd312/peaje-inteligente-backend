<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConsultaPeajeController;
use App\Http\Controllers\Api\AgenteController;

Route::prefix('v1')->group(function () {

    // Autenticación
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {

        // Sesión
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/usuario', [AuthController::class, 'usuario']);

        // Consultas de vehículos
        Route::post('/placas/consultar', [
            ConsultaPeajeController::class,
            'consultarPlaca'
        ]);

        Route::get('/placas/{placa}/historial', [
            ConsultaPeajeController::class,
            'historialPlaca'
        ]);

        // Consultas de casetas
        Route::get('/casetas/{id}/resumen', [
            ConsultaPeajeController::class,
            'resumenCaseta'
        ]);

        // Agente inteligente
        Route::post('/agente/chat', [
            AgenteController::class,
            'chat'
        ]);
    });
});
