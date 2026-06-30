-- Esquema de Base de Datos para el Módulo de Almacén Valorizado (Kardex)

CREATE TABLE IF NOT EXISTS `mp_almacen_locales` (
  `id_almacen` INT AUTO_INCREMENT PRIMARY KEY,
  `nomb_almacen` VARCHAR(100) NOT NULL,
  `ubig_almacen` VARCHAR(150) DEFAULT NULL,
  `esta_almacen` TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mp_almacen_bienes` (
  `id_bien` INT AUTO_INCREMENT PRIMARY KEY,
  `codi_bien` VARCHAR(50) DEFAULT NULL,
  `desc_bien` VARCHAR(255) NOT NULL,
  `unid_bien` VARCHAR(50) DEFAULT NULL,
  `marc_bien` VARCHAR(100) DEFAULT NULL,
  `esta_bien` TINYINT DEFAULT 1,
  UNIQUE KEY `idx_codi_bien` (`codi_bien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mp_almacen_inventario` (
  `id_almacen` INT NOT NULL,
  `id_bien` INT NOT NULL,
  `stock_actual` INT DEFAULT 0,
  `pu_actual` DECIMAL(12,4) DEFAULT 0.0000,
  `total_actual` DECIMAL(12,4) DEFAULT 0.0000,
  PRIMARY KEY (`id_almacen`, `id_bien`),
  FOREIGN KEY (`id_almacen`) REFERENCES `mp_almacen_locales` (`id_almacen`) ON DELETE CASCADE,
  FOREIGN KEY (`id_bien`) REFERENCES `mp_almacen_bienes` (`id_bien`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mp_almacen_movimientos` (
  `id_mov` INT AUTO_INCREMENT PRIMARY KEY,
  `id_almacen` INT NOT NULL,
  `id_bien` INT NOT NULL,
  `tipo_mov` ENUM('INGRESO', 'SALIDA') NOT NULL,
  `fech_mov` DATE NOT NULL,
  `doc_mov` VARCHAR(50) NOT NULL,
  `cant_mov` INT NOT NULL,
  `pu_mov` DECIMAL(12,4) NOT NULL,
  `total_mov` DECIMAL(12,4) NOT NULL,
  `fech_cadu` DATE DEFAULT NULL,
  `obse_mov` TEXT DEFAULT NULL,
  `fech_reg` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_almacen`) REFERENCES `mp_almacen_locales` (`id_almacen`) ON DELETE CASCADE,
  FOREIGN KEY (`id_bien`) REFERENCES `mp_almacen_bienes` (`id_bien`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar almacén inicial (Unificado)
INSERT INTO `mp_almacen_locales` (`id_almacen`, `nomb_almacen`, `ubig_almacen`, `esta_almacen`)
VALUES 
(1, 'Almacén Principal', 'Sede Central Arequipa', 1)
ON DUPLICATE KEY UPDATE `nomb_almacen`=VALUES(`nomb_almacen`), `ubig_almacen`=VALUES(`ubig_almacen`), `esta_almacen`=VALUES(`esta_almacen`);
