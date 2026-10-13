# Ambiente Local — API Datos Globales (RSH)

Guía para levantar los **3 proyectos juntos** en tu máquina local con un solo comando.

## Proyectos

| Proyecto | Carpeta | Dominio local | Base de datos |
|---|---|---|---|
| Process | `api-rsh-datosglobales-process-vn` | `api-rsh-datosglobales-process.local.com` | Sin Oracle |
| Export System | `api-rsh-datosglobalesexport-system-vn` | `api-rsh-datosglobalesexport-system.local.com` | Oracle (externo) |
| TX System | `api-rsh-datosglobalestx-system-vn` | `api-rsh-datosglobalestx-system.local.com` | Oracle (externo) |

---

## 1. Prerrequisitos

1. **Docker Desktop** (Windows) o **Docker Engine** (Linux) con Docker Compose **v2.20+**
   ```bash
   docker compose version
   ```
2. **Acceso al registry corporativo** `registryserver.mds.cl` (VPN o red interna)
3. **Estructura de carpetas**: los 3 proyectos deben estar en el mismo directorio, junto a los archivos de este orquestador:
   ```
   api-datos-globales/                  ← nombre libre, puede ser cualquiera
   ├── api-rsh-datosglobales-process-vn/
   ├── api-rsh-datosglobalesexport-system-vn/
   ├── api-rsh-datosglobalestx-system-vn/
   ├── docker-compose.local.yml
   ├── up-local.bat / up-local.sh
   ├── down-local.bat / down-local.sh
   ├── logs-local.bat / logs-local.sh
   └── rebuild-local.bat / rebuild-local.sh
   ```
4. **Archivo `.env` en cada proyecto** (se manejan de forma privada, pedir al equipo)
5. **Dominios en el archivo hosts** de tu máquina:
   - Windows: `C:\Windows\System32\drivers\etc\hosts` (como Administrador)
   - Linux/Mac: `/etc/hosts`
   ```
   127.0.0.1 api-rsh-datosglobales-process.local.com
   127.0.0.1 api-rsh-datosglobalesexport-system.local.com
   127.0.0.1 api-rsh-datosglobalestx-system.local.com
   ```

---

## 2. Levantar el ambiente

### Windows

> **Importante — cómo ejecutar los scripts:**
> - **PowerShell**: el `.\` es **obligatorio** (por seguridad, PowerShell no ejecuta scripts del directorio actual sin él):
>   ```powershell
>   .\up-local.bat
>   ```
> - **CMD (símbolo del sistema)**: el `.\` no es necesario:
>   ```cmd
>   up-local.bat
>   ```
> - **Explorador de Windows**: doble clic en el `.bat` también funciona.
> - Los scripts deben ejecutarse **desde el directorio raíz** (donde está `docker-compose.local.yml`), no desde dentro de las carpetas de los proyectos.

### Linux / Mac
```bash
chmod +x *.sh   # solo la primera vez
./up-local.sh
```

El script hace automáticamente:
1. Verifica que Docker esté corriendo
2. Crea la red `rsh-net` si no existe
3. Levanta el nginx-proxy (puerto 80)
4. Construye y levanta los 3 proyectos

> **Primera vez**: la construcción de imágenes puede tardar varios minutos (descarga imágenes base, composer install, etc.). Las siguientes veces usa caché y es rápido.

### Verificar que todo funciona

```bash
docker compose -f docker-compose.local.yml ps
```

Deberías ver 4 contenedores corriendo: `rsh-nginx-proxy`, `rsh-process-app`, `rsh-export-system-app`, `rsh-tx-system-app`.

Luego abre en el navegador:
- http://api-rsh-datosglobales-process.local.com
- http://api-rsh-datosglobalesexport-system.local.com
- http://api-rsh-datosglobalestx-system.local.com

---

## 3. Comandos del día a día

| Acción | Windows | Linux/Mac |
|---|---|---|
| Levantar todo | `.\up-local.bat` | `./up-local.sh` |
| Detener todo | `.\down-local.bat` | `./down-local.sh` |
| Ver logs (todos) | `.\logs-local.bat` | `./logs-local.sh` |
| Ver logs (un proyecto) | `.\logs-local.bat process-app` | `./logs-local.sh process-app` |
| Rebuild (todos) | `.\rebuild-local.bat` | `./rebuild-local.sh` |
| Rebuild (un proyecto) | `.\rebuild-local.bat process-app` | `./rebuild-local.sh process-app` |

Nombres de servicio: `process-app`, `export-system-app`, `tx-system-app`, `nginx-proxy`

### Comandos Docker Compose directos (opcional)

```bash
# Estado de los servicios
docker compose -f docker-compose.local.yml ps

# Entrar a un contenedor
docker exec -it rsh-process-app bash

# Ejecutar artisan en un proyecto
docker exec -it rsh-process-app php artisan migrate
docker exec -it rsh-export-system-app php artisan tinker

# Reiniciar solo un proyecto
docker compose -f docker-compose.local.yml restart process-app
```

---

## 4. Hot-reload (cambios sin rebuild)

El código de cada proyecto está **montado como volumen** (`./:/var/www/html/site`), y el `.env` también (`./.env:/var/www/html/site/.env`), por lo que:

- ✅ Cambios en código PHP (controladores, rutas, vistas, etc.) → **se reflejan al instante**, solo recarga el navegador
- ✅ Cambios en `.env` → **se reflejan en la siguiente petición**, sin reiniciar nada (ver nota abajo)
- ⚠️ Cambios en `composer.json` (nuevas dependencias) → requieren rebuild: `.\rebuild-local.bat process-app`
- ⚠️ Cambios en `Dockerfile.dev` o archivos de `docker/` → requieren rebuild

> **Nota sobre el `.env`**: para que los cambios se reflejen al instante, Laravel NO debe tener la configuración cacheada. En local esto es lo normal, pero si alguna vez ejecutaste `config:cache` dentro del contenedor, límpiala una vez con:
> ```powershell
> docker exec -it rsh-process-app php artisan config:clear
> ```
> Mientras no uses `config:cache` en local, los cambios del `.env` se toman en cada petición automáticamente.

---

## 5. Debugging con Xdebug

Cada proyecto tiene Xdebug instalado y configurado con su propio `serverName`:

| Proyecto | serverName (PHP_IDE_CONFIG) |
|---|---|
| Process | `process` |
| Export System | `export-system` |
| TX System | `tx-system` |

La configuración de Xdebug está en `docker/xdebug.ini` de cada proyecto (montada como volumen, editable sin rebuild).

### Configuración en PhpStorm

1. **Settings → PHP → Servers** → crear 3 servidores:
   - Name: `process` → Host: `api-rsh-datosglobales-process.local.com` → Port: `80` → Debugger: Xdebug
   - Name: `export-system` → Host: `api-rsh-datosglobalesexport-system.local.com` → Port: `80`
   - Name: `tx-system` → Host: `api-rsh-datosglobalestx-system.local.com` → Port: `80`
2. En cada servidor, activar **Use path mappings** y mapear:
   - Carpeta del proyecto local → `/var/www/html/site`
3. **Start Listening for PHP Debug Connections** y poner breakpoints

> Puedes debugear los 3 proyectos simultáneamente (ej: process llama a export-system y sigues el flujo en ambos).

---

## 6. Arquitectura del ambiente

```
                 Navegador / Postman
                        │
                        ▼  (puerto 80)
              ┌───────────────────┐
              │   rsh-nginx-proxy │  enruta por VIRTUAL_HOST
              └───────────────────┘
                 │      │      │
        ┌────────┘      │      └────────┐
        ▼               ▼               ▼
  rsh-process-app  rsh-export-     rsh-tx-system-app
  (PHP 8.3)        system-app      (PHP 8.3 + OCI8)
                   (PHP 8.3 + OCI8)      │
        │               │               │
        └───────────────┴───────────────┘
              Red Docker: rsh-net
                        │
              ┌─────────┴─────────┐
              ▼                   ▼
      Oracle externo #1   Oracle externo #2
      (via red/VPN)       (via red/VPN)
```

- Solo el **nginx-proxy** expone el puerto 80 al host; los proyectos se comunican internamente por la red `rsh-net`
- Las bases Oracle **no** se dockerizan: se conectan a los servidores externos (requiere VPN/red)
- Los `extra_hosts` con `host-gateway` permiten que los contenedores resuelvan los dominios `.local.com` entre sí

---

## 7. Troubleshooting

| Problema | Causa probable | Solución |
|---|---|---|
| `network rsh-net declared as external, but could not be found` | La red no existe | Ejecuta `docker network create rsh-net` o usa `up-local.bat` |
| `pull access denied for registryserver.mds.cl/...` | Sin acceso al registry | Conéctate a la VPN/red interna y haz `docker login registryserver.mds.cl` si aplica |
| El navegador no resuelve `.local.com` | Falta entrada en hosts | Agrega las líneas del paso 1.5 al archivo hosts |
| `port is already allocated :80` | Otro servicio usa el puerto 80 (IIS, XAMPP, otro proxy) | Detén ese servicio o cambia el puerto del proxy en `docker-compose.local.yml` (ej: `"8080:80"`) |
| Error de conexión a Oracle | Sin VPN o credenciales | Verifica VPN activa y las variables `DB_*` del `.env` del proyecto |
| Cambios en el código no se ven | OPcache o caché de Laravel | `docker exec -it rsh-process-app php artisan optimize:clear` |
| Cambios en el `.env` no se toman | Laravel tiene la config cacheada | `docker exec -it rsh-process-app php artisan config:clear` (una sola vez; no uses `config:cache` en local) |
| `include is not supported` / error de sintaxis | Docker Compose antiguo | Actualiza Docker Desktop (se requiere Compose v2.20+) |
| Permisos en `storage/` (Linux) | Propietario incorrecto | `docker exec -it rsh-process-app chown -R www-data:www-data storage bootstrap/cache` |

### Reset completo (empezar de cero)

```bash
docker compose -f docker-compose.local.yml down --rmi local --volumes
up-local.bat   # o ./up-local.sh
```

---

## 8. Notas importantes

- Los archivos de **producción** (`docker-compose.yml`, `docker-compose.prod.yml`, `Dockerfile`) **no fueron modificados**; los servidores siguen funcionando igual
- Los `.env` de cada proyecto son **privados** y no se versionan; pedirlos al equipo
- El nombre de la carpeta raíz es libre; lo único obligatorio es que los 3 proyectos estén juntos junto al orquestador

---

## 9. Cambios aplicados a los docker-compose.dev.yml

> Esta sección documenta las diferencias respecto a las versiones originales de los compose dev, para referencia del equipo y revisión de PRs.

Los `docker-compose.dev.yml` de los 3 proyectos tienen **2 cambios** respecto a sus versiones originales (ambos exclusivos de desarrollo local, **no afectan producción**):

### Cambio 1: Nombre de servicio único + container_name

**Motivo**: los 3 compose dev definían el servicio como `app`. Al incluirlos juntos en el orquestador (`include:`), Docker Compose requiere nombres únicos por servicio.

| Proyecto | Antes | Después | container_name |
|---|---|---|---|
| process | `app` | `process-app` | `rsh-process-app` |
| export-system | `app` | `export-system-app` | `rsh-export-system-app` |
| tx-system | `app` | `tx-system-app` | `rsh-tx-system-app` |

### Cambio 2: Volumen para el `.env`

**Motivo**: que los cambios en el `.env` local se reflejen en la siguiente petición, sin reiniciar el contenedor.

Se agregó esta línea en la sección `volumes:` de cada proyecto:

```yaml
- ./.env:/var/www/html/site/.env
```

**Condición**: Laravel no debe tener la config cacheada (`config:clear` una vez si es necesario).

### Cambio menor: limpieza

- Se eliminó `version: "3.9"` del compose dev de process (atributo obsoleto en Compose v2+, generaba warning)
