<?php

namespace App\Constantes;

/**
 * Class Constante
 * EJ: Constante::STATUS_200_SUCCESS_OK;
 *
 * Para cambiar nombre se debe presionar SHIFT+F6 sobre la constante
 *    así el cambio se refleja en todas los archivos donde se llama
 */
abstract class ConstanteUsuario extends ConstanteEnv
{
    /**
     * Tabla AB_ESTADO_USUARIO
     */
    const USUARIO_1_VIGENTE = 1;
    const USUARIO_0_NO_VIGENTE = 0;

    

}
