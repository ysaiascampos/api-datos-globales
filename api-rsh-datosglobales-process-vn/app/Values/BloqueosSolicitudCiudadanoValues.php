<?php


namespace App\Values;

use App\Constantes\Constante;
use App\Strategies\BloqueosSolicitudCiudadano\ActualizacionModuloEducacion;
use App\Strategies\BloqueosSolicitudCiudadano\ActualizacionModuloOcupacionIngreso;
use App\Strategies\BloqueosSolicitudCiudadano\ActualizacionModuloSalud;
use App\Strategies\BloqueosSolicitudCiudadano\DesvinculacionIntegrante;
use App\Strategies\BloqueosSolicitudCiudadano\DomicilioVivienda;
use App\Strategies\BloqueosSolicitudCiudadano\IngresoRegistro;
use App\Strategies\BloqueosSolicitudCiudadano\IncorporacionIntegrante;
use App\Strategies\BloqueosSolicitudCiudadano\Vivienda;
use App\Strategies\BloqueosSolicitudCiudadano\CambioJefeHogar;

final class BloqueosSolicitudCiudadanoValues
{
    const STRATEGY = [
        Constante::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO => IngresoRegistro::class,
        Constante::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE => IncorporacionIntegrante::class,
        Constante::TIPO_SOLICITUD_4_DESVINCULACION_DE_INTEGRANTE => DesvinculacionIntegrante::class,
        Constante::TIPO_SOLICITUD_5_ACTUALIZACION_DE_MODULO_SALUD => ActualizacionModuloSalud::class,
        Constante::TIPO_SOLICITUD_6_ACTUALIZACION_DE_MODULO_EDUCACION => ActualizacionModuloEducacion::class,
        Constante::TIPO_SOLICITUD_7_ACTUALIZACION_DE_MODULO_VIVIENDA => Vivienda::class,
        Constante::TIPO_SOLICITUD_14_ACTUALIZACION_OCUPACION_DE_INGRESOS => ActualizacionModuloOcupacionIngreso::class,
        Constante::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA => DomicilioVivienda::class,
        Constante::TIPO_SOLICITUD_100_CAMBIO_JEFE_DE_HOGAR => CambioJefeHogar::class
    ];

}
