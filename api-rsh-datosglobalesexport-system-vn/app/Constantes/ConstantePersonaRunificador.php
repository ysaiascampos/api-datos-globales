<?php

namespace App\Constantes;

/**
 * Class Constante
 * EJ: Constante::STATUS_200_SUCCESS_OK;
 *
 * Para cambiar nombre se debe presionar SHIFT+F6 sobre la constante
 *    así el cambio se refleja en todas los archivos donde se llama
 */
abstract class ConstantePersonaRunificador extends ConstantePersona
{
    /**
     * CONSTANTES DE RUNIFICADOR
     */
    const MENSAJE_ERROR_COMUNICACION_RUNIFICADOR = 'Error de comunicación con Registro Civil (runificador)';
    const MENSAJE_ERROR_RUNIFICADOR_RUN_NO_VALIDO = 'El RUN ingresado no es valido';
    const MENSAJE_ERROR_RUNIFICADOR_FALLECIDO = 'El ciudadano se encuentra fallecido';

    /**
     * DATA REGITRO CIVIL
     */
    const MENSAJE_DATOS_DE_CONTACTO_2_ERROR_INESPERADO = 'Error inesperado';
    const MENSAJE_DATOS_DE_CONTACTO_3_ERROR_INESPERADO_RUNIFICADOR = 'Error inesperado en el runificador';
    const MENSAJE_DATOS_DE_CONTACTO_5_CIUDADANO_NO_OK_REGITRO_CIVIL = 'Ciudadano no esta en registro civil';
    const MENSAJE_DATOS_DE_CONTACTO_6_ERROR_REGISTRO_CIVIL = 'Error de comunicación con Registro Civil (runificador)';



    public static $RESPUESTA_RUNIFICADOR = [
        Constante::DATOS_DE_CONTACTO_2_ERROR_INESPERADO => self::MENSAJE_DATOS_DE_CONTACTO_2_ERROR_INESPERADO,
        Constante::DATOS_DE_CONTACTO_3_ERROR_INESPERADO_RUNIFICADOR => self::MENSAJE_DATOS_DE_CONTACTO_3_ERROR_INESPERADO_RUNIFICADOR,
        Constante::DATOS_DE_CONTACTO_5_CIUDADANO_NO_OK_REGITRO_CIVIL => self::MENSAJE_DATOS_DE_CONTACTO_5_CIUDADANO_NO_OK_REGITRO_CIVIL,
        Constante::DATOS_DE_CONTACTO_6_ERROR_REGISTRO_CIVIL => self::MENSAJE_DATOS_DE_CONTACTO_6_ERROR_REGISTRO_CIVIL
    ];

}
