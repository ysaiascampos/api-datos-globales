<?php


namespace App\Http\ResponseData;



use App\Classes\PendientesCiudadanoClass;

class PendientesCiudadanoData extends DataResponse
{
    private $poseeRSH;
    private $situacionBasica;
    private $tramitesPendientes;
    private $alertas;
    private $datosContacto;
    private $datosTramo;
    private $datosComuna;
    private $datosValidaSolicitudTercero;
    public function __construct( $pendientesCiudadanoData)
    {
        $this->poseeRSH = $pendientesCiudadanoData["seccion-posee-rsh"];
        $this->situacionBasica = $pendientesCiudadanoData["seccion-situacion-basica"];
        $this->tramitesPendientes = $pendientesCiudadanoData["seccion-tramites-pendientes"];
        $this->alertas = $pendientesCiudadanoData["seccion-alertas"];
        $this->datosContacto = $pendientesCiudadanoData["seccion-datos-de-contacto"];
        $this->datosTramo = $pendientesCiudadanoData["seccion-datos-tramo"]['tramo'];
        $this->datosComuna = $pendientesCiudadanoData["seccion-datos-comuna"]['comuna-grupo-familiar-export'];
        $this->datosValidaSolicitudTercero = $pendientesCiudadanoData["seccion-datos-solicitudes-validacion-tercero"];

        $this->data['pendientes-ciudadano'] = self::mapPendientesCiudadano();
    }

    private function mapPendientesCiudadano()
    {
        $poseeRSHData = new PoseeRSHData($this->poseeRSH);
        $situacionBasicaData = new SituacionBasicaData($this->situacionBasica);
        $tramitesPendientesData = new TramitesPendientesData($this->tramitesPendientes);
        $alertasData = new AlertasData($this->alertas);
        $datosContactoData = new DatosContactoData($this->datosContacto);
        $datosTramoData = new TramoData($this->datosTramo);
        $datosComunaData = new ComunaGrupoFamiliarData($this->datosComuna);
        $datosValidaSolicitudTerceroData = new ValidaSolicitudTerceroData($this->datosValidaSolicitudTercero);
        return [
            'posee-rsh' => $poseeRSHData->data,
            'situacion-basica' => $situacionBasicaData->data,
            'tramites-pendientes' => $tramitesPendientesData->data["tramites-pendientes"],
            'alertas' => $alertasData->data['alertas'],
            'datos-de-contacto' => $datosContactoData->data,
            'tramo' => $datosTramoData->data["tramo"],
            'comuna' => $datosComunaData->data["comuna"],
            'solicitudes-validacion-tercero' => $datosValidaSolicitudTerceroData->data['solicitudes-validacion-tercero']
        ];
    }
}
