# Guía de Certificación de APIs para Negocio

**API:** Datos Globales RSH
**Herramienta:** Swagger UI (no requiere instalación ni conocimientos técnicos)

---

## 1. ¿En qué ambiente voy a certificar?

Cada ambiente es una instalación independiente de la API. Confirma con el equipo técnico **en cuál debes certificar** antes de comenzar:

| Ambiente | URL de Swagger UI | Token | Datos de prueba |
|---|---|---|---|
| **Desarrollo (local)** | `http://api-rsh-datosglobales-process.local.com/api/documentation` | Token de desarrollo | RUNs entregados por QA |
| **QA / Certificación** | *(completar URL del ambiente QA)* | Token de QA | RUNs entregados por QA |
| **Producción** | *(completar URL de producción)* | Token de producción | Casos acordados con el equipo técnico |

> ⚠️ **Importante:**
> - El **token es distinto por ambiente**: un token de desarrollo NO funciona en QA ni producción. Si te da error 401 (Unauthorized), revisa que estés usando el token correcto para ese ambiente.
> - **No uses datos de personas reales en producción** salvo que el equipo técnico lo autorice expresamente.
> - El resultado en un ambiente **no garantiza** el resultado en otro: certifica en el ambiente que te indiquen.

---

## 2. ¿Cómo ingreso?

Abre en tu navegador la URL del ambiente donde certificarás (ver tabla anterior).

Verás la lista de servicios (endpoints) agrupados por tema: **Personas**, **Solicitudes**, **Alertas**, etc.

---

## 3. Autorización (paso obligatorio, una sola vez)

Antes de probar cualquier servicio debes ingresar el token de acceso:

1. Presiona el botón verde **Authorize** (arriba a la derecha, con un candado).
2. En el campo de texto, pega el **Api-Token** (te lo entrega el equipo de integración).
3. Presiona **Authorize** y luego **Close**.

> El candado queda "cerrado". Ya puedes probar los servicios.

---

## 4. Cómo probar un servicio (paso a paso)

Ejemplo con el servicio **"Verificar si una persona posee RSH"**:

1. Expande el grupo **Personas** haciendo clic sobre él.
2. Haz clic en el servicio `GET /api/v1/getposeersh/{runCiudadano}`.
3. Presiona el botón **Try it out** (a la derecha).
4. En el campo **runCiudadano**, escribe un RUN real **sin puntos ni dígito verificador**. Ejemplo: `12345678`.
5. Presiona el botón azul **Execute**.
6. Revisa la respuesta en la sección **Responses** → **Response body**.

---

## 5. ¿Cómo sé si el servicio está correcto?

Toda respuesta tiene el mismo formato:

```json
{
  "code": 1000,
  "message": "",
  "time": "2026-09-15 14:00:00.000000",
  "data": { ... }
}
```

**Regla de certificación:**

| Lo que ves | Significado |
|---|---|
| `"code": 1000` | ✅ El servicio respondió correctamente. Los datos están en `"data"`. |
| `"code": 13xx` o `14xx` | ❌ El servicio tuvo un problema. Anota el `"code"` y el `"message"`. |
| Error de conexión / pantalla en blanco | ❌ El servicio no está disponible. Reporta al equipo técnico. |

**Certificado = code 1000 + los datos esperados en `data`.**

Ejemplo de respuesta correcta:

```json
{
  "code": 1000,
  "message": "",
  "time": "2026-09-15 14:14:09.022289",
  "data": {
    "posee-rsh": true,
    "mensaje": null
  }
}
```

---

## 6. Pauta de certificación

Para cada servicio que debas certificar, registra:

| Campo | Ejemplo |
|---|---|
| **Ambiente** | QA / Certificación |
| Servicio | `GET /api/v1/getposeersh/{runCiudadano}` |
| Dato de prueba usado | RUN `12345678` |
| ¿`code` = 1000? | Sí / No |
| ¿Los datos son correctos? | Sí / No |
| Resultado | ✅ Certificado / ❌ Rechazado |
| Observación | (opcional) |

> El **ambiente** es obligatorio registrarlo: un servicio puede estar certificado en QA pero aún pendiente en producción.

---

## 7. ¿Qué reporto si algo falla?

Si un servicio no certifica, copia y envía al equipo técnico:

1. **El ambiente donde probaste** (ej: QA)
2. **El servicio probado** (ej: `GET /api/v1/getposeersh/...`)
3. **El dato usado** (ej: RUN `12345678`)
4. **El `code` y `message` de la respuesta** (ej: `code 1402 — "Problemas al obtener posee rsh"`)

Con esos 4 datos el equipo técnico puede investigar sin pedirte más información.

---

## Preguntas frecuentes

**¿El token me lo pide cada vez?**
No. Se ingresa una vez por sesión del navegador (botón Authorize). Pero si cambias de ambiente, debes ingresar el token de ese ambiente.

**¿Puedo dañar algo probando?**
No. Los servicios de consulta (GET) solo leen información. No modifican datos. Los POST de esta API (como consultar bloqueos o validar cartolas) también son de consulta; no alteran registros.

**¿De dónde saco RUNs de prueba?**
El equipo de QA/integración te entrega una lista de casos de prueba con los resultados esperados **por ambiente**.

**¿Qué pasa si cambio de ambiente?**
Cierra la sesión del navegador (o abre una ventana de incógnito) para el otro ambiente, ingresa a la URL del ambiente nuevo y usa su token correspondiente.
