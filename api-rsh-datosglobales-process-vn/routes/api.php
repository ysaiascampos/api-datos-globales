<?php

use App\Http\Controllers\Api\CartolaEmitidaController;
use App\Http\Controllers\Api\ConfirmarDatosRSHController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PoseeRSHController;
use App\Http\Controllers\Api\DatosDeContactoController;
use App\Http\Controllers\Api\SituacionBasicaController;
use App\Http\Controllers\Api\AlertasController;
use App\Http\Controllers\Api\PendientesCiudadanoController;
use App\Http\Controllers\Api\TramitesPendientesController;
use App\Http\Controllers\Api\ObtenerSolicitudesController;
use App\Http\Controllers\Api\BloqueosSolicitudCiudadanoController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\Api\TramoController;
use App\Http\Controllers\Api\ValidaSolicitudTerceroController;
use App\Http\Controllers\Api\ComunaController;
use App\Http\Controllers\Api\DesistirSolicitudCiudadanoController;
use App\Http\Controllers\Api\SolicitudesTramitablesController;
use App\Http\Controllers\Api\InstitucionalizadoController;
use App\Http\Controllers\Api\SaveRememberTokenController;
use App\Http\Controllers\Api\ValidaRememberTokenController;
use App\Http\Controllers\Api\UsuarioAsignacionController;
use App\Http\Controllers\Api\RunificadorController;

Route::middleware([App\Http\Middleware\EnsureApiTokenIsValid::class])->prefix('v1')->group(function(){
    Route::get('/getposeersh/{runCiudadano}', PoseeRSHController::class);
    Route::get('/getdatosdecontacto/{runCiudadano}', DatosDeContactoController::class);
    Route::get('/getsituacionbasica/{runCiudadano}', SituacionBasicaController::class);
    Route::get('/getalertas/{runCiudadano}', AlertasController::class);
    Route::get('/gettramitespendientes/{runCiudadano}', TramitesPendientesController::class);

    Route::get('/getpendientesciudadano/{runCiudadano}', PendientesCiudadanoController::class);
    Route::get('/getsolicitudesciudadano/{runCiudadano}', ObtenerSolicitudesController::class);
    Route::post('/getbloqueossolicitudciudadono', BloqueosSolicitudCiudadanoController::class);
    Route::post('/getbloqueossolicitudciudadano', BloqueosSolicitudCiudadanoController::class);
    Route::get('/getpersona/{runCiudadano}', PersonaController::class);
    Route::get('/gettramo/{runCiudadano}', TramoController::class);
    Route::get('/getvalidasolicitudtercero/{runCiudadano}', ValidaSolicitudTerceroController::class);
    Route::get('/getcomuna/{runCiudadano}', ComunaController::class);
    Route::post('/getdesistirsolicitudciudadano', DesistirSolicitudCiudadanoController::class);
    Route::get('/getsolicitudestramitables/{runCiudadano}', SolicitudesTramitablesController::class);
    Route::get('/getinstitucionalizado/{runCiudadano}', InstitucionalizadoController::class);

    Route::post('/generar-remember-token', SaveRememberTokenController::class);
    Route::post('/validar-remember-token', ValidaRememberTokenController::class);
    Route::post('/usuario-asignacion', UsuarioAsignacionController::class);

    Route::get('/getrunificador/{runCiudadano}/{dv}', RunificadorController::class);
    Route::post('/codigo-cartola-emitida', [CartolaEmitidaController::class, 'store']);
    Route::post('/validar-cartola-emitida', [CartolaEmitidaController::class, 'find']);
    Route::get('/getconfirmardatosrsh/{runCiudadano}', ConfirmarDatosRSHController::class);
});
