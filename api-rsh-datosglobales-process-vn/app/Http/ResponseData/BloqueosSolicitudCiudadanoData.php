<?php


namespace App\Http\ResponseData;



use App\Classes\PendientesCiudadanoClass;

class BloqueosSolicitudCiudadanoData extends DataResponse
{
    private $puedeRealizarSolicitud;
    private $tramitesPendientes;
    private $bloqueosTramites;
    public function __construct( $pendientesCiudadanoData)
    {
        $this->puedeRealizarSolicitud = $pendientesCiudadanoData["puede-realizar-solicitud"];
        $this->tramitesPendientes = $pendientesCiudadanoData["tramites-pendientes"];
        $this->bloqueosTramites = $pendientesCiudadanoData["bloqueos-tramites"];

        $this->data['bloqueos-solicitud-ciudadono'] = self::mapPendientesCiudadano();
    }

    private function mapPendientesCiudadano()
    {
        return [
            'puede-realizar-solicitud' => $this->puedeRealizarSolicitud,
            'tramites-pendientes' => [
                "ciudadano" => $this->tramitesPendientes['ciudadano'],
                "municipal" => $this->tramitesPendientes['municipal']
            ],
            'bloqueos-tramites' => $this->bloqueosTramites
        ];
    }
}
