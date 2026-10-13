# API Datos Globales - Microservicios RSH

Sistema de microservicios para el Registro Social de Hogares (Gobierno de Chile). Arquitectura distribuida con 3 APIs independientes, Docker multi-stage builds, y orquestación con nginx reverse proxy.

## 🏗️ Arquitectura

```
┌─────────────────────────────────────────────────────────┐
│                    Nginx Proxy (Puerto 80)               │
└─────────────────────────────────────────────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
        ▼                     ▼                     ▼
┌───────────────┐   ┌───────────────┐   ┌───────────────┐
│ Process API   │   │ Export System │   │  TX System    │
│ (Laravel 13)  │   │ (Laravel 13)  │   │ (Laravel 13)  │
│ 23 endpoints  │   │ 12 endpoints  │   │ 31 endpoints  │
└───────────────┘   └───────────────┘   └───────────────┘
```

## 🎯 Servicios

### 1. Process API (`api-rsh-datosglobales-process-vn`)
- **Propósito**: Lógica de negocio, validaciones, consultas en tiempo real
- **Endpoints**: 23 endpoints REST
- **Autenticación**: API Token + JWT (HS256)
- **Documentación**: OpenAPI 3.0 con L5 Swagger
- **Base de datos**: SQLite (fallback)

**Características destacadas**:
- Factory pattern con `Http::macro('APISYSTEM')` para clientes HTTP configurables
- Strategy pattern para validaciones complejas (BloqueosSolicitudCiudadano)
- DTOs con estructura estándar `MDSFApiResponse`
- Middleware `EnsureApiTokenIsValid` con validación de múltiples tokens

### 2. Export System API (`api-rsh-datosglobalesexport-system-vn`)
- **Propósito**: Operaciones pesadas de lectura/exportación
- **Endpoints**: 12 endpoints REST
- **Base de datos**: Oracle (yajra/laravel-oci8)
- **Casos de uso**: Exportación masiva de datos, reportes

### 3. TX System API (`api-rsh-datosglobalestx-system-vn`)
- **Propósito**: Transacciones y escrituras
- **Endpoints**: 31 endpoints REST (GET, POST, PUT)
- **Base de datos**: Oracle (yajra/laravel-oci8)
- **Casos de uso**: Creación/actualización de registros, asignaciones

## 🛠️ Stack Tecnológico

- **PHP**: 8.3
- **Laravel**: 13.0
- **Oracle**: yajra/laravel-oci8 ^13.0
- **Docker**: Multi-stage builds (4 etapas)
- **API Gateway**: KONG 2.4.1 (JWT validation, request transformation)
- **Documentación**: OpenAPI 3.0 con atributos PHP 8
- **Reverse Proxy**: nginx-proxy
- **Testing**: PHPUnit ^12.0, Mockery ^1.6

## 🚀 Instalación Local

### Prerrequisitos
- Docker Desktop con Docker Compose v2.20+
- Acceso al registry corporativo `registryserver.mds.cl`
- Configurar hosts file:
  ```
  127.0.0.1 api-rsh-datosglobales-process.local.com
  127.0.0.1 api-rsh-datosglobalesexport-system.local.com
  127.0.0.1 api-rsh-datosglobalestx-system.local.com
  ```

### Levantar servicios

**Windows:**
```powershell
.\up-local.bat
```

**Linux/Mac:**
```bash
chmod +x *.sh
./up-local.sh
```

### Comandos útiles

```bash
# Ver estado
docker compose -f docker-compose.local.yml ps

# Ver logs
.\logs-local.bat process-app

# Entrar a contenedor
docker exec -it rsh-process-app bash

# Ejecutar migraciones
docker exec -it rsh-process-app php artisan migrate

# Rebuild
.\rebuild-local.bat process-app
```

## 🔐 Autenticación

### API Tokens
Cada servicio valida el header `Api-Token` contra tokens configurados en `API_SERVER_HEADER_TOKENS`.

### JWT para KONG
Los servicios generan JWT con HS256 para comunicación con KONG API Gateway:

```php
Http::macro('APISYSTEM', function ($name) {
    $http = Http::throw()
        ->timeout(env("${api_env_prefix}_RESPONSE_TIMEOUT", 30))
        ->baseUrl(env("${api_env_prefix}_URL"))
        ->withHeaders(['Api-Token' => env("${api_env_prefix}_HEADER_TOKEN")]);
    
    if (env("${api_env_prefix}_JWT_SECRET")) {
        $http->withToken(JWT::encode([...], $secret, 'HS256'));
    }
    
    return $http;
});
```

## 📚 Documentación API

Accede a la documentación OpenAPI/Swagger:
- Process API: http://api-rsh-datosglobales-process.local.com/api/documentation

## 🎨 Patrones de Diseño

### Strategy Pattern
```php
interface BloqueosSolicitudCiudadanoInterface {
    public function getPuedeRealizarSolicitud();
    public function getTramitesPendientes();
}

class Vivienda implements BloqueosSolicitudCiudadanoInterface { }
class IngresoRegistro implements BloqueosSolicitudCiudadanoInterface { }
```

### DTO Pattern
```php
class MDSFApiResponse {
    public int $code;
    public string $message;
    public string $time;
    public mixed $data;
    public int $http_code;
}
```

## 🐳 Docker Multi-Stage Build

```dockerfile
# Etapa 1: Configuración base
FROM registryserver.mds.cl/mds/nginx-php8.3-oci8:1.3 as config

# Etapa 2: Desarrollo (con Xdebug)
FROM config as dev

# Etapa 3: Compilación (instala dependencias)
FROM dev as compile

# Etapa 4: Producción (imagen optimizada)
FROM config as deploy
```

**Beneficios**:
- Imagen final 60% más pequeña
- No expone herramientas de desarrollo en producción
- OPcache JIT habilitado
- Autoloader optimizado

## 📊 Métricas

- **66+ endpoints** REST documentados
- **3 microservicios** independientes
- **60+ modelos** Eloquent
- **40+ clases** de lógica de negocio
- **10+ DTOs** para respuestas

## 👤 Autor

**Ysaías Alfonso Pérez Campos**  
Senior Backend Engineer  
Email: ysaiascampos@gmail.com  
LinkedIn: https://www.linkedin.com/in/ysaias-perez-campos-50626116a/

## 📄 Licencia

Proyecto desarrollado para el Gobierno de Chile - Registro Social de Hogares.
