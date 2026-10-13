<?php

namespace App\Models;

use App\classes\Utiles;
use App\Constantes\Constante;
use App\Exceptions\ExcepcionConsistencia;
use App\Http\ResponseData\ValidaSolicitudTerceroData;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Doctrine\Common\Cache\Psr6\get;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ValidaSolicitudTerceroClass
 *
 * @property int $ID
 * @property int|null $per_run
 * @property int $idsolicitud
 * @property string|null $EMAIL
 * @property int|0 $ENVIO_MAIL_AVISO_SOLICITUD
 * @property int|0 $estado_validacion
 * @property int|null $FECHA_VALIDACION
 * @property int|0 $ENVIO_MAIL_VALIDACION
 *
 * @method static Builder|ValidaSolicitudTercero newModelQuery()
 * @method static Builder|ValidaSolicitudTercero newQuery()
 * @method static Builder|ValidaSolicitudTercero query()
 *
 * @method static Builder|ValidaSolicitudTercero whereID($value)
 * @method static Builder|ValidaSolicitudTercero wherePERRUN($value)
 * @method static Builder|ValidaSolicitudTercero whereidsolicitud($value)
 * @method static Builder|ValidaSolicitudTercero whereEMAIL($value)
 * @method static Builder|ValidaSolicitudTercero whereENVIOMAILAVISOSOLICITUD($value)
 * @method static Builder|ValidaSolicitudTercero whereESTADOVALIDACION($value)
 * @method static Builder|ValidaSolicitudTercero whereFECHAVALIDACION($value)
 * @method static Builder|ValidaSolicitudTercero whereENVIOMAILVALIDACION($value)
 *
 * @mixin \Eloquent
 */
class ValidaSolicitudTercero extends Model
{
    protected $table = 'valida_solicitud_terceros';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $appends = [
        'nombre_asociado_solicitud',
        'run_ciudadano',
        'nombre_titular_solicitud',
        'id_padre_solicitud',
        'id_estado_solicitud',
        'estado_solicitud',
        'id_sub_estado_solicitud',
        'sub_estado_solicitud',
        'id_tipo_solicitud',
        'tipo_solicitud',
        'id_sub_tipo_solicitud',
        'sub_tipo_solicitud',
        'vigente',
        'codigo_comuna',
        'comuna_descripcion',
        'codigo_region',
        'region_descripcion',
        'fecha_creacion',
        'fecha_modificacion',
        'fecha_creacion_ciudadano',
        'comentario'
    ];

    // NOMBRE ASOCIADO SOLICITUD

    public function getnombreAsociadoSolicitudAttribute()
    {
        if (!isset($this->attributes['nombre_asociado_solicitud'])) {
            $this->attributes['nombre_asociado_solicitud'] = $this->nombreAsociadoSolicitud($this->attributes['per_run']);
        }
        return $this->attributes['nombre_asociado_solicitud'];

    }

    public static function nombreAsociadoSolicitud($runCiudadano)
    {
        try {
            $data = Personas::where('per_run', $runCiudadano)
                ->select('per_nombres', 'per_apellidopaterno', 'per_apellidomaterno')
                ->first();
            $nombre = ucwords(strtolower(
                    Utiles::cambiaTildeASuLetra($data['per_nombres']) . ' ' .
                    Utiles::cambiaTildeASuLetra($data['per_apellidopaterno']) . ' ' .
                    Utiles::cambiaTildeASuLetra($data['per_apellidomaterno']))
            );

        } catch (Exception $e) {
            Log::error("No es posible obtener el nombre_asociado_solicitud del rut: " . $runCiudadano);
        }
        return ($nombre);
    }

    //run_ciudadano
    public function getrunCiudadanoAttribute()
    {
        if (!isset($this->attributes['run_ciudadano'])) {
            $this->attributes['run_ciudadano'] = $this->runCiudadano($this->attributes['idsolicitud']);
        }
        return $this->attributes['run_ciudadano'];

    }

    public static function runCiudadano($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('run_ciudadano')
                ->first();
            $runCiudadano = ($data) ? $data->run_ciudadano : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el run_ciudadano de la solicitud: " . $idSolicitud);
        }
        return ($runCiudadano);
    }

    // NOMBRE TITULAR SOLICITUD

    public function getnombreTitularSolicitudAttribute()
    {
        if (!isset($this->attributes['nombre_titular_solicitud'])) {
            $this->attributes['nombre_titular_solicitud'] = $this->nombreTitularSolicitud($this->attributes['run_ciudadano']);
        }
        return $this->attributes['nombre_titular_solicitud'];

    }

    public static function nombreTitularSolicitud($runCiudadano)
    {
        try {
            $data = Personas::where('per_run', $runCiudadano)
                ->select('per_nombres', 'per_apellidopaterno', 'per_apellidomaterno')
                ->first();
            $nombre = ucwords(strtolower(
                    Utiles::cambiaTildeASuLetra($data['per_nombres']) . ' ' .
                    Utiles::cambiaTildeASuLetra($data['per_apellidopaterno']) . ' ' .
                    Utiles::cambiaTildeASuLetra($data['per_apellidomaterno']))
            );

        } catch (Exception $e) {
            Log::error("No es posible obtener el nombre_titular_solicitud del rut: " . $runCiudadano);
        }
        return ($nombre);
    }

    //ID PADRE SOLICITUD
    public function getIdPadreSolicitudAttribute()
    {
        if (!isset($this->attributes['id_padre_solicitud'])) {
            $this->attributes['id_padre_solicitud'] = $this->idPadreSolicitud($this->attributes['idsolicitud']);
        }
        return $this->attributes['id_padre_solicitud'];
    }

    public static function idPadreSolicitud($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('id_padre_solicitud')
                ->first();
            $idPadreSolicitud = ($data) ? $data->id_padre_solicitud : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el id_padre_solicitud de la solicitud: " . $idSolicitud);
        }
        return ($idPadreSolicitud);
    }

    //ID ESTADO SOLICITUD
    public function getidEstadoSolicitudAttribute()
    {
        if (!isset($this->attributes['id_estado_solicitud'])) {
            $this->attributes['id_estado_solicitud'] = $this->idEstadoSolicitud($this->attributes['idsolicitud']);
        }
        return $this->attributes['id_estado_solicitud'];
    }

    public static function idEstadoSolicitud($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('id_estado')
                ->first();
            $idEstadoSolicitud = ($data) ? $data->id_estado : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el id_estado de la solicitud: " . $idSolicitud);
        }
        return ($idEstadoSolicitud);
    }

    //STRING ESTADO SOLICITUD
    public function getestadoSolicitudAttribute()
    {
        if (!isset($this->attributes['estado_solicitud'])) {
            $this->attributes['estado_solicitud'] = $this->EstadoSolicitud($this->attributes['id_estado_solicitud']);
        }
        return $this->attributes['estado_solicitud'];
    }

    public static function estadoSolicitud($idEstadoSolicitud)
    {
        try {
            $data = EstadoSolicitud::where('id_estado', $idEstadoSolicitud)
                ->select('nombre_estado')
                ->first();
            $EstadoSolicitud = ($data) ? $data->nombre_estado : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el nombre_estado para el ID: " . $idEstadoSolicitud);
        }
        return ($EstadoSolicitud);
    }

    //ID SUB ESTADO SOLICITUD
    public function getidSubEstadoSolicitudAttribute()
    {
        if (!isset($this->attributes['id_sub_estado_solicitud'])) {
            $this->attributes['id_sub_estado_solicitud'] = $this->idSubEstadoSolicitud($this->attributes['idsolicitud']);
        }
        return $this->attributes['id_sub_estado_solicitud'];
    }

    public static function idSubEstadoSolicitud($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('id_sub_estado')
                ->first();
            $idSubEstadoSolicitud = ($data) ? $data->id_sub_estado : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el id_sub_estado de la solicitud: " . $idSolicitud);
        }
        return ($idSubEstadoSolicitud);
    }

    //STRING SUB ESTADO SOLICITUD
    public function getsubEstadoSolicitudAttribute()
    {
        if (!isset($this->attributes['sub_estado_solicitud'])) {
            $this->attributes['sub_estado_solicitud'] = $this->subEstadoSolicitud($this->attributes['id_sub_estado_solicitud']);
        }
        return $this->attributes['sub_estado_solicitud'];
    }

    public static function subEstadoSolicitud($idSubEstadoSolicitud)
    {
        try {
            $data = SubEstadoSolicitud::where('id_sub_estado', $idSubEstadoSolicitud)
                ->select('nombre_sub_estado')
                ->first();
            $SubEstadoSolicitud = ($data) ? $data->nombre_sub_estado : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el nombre_sub_estado par el ID: " . $idSubEstadoSolicitud);
        }
        return ($SubEstadoSolicitud);
    }

    //ID TIPO SOLICITUD
    public function getidTipoSolicitudAttribute()
    {
        if (!isset($this->attributes['id_tipo_solicitud'])) {
            $this->attributes['id_tipo_solicitud'] = $this->idTipoSolicitud($this->attributes['idsolicitud']);
        }
        return $this->attributes['id_tipo_solicitud'];
    }

    public static function idTipoSolicitud($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('id_tipo_solicitud')
                ->first();
            $idTipoSolicitud = ($data) ? $data->id_tipo_solicitud : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el id_tipo_solicitud de la solicitud: " . $idSolicitud);
        }
        return ($idTipoSolicitud);
    }

    //STRING TIPO DE SOLICITUD
    public function getTipoSolicitudAttribute()
    {
        if (!isset($this->attributes['tipo_solicitud'])) {
            $this->attributes['tipo_solicitud'] = $this->TipoSolicitud($this->attributes['id_tipo_solicitud']);
        }
        return $this->attributes['tipo_solicitud'];
    }

    public static function TipoSolicitud($idTipoSolicitud)
    {
        try {
            $data = TipoSolicitud::where('id_tipo_solicitud', $idTipoSolicitud)
                ->select('tipo_solicitud')
                ->first();
            $TipoSolicitud = ($data) ? $data->tipo_solicitud : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el tipo_solicitud para el ID: " . $idTipoSolicitud);
        }
        return ($TipoSolicitud);
    }

    //ID SUB TIPO SOLICITUD
    public function getidSubTipoSolicitudAttribute()
    {
        if (!isset($this->attributes['id_sub_tipo_solicitud'])) {
            $this->attributes['id_sub_tipo_solicitud'] = $this->IdSubTipoSolicitud($this->attributes['idsolicitud']);
        }
        return $this->attributes['id_sub_tipo_solicitud'];
    }

    public static function idSubTipoSolicitud($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('id_sub_tipo_solicitud')
                ->first();
            $idSubTipoSolicitud = ($data) ? $data->id_sub_tipo_solicitud : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el id_sub_tipo_solicitud de la solicitud: " . $idSolicitud);
        }
        return ($idSubTipoSolicitud);
    }

    //STRING SUB TIPO SOLICITUD
    public function getsubTipoSolicitudAttribute()
    {
        if (!isset($this->attributes['sub_tipo_solicitud'])) {
            $this->attributes['sub_tipo_solicitud'] = $this->subTipoSolicitud($this->attributes['id_tipo_solicitud'],
                $this->attributes['id_sub_tipo_solicitud']);
        }
        return $this->attributes['sub_tipo_solicitud'];
    }

    public static function subTipoSolicitud($idTipoSolicitud, $idSubTipoSolicitud)
    {
        try {
            $data = SubTipoSolicitud::where('id_tipo_solicitud', $idTipoSolicitud)
                ->where('id_sub_tipo_solicitud', $idSubTipoSolicitud)
                ->select('nombre_sub_tipo_solicitud')
                ->first();
            $subTipoSolicitud = ($data) ? $data->nombre_sub_tipo_solicitud : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el nombre_sub_tipo_solicitud para el tipo de solicitud: " . $idTipoSolicitud . ' y subtipo : ' . $idSubTipoSolicitud);
        }
        return ($subTipoSolicitud);
    }

    //vigente
    public function getvigenteAttribute()
    {
        if (!isset($this->attributes['vigente'])) {
            $this->attributes['vigente'] = $this->obtenerVigente($this->attributes['idsolicitud']);
        }
        return $this->attributes['vigente'];

    }

    public static function obtenerVigente($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('vigente')
                ->first();
            $fechaCreacion = ($data) ? $data->vigente : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el vigente de la solicitud: " . $idSolicitud);
        }
        return ($fechaCreacion);
    }

    //ID CODIGO COMUNA
    public function getcodigoComunaAttribute()
    {
        if (!isset($this->attributes['codigo_comuna'])) {
            $this->attributes['codigo_comuna'] = $this->codigoComuna($this->attributes['idsolicitud']);
        }
        return $this->attributes['codigo_comuna'];

    }

    public static function codigoComuna($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('com_id')
                ->first();
            $codigoComuna = ($data) ? $data->com_id : null;

        } catch (Exception $e) {
            Log::error("No es posible obtener el com_id de la solicitud: " . $idSolicitud);
        }
        return ($codigoComuna);
    }

    //STRING COMUNA
    public function getcomunaDescripcionAttribute()
    {
        if (!isset($this->attributes['comuna_descripcion'])) {
            $this->attributes['comuna_descripcion'] = $this->comunaDescripcion($this->attributes['codigo_comuna']);
        }
        return $this->attributes['comuna_descripcion'];
    }

    public static function comunaDescripcion($idComuna)
    {
        try {
            $data = Comuna::obtenerNombreComuna($idComuna);
            $comuna = ($data) ? ucwords(strtolower($data)) : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener la comuna  para la solicitud: " . $idComuna);
        }
        return ($comuna);
    }

    //ID CODIGO REGION
    public function getcodigoRegionAttribute()
    {
        if (!isset($this->attributes['codigo_region'])) {
            $this->attributes['codigo_region'] = $this->codigoRegion($this->attributes['codigo_comuna']);
        }
        return $this->attributes['codigo_region'];

    }

    public static function codigoRegion($comunaIne)
    {
        try {
            $data = Utiles::getRegionDesdeCodigoComuna($comunaIne);
            $codigoComuna = ($data) ? $data : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el codigo_region de la solicitud: " . $comunaIne);
        }
        return ($codigoComuna);
    }

    //STRING REGION
    public function getRegionDescripcionAttribute()
    {
        if (!isset($this->attributes['region_descripcion'])) {
            $this->attributes['region_descripcion'] = $this->regionDescripcion($this->attributes['codigo_region']);
        }
        return $this->attributes['region_descripcion'];
    }

    public static function regionDescripcion($idRegion)
    {
        try {
            $data = LocacionRegion::where('reg_id', (int)$idRegion)
                ->select('reg_descripcion')
                ->first();
            $region = ($data) ? ucwords(strtolower($data->reg_descripcion)) : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener la region  para la comuna id: " . $idRegion);
        }
        return ($region);

    }

    //FECHA CREACION
    public function getfechaCreacionAttribute()
    {
        if (!isset($this->attributes['fecha_creacion'])) {
            $this->attributes['fecha_creacion'] = $this->fechaCreacion($this->attributes['idsolicitud']);
        }
        return $this->attributes['fecha_creacion'];

    }

    public static function fechaCreacion($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('fecha_creacion')
                ->first();
            $fechaCreacion = ($data) ? $data->fecha_creacion : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener la fecha_creacion de la solicitud: " . $idSolicitud);
        }
        return ($fechaCreacion);
    }

    //FECHA MODIFICACION
    public function getfechaModificacionAttribute()
    {
        if (!isset($this->attributes['fecha_modificacion'])) {
            $this->attributes['fecha_modificacion'] = $this->fechaModificacion($this->attributes['idsolicitud']);
        }
        return $this->attributes['fecha_modificacion'];

    }

    public static function fechaModificacion($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('fecha_modificacion')
                ->first();
            $fechaModificacion = ($data) ? $data->fecha_modificacion : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener la fecha_modificacion de la solicitud: " . $idSolicitud);
        }
        return ($fechaModificacion);
    }

    //FECHA CREACION CIUDADANO
    public function getfechaCreacionCiudadanoAttribute()
    {
        if (!isset($this->attributes['fecha_creacion_ciudadano'])) {
            $this->attributes['fecha_creacion_ciudadano'] = $this->fechaCreacionCiudadano($this->attributes['idsolicitud']);
        }
        return $this->attributes['fecha_creacion_ciudadano'];

    }

    public static function fechaCreacionCiudadano($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('fecha_creacion_ciudadano')
                ->first();
            $fechaCreacionCiudadano = ($data) ? $data->fecha_creacion_ciudadano : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener la fecha_creacion_ciudadano de la solicitud: " . $idSolicitud);
        }
        return ($fechaCreacionCiudadano);
    }

    //FECHA CREACION CIUDADANO
    public function getcomentarioAttribute()
    {
        if (!isset($this->attributes['comentario'])) {
            $this->attributes['comentario'] = $this->obtenerComentario($this->attributes['idsolicitud']);
        }
        return $this->attributes['comentario'];

    }

    public static function obtenerComentario($idSolicitud)
    {
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                ->select('comentario')
                ->first();
            $comentario = ($data) ? $data->comentario : null;
        } catch (Exception $e) {
            Log::error("No es posible obtener el comentario de la solicitud: " . $idSolicitud);
        }
        return ($comentario);
    }


    public static function getModel()
    {
        return new ValidaSolicitudTercero();
    }

    public static function getSolicitud($runCiudadano, $idSolicitud)
    {
        $solicitud = null;
        try {
            $solicitud = ValidaSolicitudTercero::getModel()
                ->where('per_run', $runCiudadano)
                ->where('idsolicitud', $idSolicitud)
                ->first();
        } catch (Exception $ex) {
            throw $ex;
        }
        return new ValidaSolicitudTerceroData($solicitud);
    }

    public static function listSolicitudes($runCiudadano)
    {
        $datosDeSolicitud = null;
        $listaSolicitud = array();
        try {
            $datosDeSolicitud = ValidaSolicitudTercero::where('per_run', $runCiudadano)
                ->join('ab_solicitud', 'idsolicitud', '=', 'ab_solicitud.id_solicitud')
                ->whereNotIN('ab_solicitud.id_estado', array_merge([Constante::ESTADO_SOLICITUD_7_RECHAZADA],
                    [Constante::ESTADO_SOLICITUD_11_EN_DIGITACION],[Constante::ESTADO_SOLICITUD_9_DESISTIDA]))
                ->orderBy('estado_validacion','asc')
                ->orderBy('id','desc')
                ->get();
            foreach ($datosDeSolicitud as $solicitud) {
                $solicitud->id_estado = $solicitud->id_estado_solicitud;
                $listaSolicitud[] = $solicitud;
            }

        } catch (Exception $ex) {
            throw $ex;
        }

        if (empty($listaSolicitud)) {
            return null;
        }

        return $listaSolicitud;
    }

    /**
     * @return ValidaSolicitudTercero
     * @throws ExcepcionConsistencia
     */
    public static function findByIdValidacion($idValidacion)
    {
        $datosDeSolicitud = self::where('id', $idValidacion)
                            ->first();
        if (empty($datosDeSolicitud)) {
            throw ExcepcionConsistencia::new(Constante::MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA,
                Constante::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE)
                ->setContexto(['id' => $idValidacion]);
        }
        return $datosDeSolicitud;
    }

    /**
     * @return ValidaSolicitudTercero[]|Collection
     * @throws ExcepcionConsistencia
     */
    public static function findByIdSolicitud($idValidacion,$iSolicitud)
    {
        $datosDeSolicitud = self::where('idsolicitud', $iSolicitud)
            ->where('id','!=',$idValidacion)
            ->where('estado_validacion', 0)
            ->get();

        if (is_null($datosDeSolicitud)) {
            throw ExcepcionConsistencia::new(Constante::MENSAJE_ERROR_SOLICITUD_NO_ENCONTRADA,
                Constante::ERR_CONSISTENCIA_REGISTRO_NO_EXISTE)
                ->setContexto(['idsolicitud' => $iSolicitud]);
        }
        return $datosDeSolicitud;
    }

    public static function listSolicitudesCiudadano($idSolicitud)
    {
        $datosDeSolicitud = null;
        $listaSolicitud = array();
        try {
            $data = Solicitud::where('id_solicitud', $idSolicitud)
                    ->select('id_padre_solicitud','tipo_validacion_terceros')->first();

            $dataSolicitudes = Solicitud::where('id_padre_solicitud', $data->id_padre_solicitud)
                ->select('id_solicitud')->get();

            foreach ($dataSolicitudes as $dataSolicitud) {
                $datosDeSolicitud = ValidaSolicitudTercero::getModel()
                    ->where('idsolicitud', $dataSolicitud->id_solicitud)
                    ->orderBy('id')
                    ->get();
                foreach ($datosDeSolicitud as $solicitud) {
                    $listaSolicitud[$dataSolicitud->id_solicitud][] = $solicitud;
                }
            }

        } catch (Exception $ex) {
            throw $ex;
        }

        if (empty($listaSolicitud)) {
            return null;
        }

        return $listaSolicitud;
    }

}
