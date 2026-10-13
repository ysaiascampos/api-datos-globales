<?php


namespace App\Classes;


use App\Constantes\Constante;
use App\Values\BloqueosSolicitudCiudadanoValues;
use Illuminate\Support\Facades\Log;

class BloqueosSolicitudCiudadanoClass
{
    private $pendientesCiudadano;
    private $puedeRealizarSolicitud;
    private $tramitesPendientes;
    private $bloqueosTramites;

    /**
     * @param null $requestData
     * @return mixed
     */
    public function __invoke($requestData = null)
    {
        $runCiudadano = $requestData["run-ciudadano"];
        $runUsuario = $requestData["run-usuario"];
        $origenRegistro = $requestData["origen-registro"];
        $tipoSolicitud = $requestData["tipo-solicitud"];
        $subTipoSolicitud = $requestData["sub-tipo-solicitud"];
        $this->pendientesCiudadano = [
            'code' => 1000,
            'message' => '',
            'puede-realizar-solicitud' => true,
            'tramites-pendientes' => ['municipal'=> [],
                'ciudadano'=> []],
            'bloqueos-tramites' => [],
        ];

        if (!array_key_exists($tipoSolicitud, Constante::$clasesTramitePorTipoSolicitud)) {
            $this->pendientesCiudadano = [
                'code' => 1101,
                'message' => 'Tipo de solicitud no permitido'
            ];

            return $this->pendientesCiudadano;
        }
        $strategyClass = BloqueosSolicitudCiudadanoValues::STRATEGY[$tipoSolicitud];

        $strategy = new $strategyClass($requestData,
            new PersonaBasicaClass,
            new PoseeRSHClass,
            new ObtenerSolicitudesClass,
            new DatosContactoClass,
            new AlertasClass,
            new GrupoFamiliarExportClass,
            new SolicitudesTramitablesClass,
            new UltimaFechaCierreGrupoFamiliarClass
        );

        $this->puedeRealizarSolicitud = $strategy->getPuedeRealizarSolicitud();
        $this->tramitesPendientes = $strategy->getTramitesPendientes();
        $this->bloqueosTramites = $strategy->getBloqueosTramites();

        $this->pendientesCiudadano = [
            'code' => 1000,
            'message' => '',
            'puede-realizar-solicitud' => $this->puedeRealizarSolicitud,
            'tramites-pendientes' => $this->tramitesPendientes,
            'bloqueos-tramites' => $this->bloqueosTramites,
        ];

        return $this->pendientesCiudadano;
    }

}
