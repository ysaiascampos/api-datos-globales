<?php


namespace App\Http\ResponseData;



class PersonaAsignacionCrearData extends DataResponse
{
    private $persona;
    public function __construct( $personaAsignacion)
    {
        $this->persona = $personaAsignacion;
        $this->data['persona-asignacion'] = self::mapPersonaAsignacion();
    }

    private function mapPersonaAsignacion()
    {
        return [
            "per-id" => $this->persona->per_id,
            "per-run" => $this->persona->per_run,
            "per-dv" => $this->persona->per_dv,
            "per-nombres" => $this->persona->per_nombres,
            "per-apellidopaterno" => $this->persona->per_apellidopaterno,
            "per-apellidomaterno" => $this->persona->per_apellidomaterno,
            "per-fechanacimiento" => $this->persona->per_fechanacimiento,
            "per-edad" => $this->persona->per_edad,
            "per-sexo" => $this->persona->per_sexo,
            "per-nacionalidad" => $this->persona->per_nacionalidad,
            "per-direccion" => $this->persona->per_direccion,
            "per-email" => $this->persona->per_email,
            "per-telefono" => $this->persona->per_telefono,
            "per-celular" => $this->persona->per_celular,
            "per-fechadefuncion" => $this->persona->per_fechadefuncion,
            "per-fechafuente" => $this->persona->per_fechafuente,
            "per-fechaactualizacion" => $this->persona->per_fechaactualizacion,
            "per-caracterizacion" => $this->persona->per_caracterizacion,
            "per-fechacalculo" => $this->persona->per_fechacalculo,
            "per-vigente" => $this->persona->per_vigente,
            "per-fechaexpiracion" => $this->persona->per_fechaexpiracion,
            "per-idsolicitud" => $this->persona->per_idsolicitud,
            "per-calculoactivo" => $this->persona->per_calculoactivo,
            "per-fuente" => $this->persona->per_fuente,
            "no-registrado-srcei" => $this->persona->no_registrado_srcei,
            "per-situacionvisa" => $this->persona->per_situacionvisa,
            "per-identidadgenero" => $this->persona->per_identidadgenero,
            "per-md5" => $this->persona->per_md5,
            "per-nroidentificadornac" => $this->persona->per_nroidentificadornac,
            "per-estadocivil" => $this->persona->per_estadocivil,
            "per-fcorrec-man" => $this->persona->per_fcorrec_man,
            "per-desc-correc" => $this->persona->per_desc_correc,
            "per-fecha-consulta" => $this->persona->per_fecha_consulta,
            "ingreso-manual" => $this->persona->ingreso_manual,
            "per-run-padre" => $this->persona->per_run_padre,
            "per-run-madre" => $this->persona->per_run_madre,
            "per-fecha-actualizacion-padres" => $this->persona->per_fecha_actualizacion_padres
        ];
    }
}
