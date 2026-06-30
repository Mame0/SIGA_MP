-- ============================================================
-- Crear tabla mp_visitantes (visitantes externos tipo 4)
-- ============================================================
CREATE TABLE IF NOT EXISTS `mp_visitantes` (
  `codi_visi` INT(11) NOT NULL AUTO_INCREMENT,
  `ndoc_visi` VARCHAR(20) NOT NULL,
  `appa_visi` VARCHAR(60) NOT NULL DEFAULT '',
  `apma_visi` VARCHAR(60) NOT NULL DEFAULT '',
  `nomb_visi` VARCHAR(100) NOT NULL DEFAULT '',
  `codi_depe` INT(11) NOT NULL DEFAULT 0,
  `codi_pers` INT(11) NOT NULL DEFAULT 0,
  `esta_visi` TINYINT(1) NOT NULL DEFAULT 1,
  `fdig_visi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`codi_visi`),
  UNIQUE KEY `ndoc_visi` (`ndoc_visi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
