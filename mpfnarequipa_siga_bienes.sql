-- --------------------------------------------------------
-- Host:                         localhost
-- Versión del servidor:         10.4.22-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_oper
CREATE TABLE IF NOT EXISTS `mp_admi_oper` (
  `iden_oper` int(6) NOT NULL AUTO_INCREMENT,
  `logi_oper` char(16) NOT NULL,
  `pass_oper` char(41) NOT NULL,
  `ndoc_oper` char(8) NOT NULL,
  `appa_oper` varchar(50) NOT NULL,
  `apma_oper` varchar(50) NOT NULL,
  `nomb_oper` varchar(100) NOT NULL,
  `carg_oper` varchar(100) NOT NULL,
  `depe_oper` varchar(200) NOT NULL,
  `celu_oper` varchar(20) NOT NULL,
  `mail_oper` varchar(100) NOT NULL,
  `codi_depe` int(6) NOT NULL,
  `codi_perf` int(3) NOT NULL,
  `flag_band` int(1) NOT NULL,
  `esta_oper` int(1) NOT NULL,
  `fexp_oper` char(8) NOT NULL,
  `digi_oper` int(6) NOT NULL,
  `fdig_oper` char(14) NOT NULL,
  `rese_oper` int(1) NOT NULL,
  PRIMARY KEY (`iden_oper`)
) ENGINE=InnoDB AUTO_INCREMENT=1102 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers
CREATE TABLE IF NOT EXISTS `mp_admi_pers` (
  `iden_pers` int(6) NOT NULL AUTO_INCREMENT,
  `iden_tdoc` int(2) NOT NULL,
  `ndoc_pers` char(12) NOT NULL,
  `iden_sexo` int(1) NOT NULL,
  `appa_pers` varchar(50) NOT NULL,
  `apma_pers` varchar(50) NOT NULL,
  `nomb_pers` varchar(50) NOT NULL,
  `iden_eciv` int(2) NOT NULL,
  `nruc_pers` char(12) NOT NULL,
  `iden_nafp` int(2) NOT NULL,
  `cusp_pers` char(20) NOT NULL,
  `fnac_pers` char(8) NOT NULL,
  `iden_pais` int(3) NOT NULL,
  `lnac_pers` char(6) NOT NULL,
  `cins_pers` char(12) NOT NULL,
  `cper_pers` char(12) NOT NULL,
  `eins_pers` varchar(50) NOT NULL,
  `eper_pers` varchar(50) NOT NULL,
  `domi_pers` char(6) NOT NULL,
  `iden_tvia` int(3) NOT NULL,
  `dnro_pers` char(4) NOT NULL,
  `dire_pers` varchar(100) NOT NULL,
  `dint_pers` char(4) NOT NULL,
  `dpis_pers` int(2) NOT NULL,
  `dlot_pers` char(3) NOT NULL,
  `dman_pers` char(2) NOT NULL,
  `dref_pers` varchar(50) NOT NULL,
  `iden_tdom` int(3) NOT NULL,
  `iden_depe` int(3) NOT NULL,
  `iden_rlab` int(2) NOT NULL,
  `iden_carg` int(2) NOT NULL,
  `fing_pers` char(8) NOT NULL,
  `iden_pres` int(1) NOT NULL,
  `iden_modtrab` int(1) NOT NULL,
  `iden_sind` int(1) NOT NULL,
  `essa_pers` int(1) NOT NULL,
  `iden_poli` int(3) NOT NULL,
  `teps_pers` int(1) NOT NULL,
  `iden_etni` int(2) NOT NULL,
  `iden_leng` int(2) NOT NULL,
  `olen_pers` char(20) NOT NULL,
  `cona_pers` int(1) NOT NULL,
  `rcon_pers` char(15) NOT NULL,
  `carcon_pers` char(15) NOT NULL,
  `iden_disc` int(2) NOT NULL,
  `iden_ffaa` int(2) NOT NULL,
  `iden_depo` int(2) NOT NULL,
  `iden_tsan` int(2) NOT NULL,
  `aler_pers` varchar(100) NOT NULL,
  `enfe_pers` varchar(100) NOT NULL,
  `iden_nedu` int(2) NOT NULL,
  `esta_nedu` int(1) NOT NULL,
  `inst_nedu` int(4) NOT NULL,
  `afin_nedu` char(4) NOT NULL,
  `hobb_pers` varchar(100) NOT NULL,
  `idio_pers` varchar(100) NOT NULL,
  `carg_plan` int(3) NOT NULL,
  `meta_plan` int(3) NOT NULL,
  `afam_plan` int(1) NOT NULL,
  `teps_plan` int(1) NOT NULL,
  `diip_pers` char(15) NOT NULL,
  `acti_pers` int(1) NOT NULL,
  `digi_pers` int(4) NOT NULL,
  `fdig_pers` char(14) NOT NULL,
  `esta_pers` int(1) NOT NULL,
  PRIMARY KEY (`iden_pers`)
) ENGINE=MyISAM AUTO_INCREMENT=1365 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_ubig_reni
CREATE TABLE IF NOT EXISTS `mp_admi_ubig_reni` (
  `cdep` char(2) NOT NULL,
  `cpro` char(2) NOT NULL,
  `cdis` char(2) NOT NULL,
  `depa` varchar(50) NOT NULL,
  `prov` varchar(50) NOT NULL,
  `dist` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cpbi_auditoria
CREATE TABLE IF NOT EXISTS `mp_cpbi_auditoria` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `codi_bien` int(9) NOT NULL,
  `fecha_movimiento` datetime NOT NULL,
  `tipo_movimiento` varchar(50) NOT NULL COMMENT 'RECEPCION_ALMACEN, TRASLADO, DEVOLUCION, REINGRESO, etc.',
  `responsable_entrega_nombre` varchar(255) DEFAULT NULL,
  `responsable_entrega_dni` varchar(15) DEFAULT NULL,
  `responsable_recepcion_nombre` varchar(255) DEFAULT NULL,
  `responsable_recepcion_dni` varchar(15) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `id_operador` int(6) NOT NULL COMMENT 'ID del usuario del sistema que registra el movimiento',
  `anaquel` varchar(50) DEFAULT NULL,
  `nivel` varchar(50) DEFAULT NULL,
  `caja` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_auditoria`),
  KEY `idx_codi_bien` (`codi_bien`),
  CONSTRAINT `fk_auditoria_bien` FOREIGN KEY (`codi_bien`) REFERENCES `mp_cpbi_bienes` (`codi_bien`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cpbi_bienes
CREATE TABLE IF NOT EXISTS `mp_cpbi_bienes` (
  `codi_bien` int(9) NOT NULL AUTO_INCREMENT,
  `nume_regi` char(20) CHARACTER SET utf8 NOT NULL,
  `desc_bien` text NOT NULL,
  `marc_bien` varchar(50) NOT NULL,
  `seri_bien` varchar(50) NOT NULL,
  `tplg_bien` varchar(20) NOT NULL,
  `id_tipo_bien` int(11) DEFAULT NULL,
  `nume_carp` varchar(60) NOT NULL,
  `codi_deli` int(3) NOT NULL,
  `codi_fisc` int(6) NOT NULL,
  `codi_esta` int(3) NOT NULL,
  `codi_epro` int(3) NOT NULL,
  `fech_inte` date NOT NULL,
  `digi_oper_id` int(6) DEFAULT NULL,
  `agraviante` char(30) CHARACTER SET utf8 NOT NULL,
  `agraviado` char(30) CHARACTER SET utf8 NOT NULL,
  `lugar_origen_incautacion` varchar(255) DEFAULT NULL,
  `descripcion_embalaje` varchar(255) DEFAULT NULL,
  `perecible` tinyint(1) NOT NULL DEFAULT 0,
  `naturaleza_bien` varchar(100) DEFAULT NULL,
  `drogas_tipo` varchar(100) DEFAULT NULL,
  `ruta_archivo_digital` varchar(255) DEFAULT NULL,
  `id_ubig_provincia` char(6) DEFAULT NULL,
  `id_ubig_distrito` char(6) DEFAULT NULL,
  PRIMARY KEY (`codi_bien`),
  KEY `nume_regi` (`nume_regi`),
  KEY `fk_cpbi_bienes_maes_cpbi_tipos` (`id_tipo_bien`),
  KEY `fk_cpbi_bienes_maes_cpbi_estado` (`codi_esta`),
  KEY `fk_cpbi_bienes_maes_cpbi_estado_proceso` (`codi_epro`),
  CONSTRAINT `fk_cpbi_bienes_maes_cpbi_estado` FOREIGN KEY (`codi_esta`) REFERENCES `mp_maes_cpbi_estado` (`n_codigo`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cpbi_bienes_maes_cpbi_estado_proceso` FOREIGN KEY (`codi_epro`) REFERENCES `mp_maes_cpbi_estado_proceso` (`n_codigo`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cpbi_bienes_maes_cpbi_tipos` FOREIGN KEY (`id_tipo_bien`) REFERENCES `mp_maes_cpbi_tipos` (`n_codigo`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cpbi_bienes_movimiento
CREATE TABLE IF NOT EXISTS `mp_cpbi_bienes_movimiento` (
  `codi_movi` int(9) NOT NULL AUTO_INCREMENT,
  `codi_bien` int(9) NOT NULL,
  `codi_tmov` int(3) NOT NULL,
  `disp_movi` varchar(100) NOT NULL,
  `acta_movi` varchar(50) NOT NULL,
  `fech_movi` char(14) NOT NULL,
  `esta_movi` int(1) NOT NULL,
  `digi_movi` int(6) NOT NULL,
  `fdig_movi` char(14) NOT NULL,
  PRIMARY KEY (`codi_movi`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_cpbi_estado
CREATE TABLE IF NOT EXISTS `mp_maes_cpbi_estado` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_cpbi_estado_proceso
CREATE TABLE IF NOT EXISTS `mp_maes_cpbi_estado_proceso` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_cpbi_tipos
CREATE TABLE IF NOT EXISTS `mp_maes_cpbi_tipos` (
  `n_codigo` int(11) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`n_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_cpbi_tipo_movimiento
CREATE TABLE IF NOT EXISTS `mp_maes_cpbi_tipo_movimiento` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_cpbi_ubicacion
CREATE TABLE IF NOT EXISTS `mp_maes_cpbi_ubicacion` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_delito
CREATE TABLE IF NOT EXISTS `mp_maes_delito` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(70) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=188 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
