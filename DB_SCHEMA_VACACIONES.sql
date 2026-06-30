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
  `iden_pers`        INT NOT NULL,                       -- ref. lógica a mp_maes_personal.iden_pers
  `ndoc`             CHAR(8) NOT NULL,
  `appat`            VARCHAR(60) NOT NULL,
  `apmat`            VARCHAR(60) NOT NULL,
  `nombres`          VARCHAR(100) NOT NULL,
  `regimen`          VARCHAR(40) NOT NULL DEFAULT '',    -- DL.728 / CAS (de mp_maes_regimen_laboral)
  `fecha_ingreso`    DATE NOT NULL,
  `dias_por_periodo` INT NOT NULL DEFAULT 30,
  `estado`           TINYINT NOT NULL DEFAULT 1,
  `fecha_reg`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_iden_pers` (`iden_pers`),
  KEY `idx_ndoc` (`ndoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
