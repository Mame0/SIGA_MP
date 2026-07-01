-- =====================================================================
--  Esquema de Base de Datos — Módulo de Gestión de Vacaciones de Conductores
--  (Abastecimiento - SIGA MPFN-DF Arequipa)
--  Fase 0: tablas, catálogo de periodos y configuración.
--  Ver diseño completo en DOC_MODULO_VACACIONES.md
--  Idempotente: puede ejecutarse varias veces sin duplicar datos.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1) Maestro de conductores (snapshot sincronizado desde mp_maes_personal)
--    Nota: iden_pers NO es FOREIGN KEY porque mp_maes_personal es MyISAM.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mp_vaca_conductor` (
  `id_conductor`     INT AUTO_INCREMENT PRIMARY KEY,
  `iden_pers`        INT NULL,                           -- ref. lógica a mp_maes_personal.iden_pers (NULL en terceros)
  `ndoc`             CHAR(8) NOT NULL DEFAULT '',
  `appat`            VARCHAR(60) NOT NULL,
  `apmat`            VARCHAR(60) NOT NULL,
  `nombres`          VARCHAR(100) NOT NULL,
  `regimen`          VARCHAR(40) NOT NULL DEFAULT '',    -- DL.728 / CAS / TERCEROS (de mp_maes_regimen_laboral)
  `fecha_ingreso`    DATE NOT NULL,
  `dias_por_periodo` INT NOT NULL DEFAULT 30,
  `es_tercero`       TINYINT NOT NULL DEFAULT 0,         -- 1 = chofer tercero (no está en mp_maes_personal)
  `estado`           TINYINT NOT NULL DEFAULT 1,
  `fecha_reg`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_iden_pers` (`iden_pers`),               -- permite múltiples NULL (terceros)
  KEY `idx_ndoc` (`ndoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migración idempotente para instalaciones que ya crearon la tabla antes de los terceros:
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mp_vaca_conductor' AND COLUMN_NAME = 'es_tercero');
SET @sql := IF(@col = 0,
  'ALTER TABLE `mp_vaca_conductor` ADD COLUMN `es_tercero` TINYINT NOT NULL DEFAULT 0 AFTER `dias_por_periodo`',
  'SELECT 1');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

SET @nul := (SELECT IS_NULLABLE FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mp_vaca_conductor' AND COLUMN_NAME = 'iden_pers');
SET @sql := IF(@nul = 'NO',
  'ALTER TABLE `mp_vaca_conductor` MODIFY COLUMN `iden_pers` INT NULL',
  'SELECT 1');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

-- ---------------------------------------------------------------------
-- 2) Catálogo global de periodos (etiquetas de calendario fijas)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mp_vaca_periodo_cat` (
  `id_periodo_cat` INT AUTO_INCREMENT PRIMARY KEY,
  `etiqueta`       VARCHAR(9) NOT NULL,                  -- '2024-2025'
  `anio_inicio`    SMALLINT NOT NULL,
  `anio_fin`       SMALLINT NOT NULL,
  `orden`          INT NOT NULL,                         -- secuencia para la ventana actual+1
  `estado`         TINYINT NOT NULL DEFAULT 1,
  UNIQUE KEY `uq_etiqueta` (`etiqueta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3) Instancia de periodo por conductor
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mp_vaca_periodo` (
  `id_periodo`     INT AUTO_INCREMENT PRIMARY KEY,
  `id_conductor`   INT NOT NULL,
  `id_periodo_cat` INT NOT NULL,
  `etiqueta`       VARCHAR(9) NOT NULL,                  -- denormalizado para reporte
  `dias_asignados` INT NOT NULL DEFAULT 30,
  `estado`         ENUM('INCOMPLETO','COMPLETO','CERRADO') NOT NULL DEFAULT 'INCOMPLETO',
  `fecha_reg`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_cond_periodo` (`id_conductor`, `id_periodo_cat`),
  KEY `idx_conductor` (`id_conductor`),
  CONSTRAINT `fk_periodo_conductor` FOREIGN KEY (`id_conductor`)
      REFERENCES `mp_vaca_conductor` (`id_conductor`) ON DELETE CASCADE,
  CONSTRAINT `fk_periodo_cat` FOREIGN KEY (`id_periodo_cat`)
      REFERENCES `mp_vaca_periodo_cat` (`id_periodo_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4) Tramos (bloques editables de vacaciones)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mp_vaca_tramo` (
  `id_tramo`        INT AUTO_INCREMENT PRIMARY KEY,
  `id_periodo`      INT NOT NULL,
  `id_conductor`    INT NOT NULL,                        -- denormalizado para concurrencia
  `fecha_inicio`    DATE NOT NULL,
  `fecha_fin`       DATE NOT NULL,
  `dias`            INT NOT NULL,                        -- DATEDIFF(fecha_fin,fecha_inicio)+1
  `estado`          ENUM('ACTIVO','REEMPLAZADO','ANULADO') NOT NULL DEFAULT 'ACTIVO',
  `id_tramo_origen` INT DEFAULT NULL,                    -- encadena reprogramaciones sucesivas
  `fecha_reg`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `id_oper_reg`     INT NOT NULL,
  KEY `idx_periodo_estado` (`id_periodo`, `estado`),
  KEY `idx_conductor` (`id_conductor`),
  CONSTRAINT `fk_tramo_periodo` FOREIGN KEY (`id_periodo`)
      REFERENCES `mp_vaca_periodo` (`id_periodo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5) Grano diario (motor del calendario y del tope de flota)
--    Solo contiene días ACTIVOS; en reprogramación se eliminan los viejos.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mp_vaca_dia` (
  `id_dia`       INT AUTO_INCREMENT PRIMARY KEY,
  `id_tramo`     INT NOT NULL,
  `id_conductor` INT NOT NULL,
  `id_periodo`   INT NOT NULL,
  `fecha`        DATE NOT NULL,
  `estado`       ENUM('ACTIVO') NOT NULL DEFAULT 'ACTIVO',
  KEY `idx_fecha` (`fecha`),
  KEY `idx_conductor_fecha` (`id_conductor`, `fecha`),
  KEY `idx_tramo` (`id_tramo`),
  CONSTRAINT `fk_dia_tramo` FOREIGN KEY (`id_tramo`)
      REFERENCES `mp_vaca_tramo` (`id_tramo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6) Historial / auditoría de cambios
--    Sin FK a conductor/periodo: la auditoría debe sobrevivir a borrados.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mp_vaca_historial` (
  `id_hist`          INT AUTO_INCREMENT PRIMARY KEY,
  `id_conductor`     INT NOT NULL,
  `id_periodo`       INT NOT NULL,
  `accion`           ENUM('CREA','REPROGRAMA','ANULA') NOT NULL,
  `detalle_antes`    TEXT DEFAULT NULL,                  -- JSON de tramos previos
  `detalle_despues`  TEXT DEFAULT NULL,                  -- JSON de tramos nuevos
  `dias_liberados`   INT NOT NULL DEFAULT 0,
  `dias_consumidos`  INT NOT NULL DEFAULT 0,
  `saldo_resultante` INT NOT NULL,
  `id_oper`          INT NOT NULL,
  `fecha_hora`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_conductor` (`id_conductor`),
  KEY `idx_periodo` (`id_periodo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  SEED: catálogo de periodos
-- =====================================================================
INSERT INTO `mp_vaca_periodo_cat` (`etiqueta`, `anio_inicio`, `anio_fin`, `orden`, `estado`) VALUES
  ('2024-2025', 2024, 2025, 1, 1),
  ('2025-2026', 2025, 2026, 2, 1),
  ('2026-2027', 2026, 2027, 3, 1)
ON DUPLICATE KEY UPDATE
  `anio_inicio`=VALUES(`anio_inicio`),
  `anio_fin`=VALUES(`anio_fin`),
  `orden`=VALUES(`orden`),
  `estado`=VALUES(`estado`);

-- =====================================================================
--  SEED: configuración en mp_admi_conf (se carga en $_SESSION al login)
--  nomb_conf no es único, por eso se usa INSERT condicional idempotente.
-- =====================================================================
INSERT INTO `mp_admi_conf` (`nomb_conf`, `desc_conf`, `valo_conf`)
SELECT 'VACA_TOPE_FLOTA', 'Maximo de conductores de vacaciones simultaneos por dia', '4'
WHERE NOT EXISTS (SELECT 1 FROM `mp_admi_conf` WHERE `nomb_conf` = 'VACA_TOPE_FLOTA');

INSERT INTO `mp_admi_conf` (`nomb_conf`, `desc_conf`, `valo_conf`)
SELECT 'VACA_DIAS_PERIODO', 'Dias de vacaciones asignados por periodo', '30'
WHERE NOT EXISTS (SELECT 1 FROM `mp_admi_conf` WHERE `nomb_conf` = 'VACA_DIAS_PERIODO');

-- =====================================================================
--  FASE 5 — Integración al menú (Abastecimiento) y permisos
--  El nodo "Abastecimiento" (mp_admi_subm, nomb='CONST_SUBM_ABASTECIMIENTO',
--  iden_padr=0, iden_menu=1; típicamente iden_subm=83) se deriva por nombre.
--  Se cuelga de él un grupo "Vacaciones Conductores" con 5 páginas.
--  nomb_subm en texto plano (no 'CONST_') para no depender de los
--  archivos de idioma: home.php solo traduce los que empiezan con 'CONST_'.
--  Todo idempotente: puede ejecutarse varias veces sin duplicar.
-- =====================================================================

-- id del nodo "Abastecimiento" (derivado por nombre; fallback a 83)
SET @abast := (SELECT `iden_subm` FROM `mp_admi_subm`
               WHERE `nomb_subm`='CONST_SUBM_ABASTECIMIENTO' AND `iden_padr`=0
               ORDER BY `iden_subm` LIMIT 1);
SET @abast := COALESCE(@abast, 83);

-- 1) Grupo contenedor colgando de Abastecimiento
INSERT INTO `mp_admi_subm` (`iden_menu`,`nomb_subm`,`icon_subm`,`page_subm`,`iden_padr`,`orde_subm`,`esta_subm`)
SELECT 1,'Vacaciones Conductores','calendar','',@abast,7,1
WHERE NOT EXISTS (SELECT 1 FROM `mp_admi_subm` WHERE `nomb_subm`='Vacaciones Conductores' AND `iden_padr`=@abast);

-- id del grupo recién creado (o existente)
SET @vaca_grp := (SELECT `iden_subm` FROM `mp_admi_subm`
                  WHERE `nomb_subm`='Vacaciones Conductores' AND `iden_padr`=@abast
                  ORDER BY `iden_subm` LIMIT 1);

-- 2) Páginas del módulo como hijos del grupo (idempotentes por page_subm)
INSERT INTO `mp_admi_subm` (`iden_menu`,`nomb_subm`,`icon_subm`,`page_subm`,`iden_padr`,`orde_subm`,`esta_subm`)
SELECT 1,'Listado de conductores','users','vacaciones_listado.php',@vaca_grp,1,1
WHERE NOT EXISTS (SELECT 1 FROM `mp_admi_subm` WHERE `page_subm`='vacaciones_listado.php');

INSERT INTO `mp_admi_subm` (`iden_menu`,`nomb_subm`,`icon_subm`,`page_subm`,`iden_padr`,`orde_subm`,`esta_subm`)
SELECT 1,'Programación de vacaciones','calendar','vacaciones_registro.php',@vaca_grp,2,1
WHERE NOT EXISTS (SELECT 1 FROM `mp_admi_subm` WHERE `page_subm`='vacaciones_registro.php');

INSERT INTO `mp_admi_subm` (`iden_menu`,`nomb_subm`,`icon_subm`,`page_subm`,`iden_padr`,`orde_subm`,`esta_subm`)
SELECT 1,'Calendario de flota','grid','vacaciones_calendario.php',@vaca_grp,3,1
WHERE NOT EXISTS (SELECT 1 FROM `mp_admi_subm` WHERE `page_subm`='vacaciones_calendario.php');

INSERT INTO `mp_admi_subm` (`iden_menu`,`nomb_subm`,`icon_subm`,`page_subm`,`iden_padr`,`orde_subm`,`esta_subm`)
SELECT 1,'Historial de cambios','clock','vacaciones_detalle.php',@vaca_grp,4,1
WHERE NOT EXISTS (SELECT 1 FROM `mp_admi_subm` WHERE `page_subm`='vacaciones_detalle.php');

INSERT INTO `mp_admi_subm` (`iden_menu`,`nomb_subm`,`icon_subm`,`page_subm`,`iden_padr`,`orde_subm`,`esta_subm`)
SELECT 1,'Reporte de vacaciones','file-text','vacaciones_reporte.php',@vaca_grp,5,1
WHERE NOT EXISTS (SELECT 1 FROM `mp_admi_subm` WHERE `page_subm`='vacaciones_reporte.php');

INSERT INTO `mp_admi_subm` (`iden_menu`,`nomb_subm`,`icon_subm`,`page_subm`,`iden_padr`,`orde_subm`,`esta_subm`)
SELECT 1,'Importar desde Excel','upload','vacaciones_importar.php',@vaca_grp,6,1
WHERE NOT EXISTS (SELECT 1 FROM `mp_admi_subm` WHERE `page_subm`='vacaciones_importar.php');

-- =====================================================================
--  PERMISOS (mp_admi_role_subm): otorga la ruta completa
--  (nodo Abastecimiento + grupo + sus 5 páginas) al rol indicado.
--  Para que el grupo aparezca, el rol debe tener también el nodo padre.
--  Repite el bloque cambiando @rol para cada rol que deba ver el módulo.
-- =====================================================================

-- >>> Rol Admin (2) — siempre administra el sistema
SET @rol := 2;
INSERT INTO `mp_admi_role_subm` (`iden_role`,`iden_subm`,`esta_perm`,`digi_perm`,`fdig_perm`)
SELECT @rol, s.`iden_subm`, 1, 0, DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
FROM `mp_admi_subm` s
WHERE (s.`iden_subm`=@abast OR s.`iden_subm`=@vaca_grp OR s.`iden_padr`=@vaca_grp)
  AND NOT EXISTS (SELECT 1 FROM `mp_admi_role_subm` r
                  WHERE r.`iden_role`=@rol AND r.`iden_subm`=s.`iden_subm`);

-- >>> Rol adicional de RR.HH. (encargada de vacaciones)
--     Descomenta y reemplaza 9 por el iden_role correcto
--     (p. ej. 9 = Planillas, 25 = Información Personal - Admin, 8 = Asistencias).
--     Para ver los roles:  SELECT iden_role, nomb_role FROM mp_admi_role ORDER BY iden_role;
-- SET @rol := 9;
-- INSERT INTO `mp_admi_role_subm` (`iden_role`,`iden_subm`,`esta_perm`,`digi_perm`,`fdig_perm`)
-- SELECT @rol, s.`iden_subm`, 1, 0, DATE_FORMAT(NOW(),'%Y%m%d%H%i%s')
-- FROM `mp_admi_subm` s
-- WHERE (s.`iden_subm`=@abast OR s.`iden_subm`=@vaca_grp OR s.`iden_padr`=@vaca_grp)
--   AND NOT EXISTS (SELECT 1 FROM `mp_admi_role_subm` r
--                   WHERE r.`iden_role`=@rol AND r.`iden_subm`=s.`iden_subm`);
