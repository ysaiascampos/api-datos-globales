<?php

use App\Http\Controllers\Api\PersonaByBnfGrupFamiIdSolicitudExportController;
use App\Http\Controllers\Api\PersonaByFichaIdSolicitudExportController;
use App\Http\Controllers\Api\UltimaFechaCierreGFController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PoseeRSHController;
use App\Http\Controllers\Api\AlertasController;
use App\Http\Controllers\Api\GrupoFamiliarExportController;
use App\Http\Controllers\Api\IntegrantesGrupoFamiliarExportController;
use App\Http\Controllers\Api\MensajeTramoController;
use App\Http\Controllers\Api\personaExportController;
use App\Http\Controllers\Api\GrupoFamiliarExportByIdGrupoFamiliarController;
use App\Http\Controllers\Api\FuentesController;
use App\Http\Controllers\Api\personaIngresoPensionExportController;

Route::middleware([App\Http\Middleware\EnsureApiTokenIsValid::class])->prefix('v1')->group(function(){
    Route::get('/posee-rsh/{runCiudadano}',PoseeRSHController::class);
    Route::get('/alertas/{runCiudadano}',AlertasController::class);
    Route::get('/grupo-familiar-export/{runCiudadano}',GrupoFamiliarExportController::class);
    Route::get('/integrantes-grupo-familiar-export/{runCiudadano}',IntegrantesGrupoFamiliarExportController::class);
    Route::get('/mensaje-tramo',MensajeTramoController::class);
    Route::get('/persona-ficha-solicitud-export/{idSolicitud}',PersonaByFichaIdSolicitudExportController::class);
    Route::get('/persona-bnf-grup-fami-solicitud-export/{idSolicitud}',PersonaByBnfGrupFamiIdSolicitudExportController::class);
    Route::get('/persona-export/{runCiudadano}',personaExportController::class);
    Route::get('/persona-ingresos-pension-export/{runCiudadano}',personaIngresoPensionExportController::class);
    Route::get('/grupo-familiar-export-id-grupo-familiar/{IdGrupoFamiliar}',GrupoFamiliarExportByIdGrupoFamiliarController::class);
    Route::get('/fuentes',FuentesController::class);
    Route::get('/ultima-fecha-cierre-gf',UltimaFechaCierreGFController::class);
});
