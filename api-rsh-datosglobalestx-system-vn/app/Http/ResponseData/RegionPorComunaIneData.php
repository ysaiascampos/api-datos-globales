<?php


namespace App\Http\ResponseData;



class RegionPorComunaIneData extends DataResponse
{
    private $region;
    public function __construct($regionData)
    {
        $this->region = $regionData;
        $this->data['region'] = self::mapRegion();
    }

    private function mapRegion()
    {
        return [
            "codigo" => $this->region->reg_id,
            "descripcion" => $this->region->reg_descripcion,
        ];
    }
}
