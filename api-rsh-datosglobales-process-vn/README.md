# REST API Process Base

## Requerimientos

* PHP 8.1

## Instalación 

```
composer install --no-dev
cp .env.example .env
php artisan key:generate
chown -R :www-data .
```

Editar .env para indicar token(s) estático(s) (seguridad base). Por ejemplo:

```
API_SERVER_HEADER_TOKENS=APIPROCESS_TOKEN1,APIPROCESS_TOKEN2
```

Editar .env para conectarse a una api system de ejemplo
```
#JWT_* necesario si se ocupa KONG
# (ver App\Providers\AppServiceProvider 'boot')
APISYSTEM_ACCESOS_URL=http://apisystem/api/v1
APISYSTEM_ACCESOS_HEADER_TOKEN=APISYSTEM_TOKEN1
#APISYSTEM_ACCESOS_JWT_KEY=
#APISYSTEM_ACCESOS_JWT_SECRET=
```


## Configuraciones
El archivo `.env` no debe versionarse. Un ejemplo de una configuración debe versionarse con el nombre `.env.example`.

## Seguridad base
A pesar que las API's pueden ser aseguradas de forma centralizada con un Api Gateway, igualmente cada API debe tener implementada una seguridad básica. 
El middleware `EnsureApiTokenIsValid` (ver ejemplo en `routes/api.php`) agrega una seguridad base a las API's la cual consiste en validar que el HTTP Header `Api-Token` del Request sea uno de los valores configurados en la entrada `API_SERVER_HEADER_TOKENS` del `.env`.
En la entrada `API_SERVER_HEADER_TOKENS` se pueden especificar uno o más token separados por ',' (coma).

## Estándar de respuestas
Existe el modelo `App\Models\MDSFApiResponse` que ayuda a mantener un estándar de respuesta, el cual tiene el siguiente formato json:

```
{
  "code": 0,
  "message": "",
  "time": "Y-m-d H:i:s.u"
  "data": [...]
}
```

donde

* `code`: Un número (código), mayor o igual a 1000, que representa el resultado de una operación. Cada implementación debe documentar los posibles valores. Para efectos de ordenar los código posibles, se puede seguir la siguiente agrupación:

| Rango        | Tipo                                                                             |
| ------------ | -------------------------------------------------------------------------------- |
| 1000 - 1099  | La API procesó la petición sin errores y con una respuesta con datos de negocio  |
| 1100 - 1199  | Error en la validación de uno(s) parámetros(s)                                   |
| 1200 - 1299  | Problemas al buscar datos en base de datos                                       |
| 1300 - 1399  | Problemas al usar otras fuentes de datos (por ejemplo, web services)             |
| 1400 - 1499  | Error capturado sin categoría (por ejemplo, catch genérico)                      | 

* `message`: Un mensaje relacionado con `code`.
* `time`: Marca de tiempo en el que ocurre la respuesta. Su formato es Y-m-d H:i:s.u
* `data`: Un objeto json o un arreglo de objetos json con los datos de negocio.

El modelo `App\Models\MDSFApiResponse` acepta la propiedad `http_code` con la que se puede complementar la respuesta con un HTTP STATUS CODE. Por defecto se usa `Response::HTTP_OK` (200).


## Buenas prácticas

* Conservar la seguridad base para las API utilizando el middleware ```EnsureApiTokenIsValid```.

## Docker

El MDSF provee imágenes docker base para el desarrollo y despliegue de soluciones. El repositorio de tales imágenes es el registry privado https://registryserver.mds.cl el cual tiene un visualizador en https://registry.mds.cl. Las credenciales de acceso deben solicitarse.
Se puede trabajar con Docker generando una imagen de desarrollo con el siguiente comando

```
docker build -t apiprocess-dev:1.0 . --target dev
```

Crear un docker network si es que aun no se ha creado. Este permite que los contenedores existentes en esta red podrán comunicarse entre sí tomando el nombre de los contenedores como dns.

```
docker network create base-net
```

Comando de ejemplo para crear el contenedor:

```
docker run --name apiprocess -d --network=base-net -p 81:80 -v "/directorio/codigo/fuente:/var/www/html/site" apiprocess-dev:1.0
```

Si es primera vez que se levanta el entorno de trabajo en docker, se debe ingresar al contenedor:

```
docker exec -ti apiprocess bash
```

y una vez dentro de contenedor ejecutar los pasos de instalación en la carpeta ```site```.


## API Gateway - KONG

Para probar esta api en KONG se debe crear un archivo de configuración ```kong.yml``` con el siguiente contenido:

```yaml
_format_version: "1.1"

services:
- name: AccesosApiProcessSrv
  url: http://apiprocess/api/v1
  routes:
  - name: AccesosApiProcessRoute
    paths:
    - /api/process/accesos/v1
  plugins:
  - name: jwt
    config:
      claims_to_verify: ["exp"]
      maximum_expiration: 600
  - name: request-transformer
    config:
      add:
        headers: ["Api-Token: APIPROCESS_TOKEN1"]

consumers:
- username: AccesosUserDev
  jwt_secrets:
  - key: accesos-dev
    secret: abc123
```

Luego, lanzar un contenedor con el siguiente comando (suponiendo que el arhivo kong.yml está en /mdsf/ejemplo_base/kong/kong.yml):

```
docker run -d --name kong2 --network=base-net -v "/mdsf/ejemplo_base/kong:/usr/local/kong/declarative" -e "KONG_DATABASE=off" -e "KONG_DECLARATIVE_CONFIG=/usr/local/kong/declarative/kong.yml" -e "KONG_PROXY_ACCESS_LOG=/dev/stdout" -e "KONG_ADMIN_ACCESS_LOG=/dev/stdout" -e "KONG_PROXY_ERROR_LOG=/dev/stderr" -e "KONG_ADMIN_ERROR_LOG=/dev/stderr" -e "TZ=America/Santiago" -p 83:8000 kong:2.4.1
```

En el caso que ya exista un contenedor kong funcionando en la red de contenedores (por ejemplo, base-net), se puede agregar la configuración del yml en el kong.yml que ya exista y luego ejecutar:

```
docker exec kong2 kong reload
```

La api ahora podrá probarse de esta forma (reemplazar ```token``` por un jwt válido):

```
curl http://localhost:83/api/process/accesos/v1/accesos -H "Authorization: Bearer token"
```

## Ejemplos

Curl de ejemplo para probar apis:

```
curl -XPOST http://localhost:81/api/v1/create -H "API-TOKEN: APIPROCESS_TOKEN1"
curl http://localhost:81/api/v1/getall -H "API-TOKEN: APIPROCESS_TOKEN1"
curl -XPOST http://localhost:81/api/v1/guardarformulario -H "API-TOKEN: APIPROCESS_TOKEN1" -F archivo=@rutaarchivo -F region=2 -F comuna=20101
```
