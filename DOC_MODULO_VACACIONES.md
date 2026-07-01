# Módulo de Gestión de Vacaciones de Conductores

Documento de diseño e implementación. Módulo interno dentro de **Abastecimiento** del sistema SIGA (MPFN-DF Arequipa).
Operado **exclusivamente por la encargada de RR.HH.** (no hay autoservicio para conductores). Prioridad: velocidad de registro, flexibilidad total y control del impacto operativo de la flota.

> Estado: **Fases 0–5 implementadas (módulo completo).** Fase 0 = tablas + catálogo + config. Fase 1 = sincronizar/buscar/listar conductores + `vacaciones_listado.php`. Fase 2 = periodos + tramos con control de saldo + `vacaciones_registro.php`. Fase 3 = motor de tope de flota (`vaca_validar_cupo` integrado en `guardar_programacion` + acción `calendario_ocupacion`) + `vacaciones_calendario.php`; verificado: 5.º conductor el mismo día bloqueado con lista de fechas, calendario muestra ocupación y nombres. Fase 4 = reprogramación en cadena atómica (acción `reprogramar`) + anulación de tramo (`anular_tramo`) + lectura de auditoría (`obtener_historial`), todo con escritura en `mp_vaca_historial`; UI de reprogramar/anular en `vacaciones_registro.php` (modo "Reprogramar todo" + botón anular por tramo) y línea de tiempo en `vacaciones_detalle.php`. Fase 5 = alta de menú/submenú colgando de **Abastecimiento** (`mp_admi_subm.iden_subm=83`, grupo "Vacaciones Conductores" con 5 páginas) + permisos en `mp_admi_role_subm` (rol Admin=2; bloque parametrizado para el rol de RR.HH.) en `DB_SCHEMA_VACACIONES.sql`, y reporte formato Excel (§8) con exportación `.xls` e impresión en `vacaciones_reporte.php`. **Nota:** el SQL de Fase 5 (menú + permisos) debe ejecutarse en la BD para que el módulo aparezca en el menú. Ver convenciones generales del sistema en `CLAUDE.md`.
>
> **Fase 6 (mejoras) implementada:** (a) **Carga masiva desde Excel** — `vacaciones_importar.php` (subir `.xlsx/.xls/.csv` con PhpSpreadsheet **o** pegar la tabla) con previsualización fila a fila (OK / advertencia / error / duplicada) y confirmación transaccional; acciones `importar_previsualizar` e `importar_confirmar` + helper `vaca_procesar_importacion`. Empareja conductor por "APELLIDOS Y NOMBRES" y periodo por etiqueta; crea la instancia de periodo si falta (bypass de la ventana §6.4 por ser carga autoritativa); las filas que superan el tope se importan igual **con advertencia** (decisión del usuario). (b) **Tope de flota configurable** — acciones `obtener_config`/`guardar_config` sobre `mp_admi_conf` (`VACA_TOPE_FLOTA`, `VACA_DIAS_PERIODO`) con UI (engranaje) en `vacaciones_calendario.php`; el tope deja de ser fijo en 4. (c) **Choferes terceros** (contratados que NO están en `mp_maes_personal`) — se guardan en la misma `mp_vaca_conductor` con `es_tercero=1` e `iden_pers` NULL (columna nueva + `iden_pers` nullable, con ALTER idempotente; la sincronización no los toca ni borra). El importador marca los nombres no encontrados como **"Nuevo tercero"** con checkbox por fila y los crea al confirmar (`crear_tercero` + parseo de "APELLIDOS Y NOMBRES"); además hay alta manual (botón "Agregar tercero" en `vacaciones_listado.php`, con badge "Tercero" en la grilla).

---

## 1. Convenciones (alineadas al módulo de Almacén, el más moderno del sistema)

| Aspecto | Convención |
|---|---|
| Tablas | prefijo `mp_vaca_*` |
| Columnas | snake_case descriptivo (`id_conductor`, `fecha_inicio`) |
| Páginas | `vacaciones_*.php` (`_listado`, `_calendario`, `_registro`, `_detalle`) |
| API | `vacaciones_controller.php` (JSON, por `?action=`, espejo de `almacen_controller.php`) |
| Acceso a datos | `classes/Db.class.php` en páginas; PDO directo con transacciones en el controller |
| Usuario que registra | `$_SESSION['iden_oper']` |
| Fechas | almacenadas `DATE` (YYYY-MM-DD); mostradas `dd/mm/yyyy` |

---

## 2. Origen de datos — conductores

Maestro propio `mp_vaca_conductor`, **sincronizado manualmente** (snapshot) desde `mp_maes_personal`:

```sql
SELECT iden_pers, ndoc_pers, appa_pers, apma_pers, nomb_pers, fech_ingr, regi_labo, esta_pers
FROM mp_maes_personal
WHERE codi_carg = 6 AND esta_pers = 1;   -- 6 = 'ASIST. ADM.(CONDUCTOR)' en mp_maes_cargo
```

- **Régimen**: `regi_labo` → `mp_maes_regimen_laboral.x_nombre` (ej. `DL.728`, `CAS`).
- **Fecha de ingreso**: `fech_ingr` (gobierna la ventana de periodos programables).
- Flota actual ≈ **37 conductores**.
- La sincronización es *upsert* idempotente: da de alta nuevos y actualiza nombres/régimen/fecha; **no** borra ni pisa programaciones existentes. El calendario lee solo de la tabla local (rápido, sin JOINs externos).

---

## 3. Decisiones de diseño (cerradas)

1. **Periodo = etiqueta de calendario fija** (no anclada al aniversario). "2024-2025" es un rótulo compartido por toda la flota; las fechas en que se toma el descanso pueden caer después (ej. periodo 2024-2025 tomado en enero 2026). → catálogo global `mp_vaca_periodo_cat`.
2. **Conteo en días calendario inclusivos**: `dias = DATEDIFF(fecha_fin, fecha_inicio) + 1`. Cuenta sábados y domingos. No se usa tabla de feriados. El tope de flota protege **todos** los días.
3. **30 días obligatorios y deben agotarse** por periodo:
   - Suma de tramos **> 30** → **bloqueo duro** (no se guarda).
   - Suma de tramos **< 30** → se guarda, pero el periodo queda `INCOMPLETO` y se muestra **aviso**: "Faltan X días por programar (debe completar 30)".
   - Suma **= 30** → periodo `COMPLETO`.
4. **30 días para todos** (sin variación por régimen). `dias_asignados = 30` fijo.
5. **Tope de 4 ausencias simultáneas/día, global a toda la flota** (no se segmenta por dependencia/local).
6. **Carga manual** (sin importador de Excel).
7. **Conductores: snapshot + re-sync manual** (botón "Sincronizar").
8. **Ventana de programación**: como máximo el **periodo vigente + un periodo futuro** abiertos a la vez por conductor (ver §6.4).

---

## 4. Modelo de datos (`DB_SCHEMA_VACACIONES.sql`)

### 4.1 `mp_vaca_conductor` — maestro
```sql
CREATE TABLE IF NOT EXISTS mp_vaca_conductor (
  id_conductor      INT(11)     NOT NULL AUTO_INCREMENT,
  iden_pers         INT(11)     NULL,                -- FK lógica mp_maes_personal.iden_pers (NULL en terceros)
  ndoc              CHAR(8)     NOT NULL DEFAULT '',
  appat             VARCHAR(60) NOT NULL,
  apmat             VARCHAR(60) NOT NULL,
  nombres           VARCHAR(100) NOT NULL,
  regimen           VARCHAR(40) NOT NULL,           -- DL.728 / CAS / TERCEROS
  fecha_ingreso     DATE        NOT NULL,
  dias_por_periodo  INT(3)      NOT NULL DEFAULT 30,
  es_tercero        INT(1)      NOT NULL DEFAULT 0,  -- 1 = chofer tercero (no está en mp_maes_personal)
  estado            INT(1)      NOT NULL DEFAULT 1,
  fecha_reg         DATETIME    NOT NULL,
  PRIMARY KEY (id_conductor),
  UNIQUE KEY uq_iden_pers (iden_pers)                -- múltiples NULL permitidos
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
Nombre para reporte ("APELLIDOS Y NOMBRES"): `CONCAT(appat,' ',apmat,', ',nombres)` → `CORIMANYA LAZARO, JOSE LUIS JUAN`.

### 4.2 `mp_vaca_periodo_cat` — catálogo global de periodos
```sql
CREATE TABLE IF NOT EXISTS mp_vaca_periodo_cat (
  id_periodo_cat  INT(11)   NOT NULL AUTO_INCREMENT,
  etiqueta        VARCHAR(9) NOT NULL,      -- '2024-2025'
  anio_inicio     INT(4)    NOT NULL,       -- 2024
  anio_fin        INT(4)    NOT NULL,       -- 2025
  orden           INT(3)    NOT NULL,       -- para secuencia/ventana
  estado          INT(1)    NOT NULL DEFAULT 1,
  PRIMARY KEY (id_periodo_cat),
  UNIQUE KEY uq_etiqueta (etiqueta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 4.3 `mp_vaca_periodo` — instancia de periodo por conductor
```sql
CREATE TABLE IF NOT EXISTS mp_vaca_periodo (
  id_periodo      INT(11)   NOT NULL AUTO_INCREMENT,
  id_conductor    INT(11)   NOT NULL,
  id_periodo_cat  INT(11)   NOT NULL,
  etiqueta        VARCHAR(9) NOT NULL,       -- denormalizado para reporte
  dias_asignados  INT(3)    NOT NULL DEFAULT 30,
  estado          ENUM('INCOMPLETO','COMPLETO','CERRADO') NOT NULL DEFAULT 'INCOMPLETO',
  fecha_reg       DATETIME  NOT NULL,
  PRIMARY KEY (id_periodo),
  UNIQUE KEY uq_cond_periodo (id_conductor, id_periodo_cat),
  KEY idx_conductor (id_conductor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
**Saldo NO se almacena** como verdad: `saldo = dias_asignados − SUM(dias de tramos ACTIVOS)`. `estado` se recalcula tras cada operación.

### 4.4 `mp_vaca_tramo` — bloques editables ("15 en enero, 10 en febrero…")
```sql
CREATE TABLE IF NOT EXISTS mp_vaca_tramo (
  id_tramo        INT(11)   NOT NULL AUTO_INCREMENT,
  id_periodo      INT(11)   NOT NULL,
  id_conductor    INT(11)   NOT NULL,        -- denormalizado para concurrencia
  fecha_inicio    DATE      NOT NULL,
  fecha_fin       DATE      NOT NULL,
  dias            INT(3)    NOT NULL,         -- DATEDIFF(fecha_fin,fecha_inicio)+1
  estado          ENUM('ACTIVO','REEMPLAZADO','ANULADO') NOT NULL DEFAULT 'ACTIVO',
  id_tramo_origen INT(11)   DEFAULT NULL,     -- encadena reprogramaciones sucesivas
  fecha_reg       DATETIME  NOT NULL,
  id_oper_reg     INT(11)   NOT NULL,
  PRIMARY KEY (id_tramo),
  KEY idx_periodo_estado (id_periodo, estado),
  KEY idx_conductor (id_conductor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 4.5 `mp_vaca_dia` — grano diario (motor del calendario y del tope)
```sql
CREATE TABLE IF NOT EXISTS mp_vaca_dia (
  id_dia        INT(11) NOT NULL AUTO_INCREMENT,
  id_tramo      INT(11) NOT NULL,
  id_conductor  INT(11) NOT NULL,
  id_periodo    INT(11) NOT NULL,
  fecha         DATE    NOT NULL,
  estado        ENUM('ACTIVO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (id_dia),
  KEY idx_fecha (fecha),
  KEY idx_conductor_fecha (id_conductor, fecha),
  KEY idx_tramo (id_tramo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
Cada tramo se **explota en filas-día** al guardar. Concurrencia y calendario = simple `GROUP BY fecha`. En reprogramación, los días de los tramos reemplazados se **eliminan** (la tabla solo contiene días ACTIVOS).

### 4.6 `mp_vaca_historial` — auditoría de cambios
```sql
CREATE TABLE IF NOT EXISTS mp_vaca_historial (
  id_hist          INT(11) NOT NULL AUTO_INCREMENT,
  id_conductor     INT(11) NOT NULL,
  id_periodo       INT(11) NOT NULL,
  accion           ENUM('CREA','REPROGRAMA','ANULA') NOT NULL,
  detalle_antes    TEXT,                 -- JSON de tramos previos
  detalle_despues  TEXT,                 -- JSON de tramos nuevos
  dias_liberados   INT(3)  NOT NULL DEFAULT 0,
  dias_consumidos  INT(3)  NOT NULL DEFAULT 0,
  saldo_resultante INT(3)  NOT NULL,
  id_oper          INT(11) NOT NULL,
  fecha_hora       DATETIME NOT NULL,
  PRIMARY KEY (id_hist),
  KEY idx_conductor (id_conductor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 4.7 Configuración
Dos parámetros ajustables sin tocar código, en `mp_admi_conf` (`nomb_conf`/`valo_conf`):
- `VACA_TOPE_FLOTA = 4` (ausencias simultáneas máximas/día)
- `VACA_DIAS_PERIODO = 30`

El controller los lee con *fallback* a constantes (`4` y `30`).

---

## 5. API — `vacaciones_controller.php`

Respuestas siempre `{"success": bool, ...}`. Estructura idéntica a `almacen_controller.php` (lee credenciales de `classes/.credentials/db.php.ini`, PDO con `ERRMODE_EXCEPTION`, transacciones).

### Lectura (GET)
| action | Parámetros | Devuelve |
|---|---|---|
| `buscar_conductores` | `q` | lista typeahead (id, nombre, régimen) |
| `listar_conductores` | — | grilla con saldo del periodo vigente |
| `obtener_periodos` | `id_conductor` | periodos del conductor + saldo y estado de cada uno |
| `periodos_programables` | `id_conductor` | periodos que HOY se pueden tocar (regla §6.4) |
| `obtener_tramos` | `id_periodo` | tramos ACTIVOS del periodo |
| `calendario_ocupacion` | `desde`, `hasta` | `[{fecha, ocupados, conductores[]}]` para pintar el calendario y resaltar días llenos |

### Escritura (POST, transaccional)
| action | Parámetros | Efecto |
|---|---|---|
| `sincronizar_conductores` | — | *upsert* desde `mp_maes_personal` (codi_carg=6) |
| `generar_periodo` | `id_conductor`, `id_periodo_cat` | crea instancia de periodo (valida ventana §6.4) |
| `guardar_programacion` | `id_periodo`, `tramos[]` | alta de tramos (valida saldo + tope) |
| `reprogramar` | `id_periodo`, `tramos[]` | reprogramación en cadena (§6.2) |
| `anular_tramo` | `id_tramo` | libera tramo, devuelve días |

`tramos[]` = `[{fecha_inicio, fecha_fin}, ...]`.

---

## 6. Lógica de negocio (backend)

### 6.1 Motor del tope de flota (máx. 4 ausencias/día)
`validarCupoFlota($pdo, $fechas[], $idConductor, $tope)`:
```sql
SELECT fecha, COUNT(DISTINCT id_conductor) AS ocupados
FROM mp_vaca_dia
WHERE estado='ACTIVO'
  AND id_conductor <> :idConductor      -- excluye al propio (se libera en la misma operación)
  AND fecha IN (:fechas...)
GROUP BY fecha;
```
Para cada fecha candidata: si `ocupados + 1 > tope` → conflicto. Se **acumulan TODAS** las fechas en conflicto y se devuelven juntas para una **alerta visual restrictiva** que **bloquea el guardado** e indica exactamente qué días saturan la flota.

### 6.2 Reprogramación en cadena (atómica, un solo flujo)
`reprogramar(id_periodo, tramos_nuevos[])` en **una transacción**:
1. `BEGIN`; `SELECT ... FOR UPDATE` de tramos/días ACTIVOS del periodo.
2. **Liberar**: marcar tramos viejos `REEMPLAZADO` y **eliminar** sus `mp_vaca_dia` → días vuelven al saldo automáticamente.
3. **Validar saldo**: `SUM(dias_nuevos) ≤ 30` (si > 30 → ROLLBACK con error).
4. **Validar tope de flota** sobre el set nuevo (con el conductor ya liberado en el paso 2).
5. **Validar auto-solape**: el conductor no puede quedar ausente dos veces el mismo día (otro periodo/tramo).
6. Insertar tramos nuevos (con `id_tramo_origen` → cadena infinita), explotar sus días, escribir `mp_vaca_historial`.
7. Recalcular `estado` del periodo (INCOMPLETO/COMPLETO).
8. `COMMIT`; devolver `{success, saldo_nuevo, dias_liberados, dias_consumidos, estado_periodo}`. Cualquier fallo → `ROLLBACK` (nada queda a medias). La modificación de tramos ya reprogramados se permite **infinitas veces**.

### 6.3 Validación de los 30 días obligatorios
Tras cada guardado: `saldo = 30 − SUM(dias ACTIVOS)`.
- `saldo < 0` → **bloqueo** (no debió pasar §6.2.3; salvaguarda).
- `saldo > 0` → periodo `INCOMPLETO` + **aviso**: "Faltan {saldo} días por programar; debe completar 30."
- `saldo = 0` → periodo `COMPLETO`.

### 6.4 Ventana de periodos programables (actual + 1 futuro)
- **Periodo vigente** = entrada de `mp_vaca_periodo_cat` cuyo rango `[anio_inicio, anio_fin]` corresponde al año en curso (por `orden`).
- Para un conductor solo se permiten abrir instancias del **periodo vigente** y del **siguiente** (`orden + 1`). Crear un periodo con `orden ≥ vigente.orden + 2` se **bloquea** ("aún no corresponde programar ese periodo").
- **Guarda por aniversario**: un periodo futuro solo se habilita si el conductor ya cumplió al menos un año desde `fecha_ingreso` (evita pre-programar a alguien con < 1 año). En la flota actual (veteranos) rara vez aplica, pero se valida.

---

## 7. Páginas del módulo
```
vacaciones_listado.php      -- grilla de conductores: nombre, cargo, régimen, periodo, saldo, estado
vacaciones_calendario.php   -- calendario dinámico de concurrencia (reemplaza la "sábana" del Excel)
vacaciones_registro.php     -- alta/reprogramación de tramos de un conductor (typeahead + selección de periodo)
vacaciones_detalle.php      -- historial de cambios (mp_vaca_historial) por conductor/periodo
vacaciones_reporte.php      -- reporte formato Excel (§8): una fila por tramo, con filtros + exportación .xls e impresión
vacaciones_importar.php     -- carga masiva desde Excel (.xlsx/.csv o pegar tabla): previsualiza y confirma
vacaciones_controller.php   -- API JSON
DB_SCHEMA_VACACIONES.sql    -- DDL de las 7 tablas/objetos + alta de menú/permisos (Fase 5)
```
Más alta de menú/submenú en `mp_admi_menu` / `mp_admi_subm` colgando de **Abastecimiento**, con su `iden_subm` asignado a los roles de RR.HH. en `mp_admi_role_subm`.

---

## 8. Formato de reporte / grilla (igual que el Excel origen)

| APELLIDOS Y NOMBRES | CARGO | REGIMEN | PERIODO | FECHA DE INICIO | FECHA DE FIN | TOTAL VAC. |
|---|---|---|---|---|---|---|
| CORIMANYA LAZARO, JOSE LUIS JUAN | ASISTENTE ADMINISTRATIVO(CONDUCTOR) | DL.728 | 2024-2025 | 01/01/2026 | 30/01/2026 | 30 |
| ARTEAGA PALACIOS, VICTOR HUGO | ASISTENTE ADMINISTRATIVO(CONDUCTOR) | CAS | 2025-2026 | 01/12/2026 | 30/12/2026 | 30 |

- Cada **fila = un tramo** (`mp_vaca_tramo`). Un conductor con vacaciones fraccionadas aparece en varias filas.
- "CARGO" es texto fijo "ASISTENTE ADMINISTRATIVO(CONDUCTOR)".
- "TOTAL VAC." = `dias` del tramo (los del periodo deben sumar 30).
- Fechas mostradas `dd/mm/yyyy`.

---

## 9. Plan de implementación por fases

Cada fase es **desplegable y probable de forma independiente**. Se construye sobre la anterior.

### Fase 0 — Base de datos y catálogos *(fundación)*
**Objetivo:** dejar lista la estructura de datos.
**Entregables:**
- `DB_SCHEMA_VACACIONES.sql` con las 6 tablas (§4.1–4.6).
- Seed de `mp_vaca_periodo_cat`: `2024-2025`, `2025-2026`, `2026-2027` (con `anio_inicio`, `anio_fin`, `orden`).
- Filas de configuración en `mp_admi_conf`: `VACA_TOPE_FLOTA=4`, `VACA_DIAS_PERIODO=30`.
**Criterio de aceptación:** las tablas se crean sin error en la BD; el catálogo y la config tienen datos.
**Dependencias:** ninguna.

### Fase 1 — Sincronización y consulta de conductores
**Objetivo:** poblar el maestro de conductores y poder verlos.
**Entregables:**
- `vacaciones_controller.php` (esqueleto + lectura de credenciales/PDO/transacciones, estilo `almacen_controller.php`).
- Acciones: `sincronizar_conductores`, `buscar_conductores`, `listar_conductores`.
- `vacaciones_listado.php` (grilla solo lectura: nombre, cargo, régimen, fecha ingreso).
**Criterio de aceptación:** al pulsar "Sincronizar" se cargan ~37 conductores (codi_carg=6) con régimen y fecha de ingreso; el typeahead encuentra por nombre/DNI; re-sincronizar no duplica.
**Dependencias:** Fase 0.

### Fase 2 — Periodos y programación básica (con saldo, sin tope)
**Objetivo:** registrar tramos de vacaciones validando los 30 días.
**Entregables:**
- Acciones: `obtener_periodos`, `periodos_programables` (ventana actual+1 futuro §6.4), `generar_periodo`, `obtener_tramos`, `guardar_programacion`.
- Lógica de saldo y estado del periodo (§6.3): bloqueo si suma > 30; aviso "faltan X" si < 30.
- Explosión de tramos en `mp_vaca_dia`.
- `vacaciones_registro.php` (typeahead conductor → elegir periodo → agregar tramos → guardar).
**Criterio de aceptación:** se puede crear un periodo y sus tramos; el saldo se calcula bien; exceder 30 se bloquea; periodo < 30 queda `INCOMPLETO` con aviso; abrir un 3.er periodo se bloquea.
**Dependencias:** Fase 1.

### Fase 3 — Motor de tope de flota + calendario
**Objetivo:** proteger la capacidad de la flota (máx. 4/día) y visualizarla.
**Entregables:**
- Función `validarCupoFlota` (§6.1) integrada en `guardar_programacion`.
- Acción `calendario_ocupacion` (ocupación diaria + conductores).
- `vacaciones_calendario.php` (calendario dinámico, resalta días llenos; reemplaza la "sábana" del Excel).
- Alerta visual restrictiva que bloquea el guardado listando las fechas en conflicto.
**Criterio de aceptación:** intentar un 5.º conductor el mismo día se bloquea con alerta indicando las fechas; el calendario muestra la concurrencia real por día.
**Dependencias:** Fase 2.

### Fase 4 — Reprogramación en cadena + historial
**Objetivo:** editar programaciones infinitas veces de forma atómica y auditable.
**Entregables:**
- Acciones: `reprogramar` (§6.2, libera días viejos + valida saldo + valida tope + inserta nuevos, todo transaccional), `anular_tramo`.
- Escritura en `mp_vaca_historial` (CREA/REPROGRAMA/ANULA).
- `vacaciones_detalle.php` (historial por conductor/periodo).
**Criterio de aceptación:** reprogramar 15+15 → 15+10+5 libera y reasigna en un solo flujo; el saldo vuelve correcto; el tope se revalida; toda operación queda en el historial; un fallo hace rollback sin dejar datos a medias.
**Dependencias:** Fase 3.

### Fase 5 — Menú, permisos y reporte
**Objetivo:** integrar al sistema y entregar el reporte.
**Entregables:**
- Alta de menú/submenú en `mp_admi_menu` / `mp_admi_subm` colgando de **Abastecimiento**; asignación a roles de RR.HH. en `mp_admi_role_subm`.
- Reporte/grilla con el formato del Excel (§8); exportación (PDF con `classes/TCPDF` o Excel).
**Criterio de aceptación:** RR.HH. ve el módulo en su menú; el reporte reproduce las columnas del Excel (una fila por tramo); la exportación funciona.
**Dependencias:** Fase 4.

---

### Resumen de fases

| Fase | Foco | Archivos principales |
|---|---|---|
| 0 | BD + catálogos | `DB_SCHEMA_VACACIONES.sql` |
| 1 | Conductores | `vacaciones_controller.php`, `vacaciones_listado.php` |
| 2 | Periodos + tramos (saldo) | `vacaciones_controller.php`, `vacaciones_registro.php` |
| 3 | Tope de flota + calendario | `vacaciones_controller.php`, `vacaciones_calendario.php` |
| 4 | Reprogramación + historial | `vacaciones_controller.php`, `vacaciones_detalle.php` |
| 5 | Menú, permisos, reporte | menús + reporte/PDF |
