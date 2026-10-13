<?php


namespace App\Http\ResponseData;



class TramoData extends DataResponse
{
    private $grupoFamiliarTramo;
    public function __construct( $respuestaTramo)
    {
        $this->grupoFamiliarTramo = $respuestaTramo;
        $this->data["tramo"] = self::mapTramo();
    }

    private function mapTramo()
    {
        return [
            'numero' => $this->grupoFamiliarTramo["numero"],
            'nombre' => $this->grupoFamiliarTramo["nombre"],
            'mensaje' => $this->grupoFamiliarTramo["mensaje"],
        ];
    }
}
