<?php

namespace App\Constantes;

/**
 * Class Constante
 * EJ: Constante::STATUS_200_SUCCESS_OK;
 *
 * Para cambiar nombre se debe presionar SHIFT+F6 sobre la constante
 *    así el cambio se refleja en todas los archivos donde se llama
 */
abstract class ConstanteError extends ConstanteEstado
{
    /**
     * MENSAJES DE ERROR
     */
    const MENSAJE_ERROR_CONSUMO_SERVICIO_REST = 'Error al consumir servicio rest. Verifique que la información proporcionada al servicio este vigente en la plataforma.';
    const MENSAJE_ERROR_NO_GRUPO_FAMILIAR_VIGENTE = 'El ciudadano no posee registro vigente de su grupo familiar en la plataforma'; // DatosBeneficiarios.php
    const MENSAJE_ERROR_NO_GRUPO_FAMILIAR_VIGENTE2 = 'El ciudadano no posee un grupo familiar activo en la plataforma'; // DatosDomicilioVivienda.php
    const MENSAJE_ERROR_NO_DATO_CONTACTO = 'El ciudadano no posee registros en el Registro Civil'; // 'El ciudadano no posee registros en la plataforma';
    const MENSAJE_ERROR_FALLA_REGISTRO_CIVIL = 'No es posible consultar la información para el ciudadano, debido a un error desde el SRCeI. Contactar con la Mesa de Ayuda.'; // 'Error en registro civil';
    const MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA = 'La solicitud no se encuentra vigente o no esta disponible'; // DatosDeSolicitud.php
    const MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA2 = 'Solicitud no encontrada'; // DatosTipoDeSolicitud.php
    const MENSAJE_ERROR_YA_EXISTE_SOLICITUD_TIPO_1 = 'El ciudadano ya tiene una solicitud de cambio de domicilio y vivienda en trámite';
    const MENSAJE_ERROR_YA_EXISTE_SOLICITUD_MISMO_TIPO = 'Ya existe una solicitud en trámite.';
    const MENSAJE_ERROR_SOLICITUD_SIN_VISTA = 'No existe vista para este tipo de solicitud';
    const MENSAJE_RUN_NO_CORRESPONDE_AL_DE_SOLICITUD = 'RUN no corresponde al de la solicitud';
    const MENSAJE_ERROR_MSG_NO_DATOS_SOLICITUD = 'No se encuentran datos asociados a esta solicitud';
    const MENSAJE_ERROR_MSG_NO_DATOS_GRUPO_FAMILIAR = 'No se encuentran datos asociados a este grupo familiar';
    const MENSAJE_ERROR_MSG_NO_DATOS_CONTACTO = 'El ciudadano no registra informacion en la plataforma.';
    const MENSAJE_ERROR_FALLA_DATOS_CONTACTO = 'Error al guardar la información de contacto';
    const MENSAJE_FOLIO_SIN_ORDEN_TRABAJO = 'Error no existe Orden de Trabajo para este Folio';
    const MENSAJE_ERROR_GUARDADO_DATOS_SOLICITUD = 'Error no se logro guardar parte de la solicitud (%parte%)';
    const MENSAJE_ERROR_MENOR_GRUPO_FAMILIAR_VIGENTE = 'Si se realiza esta solicitud quedarán menores sin RSH';


    /**
     * MENSAJES GENERALES
     */
    const MENSAJE_SOLICITUD_SUCCESS = "La solicitud fue editada";
    const MENSAJE_SOLICITUD_CREATED = "La solicitud fue creada";
    const MENSAJE_DOCUMENTO_AUTOFIRMADO = 'No se requiere adjuntar este documento puesto que el acceso con clave única equivale a firma electrónica';

    /**
     * Mensaje de uso de datos en cartolas
     * --------------------------------------------------------------------------
     * Mensaje que indica la fecha de los datos utilizados para calcular el tramo
     */
    const FechaDatosCartola = 'Esta cartola ha sido construida con información actualizada y aprobada por el municipio hasta el día 21 de octubre de 2016 a las 12:30 horas. Los cambios de información aprobados con posterioridad a esa fecha se verán reflejados en tu cartola el primer día hábil de diciembre.';

    /**
     * Antiguos
     */
    const mensajeAlertaCiudadano = 'Su grupo familiar se encuentra en estado En Revisión. Debe actualizar la información de su registro.';
    const mesajeCiudadanoIngresaClaveRun = 'Recuerde que al acceder con Clave RUN; debe acreditar su identidad en la municipalidad correspondiente a su domicilio para continuar con la gestión de su solicitud. Solo el ingreso con ClaveÚnica del Registro Civil; permite finalizar su trámite en línea.';

    /**
     * CONSTANTES DE ERROR DE SOAP
     */
    const MENSAJE_ERROR_SOAP_COMUNICACION_RUNIFICADOR = 'Error de comunicación con Registro Civil (runificador)';
    const MENSAJE_SOAP_RUNIFICADOR_RUN_NO_VALIDO = 'El RUN ingresado no es valido';

    /**
     * Codigos COMUNICACION con SERVICIOS o URLs
     */
    const COMUNICACION_OK = 1;     //  NO ERROR
    const COMUNICACION_ERROR = -1;
    const COMUNICACION_OK_TEXTO = 'OK';
    const COMUNICACION_ERROR_TEXTO = 'NO OK';


    /**
     * Mensajes Generales sobre solicitudes-tramitables
     */
    const MENSAJE_NO_CUMPLE_REGLA_MUNICIPAL = 'El ciudadano no cuenta con registros administrativos que permitan realizar esta solicitud';
    const MENSAJE_NO_CUMPLE_REGLA_CIUDADANO = 'No cuenta con datos complementarios que influyan en la Calificación Socioecónomica y que permitan realizar este tramite';

    const MENSAJE_SOLICITUD_JURIDICA_PROPIA_EN_TRAMITE_ACTUALIZACION = 'Se mantiene en trámite una solicitud de actualización de participación en esta sociedad o empresa.';
    const MENSAJE_SOLICITUD_JURIDICA_PROPIEDAD_PROPIA_EN_TRAMITE_ACTUALIZACION = 'Se mantiene en trámite una solicitud de actualización de bien raíz que es patrimonio de esta empresa o sociedad.';
    const MENSAJE_SOLICITUD_JURIDICA_TERRESTRE_PROPIA_EN_TRAMITE_ACTUALIZACION = 'Se mantiene en trámite una solicitud de actualización de vehículo terrestre que es patrimonio de esta empresa o sociedad';
    const MENSAJE_SOLICITUD_JURIDICA_MARINA_PROPIA_EN_TRAMITE_ACTUALIZACION = 'Se mantiene en trámite una solicitud de actualización de vehículo marítimo que es patrimonio de esta empresa o sociedad';

    const MENSAJE_SOLICITUD_JURIDICA_SOCIEDAD_EN_TRAMITE_ACTUALIZACION = 'Se mantiene en trámite una solicitud de actualización de participación en esta sociedad o empresa, generada por un integrante de la sociedad.';
    const MENSAJE_SOLICITUD_JURIDICA_PROPIEDAD_SOCIEDAD_EN_TRAMITE_ACTUALIZACION = 'Se mantiene en trámite una solicitud de actualización de bien raíz que es patrimonio de esta empresa o sociedad, generada por un integrante de la sociedad.';
    const MENSAJE_SOLICITUD_JURIDICA_TERRESTRE_SOCIEDAD_EN_TRAMITE_ACTUALIZACION = 'Se mantiene en trámite una solicitud de actualización de vehículo terrestre que es patrimonio de esta empresa o sociedad, generada por un integrante de la sociedad.';
    const MENSAJE_SOLICITUD_JURIDICA_MARINA_SOCIEDAD_EN_TRAMITE_ACTUALIZACION = 'Se mantiene en trámite una solicitud de actualización de vehículo marítimo que es patrimonio de esta empresa o sociedad, generada por un integrante de la sociedad.';

    const MENSAJE_SOLICITUD_JURIDICA_PROPIA_EN_TRAMITE_RECTIFICACION = 'Se mantiene en trámite una solicitud de rectificación de participación en esta sociedad o empresa.';
    const MENSAJE_SOLICITUD_JURIDICA_PROPIEDAD_PROPIA_EN_TRAMITE_RECTIFICACION = 'Se mantiene en trámite una solicitud de rectificación de bien raíz que es patrimonio de esta empresa o sociedad.';
    const MENSAJE_SOLICITUD_JURIDICA_TERRESTRE_PROPIA_EN_TRAMITE_RECTIFICACION = 'Se mantiene en trámite una solicitud de rectificación de vehículo terrestre que es patrimonio de esta empresa o sociedad';
    const MENSAJE_SOLICITUD_JURIDICA_MARINA_PROPIA_EN_TRAMITE_RECTIFICACION = 'Se mantiene en trámite una solicitud de rectificación de vehículo marítimo que es patrimonio de esta empresa o sociedad';

    const MENSAJE_SOLICITUD_JURIDICA_SOCIEDAD_EN_TRAMITE_RECTIFICACION = 'Se mantiene en trámite una solicitud de rectificación de participación en esta sociedad o empresa, generada por un integrante de la sociedad.';
    const MENSAJE_SOLICITUD_JURIDICA_PROPIEDAD_SOCIEDAD_EN_TRAMITE_RECTIFICACION = 'Se mantiene en trámite una solicitud de rectificación de bien raíz que es patrimonio de esta empresa o sociedad, generada por un integrante de la sociedad.';
    const MENSAJE_SOLICITUD_JURIDICA_TERRESTRE_SOCIEDAD_EN_TRAMITE_RECTIFICACION = 'Se mantiene en trámite una solicitud de rectificación de vehículo terrestre que es patrimonio de esta empresa o sociedad, generada por un integrante de la sociedad.';
    const MENSAJE_SOLICITUD_JURIDICA_MARINA_SOCIEDAD_EN_TRAMITE_RECTIFICACION= 'Se mantiene en trámite una solicitud de rectificación de vehículo marítimo que es patrimonio de esta empresa o sociedad, generada por un integrante de la sociedad.';

    const MENSAJE_ERROR_FORMULARIO_SOLICITUD = 'Ha ocurrido un problema al obtener formulario de la solicitud.';

    /*
     *
        Rango Tipo
        1000 - 1099 La API procesó la petición sin errores y con una respuesta con datos de negocio
        1100 - 1199 Error en la validación de uno(s) parámetros(s)
        1200 - 1299 Problemas al buscar datos en base de datos
        1300 - 1399 Problemas al usar otras fuentes de datos (por ejemplo, web services)
        1400 - 1499 Error capturado sin categoría (por ejemplo, catch genérico)
     */
    /**
     * MENSAJES DE ERROR SERVICIO
     */
    const MENSAJE_ERROR_10_CONSUMO_SERVICIO_REST = 'Actualmente tenemos problemas técnicos. Por favor, inténtalo más tarde.';
    const MENSAJE_ERROR_11_NO_POSEE_RSH = 'No tienes Registro Social de Hogares. Realiza una solicitud de ingreso para acceder al trámite.';
    const MENSAJE_ERROR_12_NO_POSEE_UN_CORREO_VALIDADO = 'Tu correo electrónico no está validado. Ingresa a tu información personal para confirmar tu correo.';
    const MENSAJE_ERROR_13_POSEE_SOLICITUD_EN_BORRADOR = 'Tú u otras personas de tu hogar tienen solicitudes incompletas. Debes completarlas o eliminarlas antes de crear una nueva solicitud.';
    const MENSAJE_ERROR_14_POSEE_SOLICITUD_EN_TRAMITE = 'Tú u otras personas de tu hogar tienen solicitudes en proceso. Debes completarlas o eliminarlas antes de crear una nueva solicitud.';
    const MENSAJE_ERROR_15_POSEE_MENSAJE_BLOQUEANTE = 'Tú u otras personas de tu hogar tienen solicitudes pendientes. Revisa tus notificaciones y realiza el trámite antes de crear una nueva solicitud.';
    const MENSAJE_ERROR_16_ESPERA_TRAMO = 'Tu solicitud de ingreso al Registro Social de Hogares (RSH) fue aprobada. Conoce tu calificación en la próxima actualización del RSH.';
    const MENSAJE_ERROR_17_ESPERA_TRAMO_1_INGRESO_REGISTRO = 'Tu solicitud de ingreso al Registro Social de Hogares (RSH) fue aprobada. Conoce tu calificación en la próxima actualización del RSH.';
    const MENSAJE_ERROR_18_ESPERA_TRAMO_3_INCORPORACION = 'Tu incorporación al Registro Social de Hogares (RSH) fue aprobada. Conoce tu calificación en la próxima actualización del RSH.';
    const MENSAJE_ERROR_19_ESPERA_TRAMO_30_INGRESO_INSTITUCIONALIZADOS = 'Tu solicitud de ingreso al Registro Social de Hogares (RSH) fue aprobada. Conoce tu calificación en la próxima actualización del RSH.';
    const MENSAJE_ERROR_20_ESPERA_TRAMO_202_DOMICILIO_VIVIENDA = 'Tu solicitud de Cambio de domicilio aprobada. Conoce tu calificación en la próxima actualización del RSH.';
    const MENSAJE_ERROR_21_ESPERA_TRAMO_206_INGRESO_NNA = 'Tu solicitud de Ingreso NNA Especial aprobada. Conoce tu calificación en la próxima actualización del RSH.';
    const MENSAJE_ERROR_22_MENOR_DE_18_EDAD = 'El ciudadano no puede ser menor a 18 años de edad, para realizar trámites en la plataforma.';
    const MENSAJE_ERROR_23_SOLICITANTE_MENOR_DE_18_EDAD = 'El solicitante no puede ser menor a 18 años de edad para realizar trámites en la plataforma.';
    const MENSAJE_ERROR_24_USUARIO_INSTITUCIONALIZADO = 'Usuario cuenta con registro como persona institucionalizada, debe realizar un nuevo ingreso al registro';

    const NUMERO_ERROR_10_CONSUMO_SERVICIO_REST = 10;
    const NUMERO_ERROR_11_NO_POSEE_RSH = 11;
    const NUMERO_ERROR_12_NO_POSEE_UN_CORREO_VALIDADO = 12;
    const NUMERO_ERROR_13_POSEE_SOLICITUD_EN_BORRADOR = 13;
    const NUMERO_ERROR_14_POSEE_SOLICITUD_EN_TRAMITE = 14;
    const NUMERO_ERROR_15_POSEE_MENSAJE_BLOQUEANTE = 15;
    const NUMERO_ERROR_16_ESPERA_TRAMO = 16;
    const NUMERO_ERROR_17_ESPERA_TRAMO_1_INGRESO_REGISTRO = 17;
    const NUMERO_ERROR_18_ESPERA_TRAMO_3_INCORPORACION = 18;
    const NUMERO_ERROR_19_ESPERA_TRAMO_30_INGRESO_INSTITUCIONALIZADOS = 19;
    const NUMERO_ERROR_20_ESPERA_TRAMO_202_DOMICILIO_VIVIENDA = 20;
    const NUMERO_ERROR_21_ESPERA_TRAMO_206_INGRESO_NNA = 21;
    const NUMERO_ERROR_22_MENOR_DE_18_EDAD = 22;
    const NUMERO_ERROR_23_SOLICITANTE_MENOR_DE_18_EDAD = 23;
    const NUMERO_ERROR_24_USUARIO_INSTITUCIONALIZADO = 24;
    const NUMERO_ERROR_25_SOAP_COMUNICACION_RUNIFICADOR = 25;
    const NUMERO_ERROR_26_NO_DATO_CONTACTO = 26;

    const NUMERO_ERROR_120_VARIOS = 120;

    public static $MENSAJE_DE_ERROR_SERVICIO = [
        self::NUMERO_ERROR_10_CONSUMO_SERVICIO_REST => self::MENSAJE_ERROR_10_CONSUMO_SERVICIO_REST,
        self::NUMERO_ERROR_11_NO_POSEE_RSH => self::MENSAJE_ERROR_11_NO_POSEE_RSH,
        self::NUMERO_ERROR_12_NO_POSEE_UN_CORREO_VALIDADO => self::MENSAJE_ERROR_12_NO_POSEE_UN_CORREO_VALIDADO,
        self::NUMERO_ERROR_13_POSEE_SOLICITUD_EN_BORRADOR => self::MENSAJE_ERROR_13_POSEE_SOLICITUD_EN_BORRADOR,
        self::NUMERO_ERROR_14_POSEE_SOLICITUD_EN_TRAMITE => self::MENSAJE_ERROR_14_POSEE_SOLICITUD_EN_TRAMITE,
        self::NUMERO_ERROR_15_POSEE_MENSAJE_BLOQUEANTE => self::MENSAJE_ERROR_15_POSEE_MENSAJE_BLOQUEANTE,
        self::NUMERO_ERROR_16_ESPERA_TRAMO => self::MENSAJE_ERROR_16_ESPERA_TRAMO,
        self::NUMERO_ERROR_17_ESPERA_TRAMO_1_INGRESO_REGISTRO => self::MENSAJE_ERROR_17_ESPERA_TRAMO_1_INGRESO_REGISTRO,
        self::NUMERO_ERROR_18_ESPERA_TRAMO_3_INCORPORACION => self::MENSAJE_ERROR_18_ESPERA_TRAMO_3_INCORPORACION,
        self::NUMERO_ERROR_19_ESPERA_TRAMO_30_INGRESO_INSTITUCIONALIZADOS => self::MENSAJE_ERROR_19_ESPERA_TRAMO_30_INGRESO_INSTITUCIONALIZADOS,
        self::NUMERO_ERROR_20_ESPERA_TRAMO_202_DOMICILIO_VIVIENDA => self::MENSAJE_ERROR_20_ESPERA_TRAMO_202_DOMICILIO_VIVIENDA,
        self::NUMERO_ERROR_21_ESPERA_TRAMO_206_INGRESO_NNA => self::MENSAJE_ERROR_21_ESPERA_TRAMO_206_INGRESO_NNA,
        self::NUMERO_ERROR_22_MENOR_DE_18_EDAD => self::MENSAJE_ERROR_22_MENOR_DE_18_EDAD,
        self::NUMERO_ERROR_23_SOLICITANTE_MENOR_DE_18_EDAD => self::MENSAJE_ERROR_23_SOLICITANTE_MENOR_DE_18_EDAD,
        self::NUMERO_ERROR_24_USUARIO_INSTITUCIONALIZADO => self::MENSAJE_ERROR_24_USUARIO_INSTITUCIONALIZADO
    ];
    const MENSAJE_SERVICIO_CODIGO = 'codigo';
    const MENSAJE_SERVICIO_MENSAJE = 'mensaje';

    public static $MENSAJE_DE_ERROR_SERVICIO_COMPLETO = [
        self::NUMERO_ERROR_10_CONSUMO_SERVICIO_REST => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_10_CONSUMO_SERVICIO_REST,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_10_CONSUMO_SERVICIO_REST,
        ],
        self::NUMERO_ERROR_11_NO_POSEE_RSH => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_11_NO_POSEE_RSH,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_11_NO_POSEE_RSH,
        ],
        self::NUMERO_ERROR_13_POSEE_SOLICITUD_EN_BORRADOR => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_13_POSEE_SOLICITUD_EN_BORRADOR,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_13_POSEE_SOLICITUD_EN_BORRADOR,
        ],
        self::NUMERO_ERROR_14_POSEE_SOLICITUD_EN_TRAMITE => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_14_POSEE_SOLICITUD_EN_TRAMITE,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_14_POSEE_SOLICITUD_EN_TRAMITE,
        ],
        self::NUMERO_ERROR_15_POSEE_MENSAJE_BLOQUEANTE => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_15_POSEE_MENSAJE_BLOQUEANTE,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_15_POSEE_MENSAJE_BLOQUEANTE,
        ],
        self::NUMERO_ERROR_22_MENOR_DE_18_EDAD => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_22_MENOR_DE_18_EDAD,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_22_MENOR_DE_18_EDAD,
        ],
        self::NUMERO_ERROR_23_SOLICITANTE_MENOR_DE_18_EDAD => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_23_SOLICITANTE_MENOR_DE_18_EDAD,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_23_SOLICITANTE_MENOR_DE_18_EDAD,
        ],
        self::NUMERO_ERROR_24_USUARIO_INSTITUCIONALIZADO => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_24_USUARIO_INSTITUCIONALIZADO,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_24_USUARIO_INSTITUCIONALIZADO,
        ],

    ];

    const MENSAJE_SERVICIO_CODIGO_DEFECTO_0 = 0;
    public static $MENSAJE_DE_ERROR_SERVICIO_ESPERA_TRAMO = [
        self::MENSAJE_SERVICIO_CODIGO_DEFECTO_0 => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_16_ESPERA_TRAMO,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_16_ESPERA_TRAMO,
        ],
        self::TIPO_SOLICITUD_1_INGRESO_AL_REGISTRO => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_17_ESPERA_TRAMO_1_INGRESO_REGISTRO,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_17_ESPERA_TRAMO_1_INGRESO_REGISTRO,
        ],
        self::TIPO_SOLICITUD_3_INCORPORACION_DE_NUEVO_INTEGRANTE => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_18_ESPERA_TRAMO_3_INCORPORACION,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_18_ESPERA_TRAMO_3_INCORPORACION,
        ],
        self::TIPO_SOLICITUD_30_SOLICITUD_DE_INGRESO_PARA_INSTITUCIONALIZADOS => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_19_ESPERA_TRAMO_30_INGRESO_INSTITUCIONALIZADOS,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_19_ESPERA_TRAMO_30_INGRESO_INSTITUCIONALIZADOS,
        ],
        self::TIPO_SOLICITUD_202_CAMBIO_DE_DOMICILIO_Y_VIVIENDA => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_20_ESPERA_TRAMO_202_DOMICILIO_VIVIENDA,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_20_ESPERA_TRAMO_202_DOMICILIO_VIVIENDA,
        ],
        self::TIPO_SOLICITUD_206_INGRESO_AL_REGISTRO_NNA => [
            self::MENSAJE_SERVICIO_CODIGO => self::NUMERO_ERROR_21_ESPERA_TRAMO_206_INGRESO_NNA,
            self::MENSAJE_SERVICIO_MENSAJE => self::MENSAJE_ERROR_21_ESPERA_TRAMO_206_INGRESO_NNA,
        ],
    ];


    //errores de modelo Usuario - Remember Token 1501-1599
    const MENSAJE_ERR_NEGOCIO_NO_SE_PUDO_GENERAR_REMEMBER_TOKEN = "Problemas al generar Remember Token";
    const MENSAJE_ERR_NEGOCIO_NO_SE_PUDO_VALIDAR_REMEMBER_TOKEN = "Problemas al validar Remember Token";
}
