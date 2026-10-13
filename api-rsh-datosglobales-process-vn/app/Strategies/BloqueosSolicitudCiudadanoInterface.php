<?php


namespace App\Strategies;


interface BloqueosSolicitudCiudadanoInterface
{

    public function getPuedeRealizarSolicitud();
    public function getTramitesPendientes();
    public function getBloqueosTramites();
}