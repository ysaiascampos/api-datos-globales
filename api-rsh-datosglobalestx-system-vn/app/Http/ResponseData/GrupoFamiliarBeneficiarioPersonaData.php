<?php


namespace App\Http\ResponseData;



class GrupoFamiliarBeneficiarioPersonaData extends DataResponse
{
    private $grupoFamiliar;
    private $personaTokenMail;
    public function __construct( $grupoFamiliar)
    {
        $this->grupoFamiliar = $grupoFamiliar;
        $this->data['grupo-familiar-beneficiario-persona'] = self::mapGrupoFamiliarBeneficiarioPersona();
    }

    private function mapGrupoFamiliarBeneficiarioPersona()
    {
        return [
//            "grupfami-id" => $this->grupoFamiliar->grupfami_id,
//            "grupfami-folio" => $this->grupoFamiliar->grupfami_folio,
            "grupfami-nuevofolio" => $this->grupoFamiliar->grupfami_nuevofolio,
//            "grupfami-comunaine" => $this->grupoFamiliar->grupfami_comunaine,
//            "grupfami-localidad" => $this->grupoFamiliar->grupfami_localidad,
//            "grupfami-unidadvecinal" => $this->grupoFamiliar->grupfami_unidadvecinal,
//            "grupfami-codigoah" => $this->grupoFamiliar->grupfami_codigoah,
//            "grupfami-c-zona" => $this->grupoFamiliar->grupfami_c_zona,
//            "grupfami-c-calle" => $this->grupoFamiliar->grupfami_c_calle,
//            "grupfami-n-calle" => $this->grupoFamiliar->grupfami_n_calle,
//            "grupfami-sitio" => $this->grupoFamiliar->grupfami_sitio,
//            "grupfami-block" => $this->grupoFamiliar->grupfami_block,
//            "grupfami-dpto" => $this->grupoFamiliar->grupfami_dpto,
//            "grupfami-casa" => $this->grupoFamiliar->grupfami_casa,
//            "grupfami-referencia" => $this->grupoFamiliar->grupfami_referencia,
//            "grupfami-numclienteluz" => $this->grupoFamiliar->grupfami_numclienteluz,
//            "grupfami-numclienteagua" => $this->grupoFamiliar->grupfami_numclienteagua,
//            "grupfami-tipovivienda" => $this->grupoFamiliar->grupfami_tipovivienda,
//            "grupfami-situacionsitio" => $this->grupoFamiliar->grupfami_situacionsitio,
//            "grupfami-situacionvivienda" => $this->grupoFamiliar->grupfami_situacionvivienda,
//            "grupfami-fuenteagua" => $this->grupoFamiliar->grupfami_fuenteagua,
//            "grupfami-distribucionagua" => $this->grupoFamiliar->grupfami_distribucionagua,
//            "grupfami-sistemabano" => $this->grupoFamiliar->grupfami_sistemabano,
//            "grupfami-dormitoriosocupados" => $this->grupoFamiliar->grupfami_dormitoriosocupados,
//            "grupfami-principalocupante" => $this->grupoFamiliar->grupfami_principalocupante,
//            "grupfami-vigente" => $this->grupoFamiliar->grupfami_vigente,
//            "grupfami-fechaexpiracion" => $this->grupoFamiliar->grupfami_fechaexpiracion,
//            "grupfami-idsolicitud" => $this->grupoFamiliar->grupfami_idsolicitud,
//            "grupfami-calculoactivo" => $this->grupoFamiliar->grupfami_calculoactivo,
//            "grupfami-fuente" => $this->grupoFamiliar->grupfami_fuente,
//            "grupfami-estado" => $this->grupoFamiliar->grupfami_estado,
//            "grupfami-materialmuro" => $this->grupoFamiliar->grupfami_materialmuro,
//            "grupfami-materialmuroestado" => $this->grupoFamiliar->grupfami_materialmuroestado,
//            "grupfami-materialpiso" => $this->grupoFamiliar->grupfami_materialpiso,
//            "grupfami-materialpisoestado" => $this->grupoFamiliar->grupfami_materialpisoestado,
//            "grupfami-materialtecho" => $this->grupoFamiliar->grupfami_materialtecho,
//            "grupfami-materialtechoestado" => $this->grupoFamiliar->grupfami_materialtechoestado,
//            "grupfami-fecha-encuesta" => $this->grupoFamiliar->grupfami_fecha_encuesta,
//            "grupfami-fecha-modificacion" => $this->grupoFamiliar->grupfami_fecha_modificacion,
//            "grupfami-run-encuestador" => $this->grupoFamiliar->grupfami_run_encuestador,
//            "grupfami-rut-validacion" => $this->grupoFamiliar->grupfami_rut_validacion,
//            "grupfami-fecha-validacion" => $this->grupoFamiliar->grupfami_fecha_validacion,
//            "grupfami-rut-validacion-dir" => $this->grupoFamiliar->grupfami_rut_validacion_dir,
//            "grupfami-fecha-validacion-dir" => $this->grupoFamiliar->grupfami_fecha_validacion_dir,
//            "grupfami-fecha-estado" => $this->grupoFamiliar->grupfami_fecha_estado,
//            "grupfami-motivo-cambio-estado" => $this->grupoFamiliar->grupfami_motivo_cambio_estado,
//            "grupfami-rut-cambio-estado" => $this->grupoFamiliar->grupfami_rut_cambio_estado,
//            "grupfami-rut-validacion-act" => $this->grupoFamiliar->grupfami_rut_validacion_act,
//            "grupfami-fecha-validacion-act" => $this->grupoFamiliar->grupfami_fecha_validacion_act,
//            "grupfami-c-calle2" => $this->grupoFamiliar->grupfami_c_calle2,
//            "id-supervision" => $this->grupoFamiliar->id_supervision,
//            "grupfami-numdomicilio" => $this->grupoFamiliar->grupfami_numdomicilio,
//            "grupfami-institucionalizado" => $this->grupoFamiliar->grupfami_institucionalizado,
//            "grupfami-fcorrec-man" => $this->grupoFamiliar->grupfami_fcorrec_man,
//            "grupfami-desc-correc" => $this->grupoFamiliar->grupfami_desc_correc,
//            "manzent" => $this->grupoFamiliar->manzent,
//            "sist-referencia-coord-x-y" => $this->grupoFamiliar->sist_referencia_coord_x_y,
//            "sist-origen-x-y" => $this->grupoFamiliar->sist_origen_x_y,
//            "coordenada-x" => $this->grupoFamiliar->coordenada_x,
//            "coordenada-y" => $this->grupoFamiliar->coordenada_y,
//            "direc-sin-separar-geo" => $this->grupoFamiliar->direc_sin_separar_geo,
//            "direc-calle-geo" => $this->grupoFamiliar->direc_calle_geo,
//            "direc-numero-geo" => $this->grupoFamiliar->direc_numero_geo,
//            "uv-geo" => $this->grupoFamiliar->uv_geo,
//            "fecha-geo" => $this->grupoFamiliar->fecha_geo,
//            "localidad-geo" => $this->grupoFamiliar->localidad_geo,
//            "grupfami-rut-validacion-ciud" => $this->grupoFamiliar->grupfami_rut_validacion_ciud,
//            "grupfami-fecha-validacion-ciud" => $this->grupoFamiliar->grupfami_fecha_validacion_ciud,
//            "uv-old" => $this->grupoFamiliar->uv_old,
//            "nominado-supervision" => $this->grupoFamiliar->nominado_supervision,
//            "grupfami-fecha-suspension" => $this->grupoFamiliar->grupfami_fecha_suspension,
//            "grupfami-codigo-suspension" => $this->grupoFamiliar->grupfami_codigo_suspension,
//            "localidad-ine" => $this->grupoFamiliar->localidad_ine,
//            "entidades-ine" => $this->grupoFamiliar->entidades_ine,
//            "cod-direccion-gis" => $this->grupoFamiliar->cod_direccion_gis,
//            "grupfami-idcompluz" => $this->grupoFamiliar->grupfami_idcompluz,
//            "grupfami-idcompagua" => $this->grupoFamiliar->grupfami_idcompagua,
//            "grupfami-coord-dir-logitud" => $this->grupoFamiliar->grupfami_coord_dir_logitud,
//            "grupfami-coord-dir-latitud" => $this->grupoFamiliar->grupfami_coord_dir_latitud,
//
//
//            "bnfgrupfami-id" => $this->grupoFamiliar->bnfgrupfami_id,
//            "bnfgrupfami-persona" => $this->grupoFamiliar->bnfgrupfami_persona,
//            "bnfgrupfami-grupofamiliar" => $this->grupoFamiliar->bnfgrupfami_grupofamiliar,
//            "bnfgrupfami-parentesco" => $this->grupoFamiliar->bnfgrupfami_parentesco,
//            "bnfgrupfami-fecha" => $this->grupoFamiliar->bnfgrupfami_fecha,
//            "bnfgrupfami-vigente" => $this->grupoFamiliar->bnfgrupfami_vigente,
//            "bnfgrupfami-fechaexpiracion" => $this->grupoFamiliar->bnfgrupfami_fechaexpiracion,
//            "bnfgrupfami-idsolicitud" => $this->grupoFamiliar->bnfgrupfami_idsolicitud,
//            "bnfgrupfami-calculoactivo" => $this->grupoFamiliar->bnfgrupfami_calculoactivo,
//            "bnfgrupfami-fuente" => $this->grupoFamiliar->bnfgrupfami_fuente,
//            "bnfgrupfami-informante" => $this->grupoFamiliar->bnfgrupfami_informante,
//            "bnfgrupfami-fecha-ingreso" => $this->grupoFamiliar->bnfgrupfami_fecha_ingreso,
//            "bnfgrupfami-fecha-modificacion" => $this->grupoFamiliar->bnfgrupfami_fecha_modificacion,
//            "bnfgrupfami-institucionalizado" => $this->grupoFamiliar->bnfgrupfami_institucionalizado,
//            "bnfgrupfami-fcorrec-man" => $this->grupoFamiliar->bnfgrupfami_fcorrec_man,
//            "bnfgrupfami-desc-correc" => $this->grupoFamiliar->bnfgrupfami_desc_correc,
//            "bnfgrupfami-migrante" => $this->grupoFamiliar->bnfgrupfami_migrante,
        ];
    }
}
