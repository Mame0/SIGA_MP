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

-- Volcando estructura para tabla mpfnarequipa_siga.datasgf
CREATE TABLE IF NOT EXISTS `datasgf` (
  `id_fiscal` char(5) NOT NULL,
  `no_fiscal` char(50) NOT NULL,
  `id_unico` char(25) NOT NULL,
  `fe_denuncia` datetime NOT NULL,
  `fe_ing_caso` datetime NOT NULL,
  `fe_asig` datetime NOT NULL,
  `id_etapa` int(11) NOT NULL,
  `de_etapa` char(30) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `de_estado` char(50) NOT NULL,
  `st_acumulado` char(1) NOT NULL,
  `tx_tipo_caso` char(3) NOT NULL,
  `condicion` char(30) NOT NULL,
  `fe_conclusion` datetime NOT NULL,
  `id_depe` int(11) NOT NULL,
  KEY `id_fiscal` (`id_fiscal`),
  KEY `id_unico` (`id_unico`),
  KEY `id_etapa` (`id_etapa`),
  KEY `id_estado` (`id_estado`),
  KEY `fe_asig` (`fe_asig`),
  KEY `fe_conclusion` (`fe_conclusion`),
  KEY `id_depe` (`id_depe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para procedimiento mpfnarequipa_siga.expedienteAddOrEdit
DELIMITER //
CREATE PROCEDURE `expedienteAddOrEdit`(IN `_id_expediente_electronico` INT, IN `_num_expediente` VARCHAR(100), IN `_juzgado` VARCHAR(100), IN `_delito` VARCHAR(150), IN `_demandante` VARCHAR(200), IN `_demandado` VARCHAR(200), IN `_usuario_pj` VARCHAR(45), IN `_fecha_ingreso` VARCHAR(45), IN `_hora_ingreso` VARCHAR(45), IN `_id_estado` INT, IN `_ip_address_sube` VARCHAR(45), IN `_inf_adi` TEXT, IN `_contenido` LONGBLOB, IN `_tamanio` INT, IN `_tipo` VARCHAR(45), IN `_nombre_archivo` VARCHAR(45), IN `_tamanio_unidad` VARCHAR(45), IN `_fecha_ingreso_msiaf` VARCHAR(45), IN `_num_msiaf` VARCHAR(45), IN `_num_caso` VARCHAR(45), IN `_num_fiscalia` VARCHAR(45), IN `_num_fiscalia_int` VARCHAR(45), IN `_fecha_registro_mp` VARCHAR(10), IN `_hora_registro_mp` VARCHAR(10), IN `_fecha_asignado_f` VARCHAR(10), IN `_hora_asignado_f` VARCHAR(10))
BEGIN 
  IF _id_expediente_electronico = 0 THEN
    INSERT INTO expediente_electronico (num_expediente, juzgado, delito, demandante, demandado, usuario_pj, fecha_ingreso, hora_ingreso, id_estado, ip_address_sube, inf_adi, contenido, tamanio, tipo, nombre_archivo, tamanio_unidad, fecha_ingreso_msiaf, num_msiaf, num_caso, num_fiscalia, num_fiscalia_int, fecha_registro_mp, hora_registro_mp, fecha_asignado_f, hora_asignado_f)
    VALUES (_num_expediente, _juzgado, _delito, _demandante, _demandado, _usuario_pj, _fecha_ingreso, _hora_ingreso, _id_estado, _ip_address_sube, _inf_adi, _contenido, _tamanio, _tipo, _nombre_archivo, _tamanio_unidad, _fecha_ingreso_msiaf, _num_msiaf, _num_caso, _num_fiscalia, _num_fiscalia_int, _fecha_registro_mp, _hora_registro_mp, _fecha_asignado_f, _hora_asignado_f);
  SET _id_expediente_electronico = LAST_INSERT_ID();
  ELSE
    UPDATE expediente_electronico
    SET
    num_expediente = _num_expediente,
    juzgado = _juzgado,
    delito = _delito,
    demandante = _demandante,
    demandado = _demandado,
    usuario_pj = _usuario_pj,
    fecha_ingreso = _fecha_ingreso,
    hora_ingreso = _hora_ingreso,
    id_estado = _id_estado,
    ip_address_sube = _ip_address_sube,
    inf_adi = _inf_adi,
    contenido = _contenido,
    tamanio = _tamanio,
    tipo = _tipo,
    nombre_archivo = _nombre_archivo,
    tamanio_unidad = _tamanio_unidad,
    fecha_ingreso_msiaf = _fecha_ingreso_msiaf,
    num_msiaf = _num_msiaf,
    num_caso = _num_caso,
    num_fiscalia = _num_fiscalia,
    num_fiscalia_int = _num_fiscalia_int,
    fecha_registro_mp = _fecha_registro_mp,
    hora_registro_mp = _hora_registro_mp,
    fecha_asignado_f = _fecha_asignado_f,
    hora_asignado_f = _hora_asignado_f
       WHERE id_expediente_electronico = _id_expediente_electronico;
  END IF;
END//
DELIMITER ;

-- Volcando estructura para tabla mpfnarequipa_siga.expediente_electronico
CREATE TABLE IF NOT EXISTS `expediente_electronico` (
  `id_expediente_electronico` int(11) NOT NULL AUTO_INCREMENT,
  `num_expediente` varchar(100) NOT NULL,
  `juzgado` varchar(100) NOT NULL,
  `delito` varchar(150) NOT NULL,
  `demandante` varchar(200) NOT NULL,
  `demandado` varchar(200) NOT NULL,
  `usuario_pj` varchar(45) NOT NULL,
  `fecha_ingreso` varchar(45) NOT NULL,
  `hora_ingreso` varchar(45) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `ip_address_sube` varchar(45) NOT NULL,
  `inf_adi` text NOT NULL,
  `contenido` longblob NOT NULL,
  `tamanio` int(11) NOT NULL,
  `tipo` varchar(45) NOT NULL,
  `nombre_archivo` varchar(45) NOT NULL,
  `tamanio_unidad` varchar(45) NOT NULL,
  `fecha_ingreso_msiaf` varchar(45) NOT NULL,
  `num_msiaf` varchar(45) NOT NULL,
  `num_caso` varchar(45) NOT NULL,
  `num_fiscalia` varchar(45) NOT NULL,
  `num_fiscalia_int` varchar(45) NOT NULL,
  `fecha_registro_mp` varchar(10) NOT NULL,
  `hora_registro_mp` varchar(10) NOT NULL,
  `fecha_asignado_f` varchar(10) NOT NULL,
  `hora_asignado_f` varchar(10) NOT NULL,
  PRIMARY KEY (`id_expediente_electronico`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.ext_usuario
CREATE TABLE IF NOT EXISTS `ext_usuario` (
  `id_ext_usu` int(11) NOT NULL AUTO_INCREMENT,
  `dni` varchar(60) NOT NULL,
  `ap_p` varchar(45) NOT NULL,
  `ap_m` varchar(45) NOT NULL,
  `nombres` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tel_trabajo` varchar(45) NOT NULL,
  `tel_cel` varchar(45) NOT NULL,
  `documen_auto` varchar(45) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `id_ventanilla` varchar(45) NOT NULL,
  `fecha_reg` varchar(45) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `id_ext_cargo` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `id_sexo` int(11) NOT NULL,
  `login` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `foto` text NOT NULL,
  `firma` text NOT NULL,
  `activo` int(1) NOT NULL,
  PRIMARY KEY (`id_ext_usu`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_acce
CREATE TABLE IF NOT EXISTS `mp_admi_acce` (
  `iden_acce` int(6) NOT NULL AUTO_INCREMENT,
  `dire_acce` varchar(30) NOT NULL,
  `iden_oper` int(6) NOT NULL,
  `ndoc_oper` char(12) NOT NULL,
  `diip_acce` char(15) NOT NULL,
  `fdig_acce` char(14) NOT NULL,
  `esta_acce` int(1) NOT NULL,
  PRIMARY KEY (`iden_acce`)
) ENGINE=MyISAM AUTO_INCREMENT=779 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_conf
CREATE TABLE IF NOT EXISTS `mp_admi_conf` (
  `iden_conf` int(3) NOT NULL AUTO_INCREMENT,
  `nomb_conf` varchar(30) NOT NULL,
  `desc_conf` varchar(100) NOT NULL,
  `valo_conf` varchar(100) NOT NULL,
  PRIMARY KEY (`iden_conf`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_depe
CREATE TABLE IF NOT EXISTS `mp_admi_depe` (
  `codi_depe` int(6) NOT NULL AUTO_INCREMENT,
  `nomb_depe` varchar(100) NOT NULL,
  `sigl_depe` char(20) NOT NULL,
  `codi_loca` int(3) NOT NULL,
  `dire_depe` varchar(100) NOT NULL,
  `tipo_depe` int(3) NOT NULL,
  `codi_padr` int(6) NOT NULL,
  `esta_depe` int(1) NOT NULL,
  `digi_depe` int(6) NOT NULL,
  `fdig_depe` char(14) NOT NULL,
  `depe_prin` tinyint(1) NOT NULL,
  `abre_depe` char(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`codi_depe`)
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_depe_backup
CREATE TABLE IF NOT EXISTS `mp_admi_depe_backup` (
  `codi_depe` int(6) NOT NULL DEFAULT 0,
  `nomb_depe` varchar(100) NOT NULL,
  `sigl_depe` char(20) NOT NULL,
  `codi_loca` int(3) NOT NULL,
  `dire_depe` varchar(100) NOT NULL,
  `tipo_depe` int(3) NOT NULL,
  `codi_padr` int(6) NOT NULL,
  `esta_depe` int(1) NOT NULL,
  `digi_depe` int(6) NOT NULL,
  `fdig_depe` char(14) NOT NULL,
  `depe_prin` tinyint(1) NOT NULL,
  `abre_depe` char(100) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_loca
CREATE TABLE IF NOT EXISTS `mp_admi_loca` (
  `codi_loca` int(3) NOT NULL AUTO_INCREMENT,
  `nom1_loca` varchar(100) NOT NULL,
  `nom2_loca` varchar(300) NOT NULL,
  `dire_loca` varchar(200) NOT NULL,
  `ubig_loca` char(6) NOT NULL,
  `lati_loca` varchar(20) NOT NULL,
  `long_loca` varchar(20) NOT NULL,
  `rang_loca` int(3) NOT NULL,
  `esta_loca` int(1) NOT NULL,
  `digi_loca` int(6) NOT NULL,
  `fdig_loca` char(14) NOT NULL,
  PRIMARY KEY (`codi_loca`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_menu
CREATE TABLE IF NOT EXISTS `mp_admi_menu` (
  `iden_menu` int(3) NOT NULL AUTO_INCREMENT,
  `nomb_menu` varchar(50) NOT NULL,
  `icon_menu` char(20) NOT NULL,
  `orde_menu` int(2) NOT NULL,
  `esta_menu` int(1) NOT NULL,
  PRIMARY KEY (`iden_menu`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

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

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_oper_firm
CREATE TABLE IF NOT EXISTS `mp_admi_oper_firm` (
  `iden_oper` int(6) NOT NULL,
  `firm_oper` blob NOT NULL,
  PRIMARY KEY (`iden_oper`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_oper_role
CREATE TABLE IF NOT EXISTS `mp_admi_oper_role` (
  `iden_oper` int(6) NOT NULL,
  `iden_role` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_oper_role_backup
CREATE TABLE IF NOT EXISTS `mp_admi_oper_role_backup` (
  `iden_oper` int(6) NOT NULL,
  `iden_role` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_curs
CREATE TABLE IF NOT EXISTS `mp_admi_pers_curs` (
  `iden_curs` int(6) NOT NULL AUTO_INCREMENT,
  `iden_pers` int(6) NOT NULL,
  `nomb_curs` varchar(50) NOT NULL,
  `iden_inst` int(4) NOT NULL,
  `otro_inst` varchar(100) DEFAULT NULL,
  `nota_curs` int(2) NOT NULL,
  `desd_curs` char(8) NOT NULL,
  `hast_curs` char(8) NOT NULL,
  `nhor_curs` int(3) NOT NULL,
  `digi_curs` int(6) NOT NULL,
  `fdig_curs` char(14) NOT NULL,
  `esta_curs` int(1) NOT NULL,
  PRIMARY KEY (`iden_curs`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_emer
CREATE TABLE IF NOT EXISTS `mp_admi_pers_emer` (
  `iden_emer` int(6) NOT NULL AUTO_INCREMENT,
  `iden_pers` int(6) NOT NULL,
  `appa_emer` varchar(50) NOT NULL,
  `apma_emer` varchar(50) NOT NULL,
  `nomb_emer` varchar(50) NOT NULL,
  `tfij_emer` char(10) NOT NULL,
  `tcel_emer` char(10) NOT NULL,
  `digi_emer` int(6) NOT NULL,
  `fdig_emer` char(14) NOT NULL,
  `esta_emer` int(1) NOT NULL,
  PRIMARY KEY (`iden_emer`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_enfe
CREATE TABLE IF NOT EXISTS `mp_admi_pers_enfe` (
  `iden_pers` int(6) NOT NULL,
  `iden_enfe` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_expe
CREATE TABLE IF NOT EXISTS `mp_admi_pers_expe` (
  `iden_expe` int(6) NOT NULL AUTO_INCREMENT,
  `iden_pers` int(6) NOT NULL,
  `inst_expe` varchar(100) NOT NULL,
  `iden_carg` int(4) NOT NULL,
  `desd_expe` char(8) NOT NULL,
  `hast_expe` char(8) NOT NULL,
  `iden_cond` int(2) NOT NULL,
  `iden_moti` int(2) NOT NULL,
  `digi_expe` int(6) NOT NULL,
  `fdig_expe` char(14) NOT NULL,
  `esta_expe` int(1) NOT NULL,
  PRIMARY KEY (`iden_expe`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_fami
CREATE TABLE IF NOT EXISTS `mp_admi_pers_fami` (
  `iden_fami` int(6) NOT NULL AUTO_INCREMENT,
  `iden_pers` int(6) NOT NULL,
  `iden_tipo` int(2) NOT NULL,
  `appa_fami` varchar(50) NOT NULL,
  `apma_fami` varchar(50) NOT NULL,
  `nomb_fami` varchar(50) NOT NULL,
  `iden_tdoc` int(2) NOT NULL,
  `ndoc_fami` char(12) NOT NULL,
  `iden_sexo` int(1) NOT NULL,
  `fnac_fami` char(8) NOT NULL,
  `vive_fami` int(1) NOT NULL,
  `iden_ocup` int(3) NOT NULL,
  `iden_pais` int(3) NOT NULL,
  `lnac_fami` char(6) NOT NULL,
  `iden_tent` int(2) NOT NULL,
  `iden_regi` int(2) NOT NULL,
  `digi_fami` int(6) NOT NULL,
  `fdig_fami` char(14) NOT NULL,
  `esta_fami` int(1) NOT NULL,
  PRIMARY KEY (`iden_fami`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_grad
CREATE TABLE IF NOT EXISTS `mp_admi_pers_grad` (
  `iden_grad` int(6) NOT NULL AUTO_INCREMENT,
  `iden_pers` int(6) NOT NULL,
  `iden_nive` int(2) NOT NULL,
  `iden_esta` int(2) NOT NULL,
  `iden_espe` int(4) NOT NULL,
  `iden_inst` int(4) NOT NULL,
  `ntit_grad` char(15) NOT NULL,
  `ncol_grad` char(15) NOT NULL,
  `desd_grad` char(8) NOT NULL,
  `hast_grad` char(8) NOT NULL,
  `fech_grad` char(8) NOT NULL,
  `digi_grad` int(6) NOT NULL,
  `fdig_grad` char(14) NOT NULL,
  `esta_grad` int(1) NOT NULL,
  PRIMARY KEY (`iden_grad`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_hobb
CREATE TABLE IF NOT EXISTS `mp_admi_pers_hobb` (
  `iden_pers` int(6) NOT NULL,
  `iden_hobb` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_idio
CREATE TABLE IF NOT EXISTS `mp_admi_pers_idio` (
  `iden_pers` int(6) NOT NULL,
  `iden_idio` int(2) NOT NULL,
  `iden_nive_habl` int(2) NOT NULL COMMENT 'ID del nivel hablado',
  `iden_nive_escr` int(2) NOT NULL COMMENT 'ID del nivel escrito',
  PRIMARY KEY (`iden_pers`,`iden_idio`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_plan_clas
CREATE TABLE IF NOT EXISTS `mp_admi_pers_plan_clas` (
  `iden_pers` int(6) NOT NULL,
  `iden_clas` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_pers_repo
CREATE TABLE IF NOT EXISTS `mp_admi_pers_repo` (
  `iden_repo` int(3) NOT NULL AUTO_INCREMENT,
  `nomb_repo` varchar(50) NOT NULL,
  `codi_loca` int(3) NOT NULL,
  `codi_depe` int(6) NOT NULL,
  `codi_regi` int(3) NOT NULL,
  `codi_carg` int(3) NOT NULL,
  `codi_sexo` int(1) NOT NULL,
  `codi_hijo` int(1) NOT NULL,
  `edad_desd` int(2) NOT NULL,
  `edad_hast` int(2) NOT NULL,
  `sele_colu` char(20) NOT NULL,
  `digi_repo` int(6) NOT NULL,
  `fdig_repo` char(14) NOT NULL,
  `esta_repo` int(1) NOT NULL,
  PRIMARY KEY (`iden_repo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_repo_guardadas
CREATE TABLE IF NOT EXISTS `mp_admi_repo_guardadas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_consulta` varchar(100) NOT NULL,
  `filtros_json` text NOT NULL,
  `usuario_id` int(11) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_modificacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activo` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_name` (`usuario_id`,`nombre_consulta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_role
CREATE TABLE IF NOT EXISTS `mp_admi_role` (
  `iden_role` int(3) NOT NULL AUTO_INCREMENT,
  `nomb_role` varchar(50) NOT NULL,
  `codi_inst` int(3) NOT NULL,
  `esta_role` int(1) NOT NULL,
  PRIMARY KEY (`iden_role`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_role_subm
CREATE TABLE IF NOT EXISTS `mp_admi_role_subm` (
  `iden_role` int(6) NOT NULL,
  `iden_subm` int(6) NOT NULL,
  `esta_perm` int(1) NOT NULL,
  `digi_perm` int(6) NOT NULL,
  `fdig_perm` char(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_admi_subm
CREATE TABLE IF NOT EXISTS `mp_admi_subm` (
  `iden_subm` int(3) NOT NULL AUTO_INCREMENT,
  `iden_menu` int(3) NOT NULL,
  `nomb_subm` varchar(50) NOT NULL,
  `icon_subm` char(20) NOT NULL,
  `page_subm` varchar(100) NOT NULL,
  `iden_padr` int(3) NOT NULL,
  `orde_subm` int(2) NOT NULL,
  `esta_subm` int(1) NOT NULL,
  PRIMARY KEY (`iden_subm`)
) ENGINE=InnoDB AUTO_INCREMENT=268 DEFAULT CHARSET=latin1;

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

-- Volcando estructura para tabla mpfnarequipa_siga.mp_alertas
CREATE TABLE IF NOT EXISTS `mp_alertas` (
  `autogen` int(11) NOT NULL AUTO_INCREMENT,
  `al_dni` char(8) NOT NULL,
  `al_desc` text NOT NULL,
  `al_tpad` tinyint(4) NOT NULL,
  `al_adju` char(40) NOT NULL,
  `al_vali` char(1) NOT NULL,
  `al_resp` text NOT NULL,
  `al_fesu` datetime NOT NULL,
  `al_fere` datetime NOT NULL,
  PRIMARY KEY (`autogen`),
  KEY `al_dni` (`al_dni`),
  KEY `al_vali` (`al_vali`),
  KEY `al_tpad` (`al_tpad`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_asistencia
CREATE TABLE IF NOT EXISTS `mp_asistencia` (
  `dni` char(8) NOT NULL,
  `fecha` date NOT NULL,
  `horaentrada` time NOT NULL,
  `horasalida` time NOT NULL,
  `horamarcaing` time NOT NULL,
  `horamarcasal` time NOT NULL,
  `horasextra` int(2) NOT NULL,
  `minutosextra` int(2) NOT NULL,
  `horastrabajadas` char(6) NOT NULL,
  `horasremotas` char(6) NOT NULL,
  `horastotales` char(6) NOT NULL,
  `licencia_descripcion` char(20) NOT NULL,
  `licencia_resolucion` char(25) NOT NULL,
  KEY `dni` (`dni`),
  KEY `fecha` (`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_asistencia_feccompensables
CREATE TABLE IF NOT EXISTS `mp_asistencia_feccompensables` (
  `autogen` int(11) NOT NULL AUTO_INCREMENT,
  `fechacompensable` date NOT NULL,
  `fechacompensablehasta` date NOT NULL,
  `cantdias` int(3) NOT NULL,
  `canthoras` int(2) NOT NULL,
  `descripcionfecha` char(50) NOT NULL,
  `fechainicialcompensa` date NOT NULL,
  `fechafinalcompensa` date NOT NULL,
  PRIMARY KEY (`autogen`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_asis_marcaciones
CREATE TABLE IF NOT EXISTS `mp_asis_marcaciones` (
  `id` int(6) NOT NULL,
  `emp` int(6) NOT NULL,
  `emp_code` char(14) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `departament` varchar(150) NOT NULL,
  `position` varchar(100) NOT NULL,
  `punch_time` char(20) NOT NULL,
  `punch_state` int(2) NOT NULL,
  `punch_state_display` varchar(50) NOT NULL,
  `verify_type` int(2) NOT NULL,
  `verify_type_display` varchar(50) NOT NULL,
  `work_code` varchar(50) NOT NULL,
  `gps_location` varchar(50) NOT NULL,
  `area_alias` varchar(50) NOT NULL,
  `terminal_sn` varchar(50) NOT NULL,
  `temperature` varchar(10) NOT NULL,
  `is_mask` varchar(20) NOT NULL,
  `terminal_alias` varchar(20) NOT NULL,
  `upload_time` char(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_bienesinventario
CREATE TABLE IF NOT EXISTS `mp_bienesinventario` (
  `codi_bien` int(11) NOT NULL AUTO_INCREMENT,
  `bien_correlativo` char(50) NOT NULL,
  `bien_codpatrimonial` char(10) NOT NULL,
  `bien_descripcion` char(50) NOT NULL,
  `bien_marca` char(20) NOT NULL,
  `bien_modelo` char(20) NOT NULL,
  `bien_serie` char(20) NOT NULL,
  `bien_cantidad` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `bien_tecnologia` int(11) NOT NULL,
  PRIMARY KEY (`codi_bien`),
  KEY `bien_codpatrimonial` (`bien_codpatrimonial`)
) ENGINE=MyISAM AUTO_INCREMENT=16344 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_bienestecnologias
CREATE TABLE IF NOT EXISTS `mp_bienestecnologias` (
  `tecno_id` int(11) NOT NULL AUTO_INCREMENT,
  `tecno_descripcion` char(25) CHARACTER SET utf8 NOT NULL,
  `activo` tinyint(1) NOT NULL,
  PRIMARY KEY (`tecno_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_bienes_movcabecera
CREATE TABLE IF NOT EXISTS `mp_bienes_movcabecera` (
  `codi_movi` int(11) NOT NULL AUTO_INCREMENT,
  `movi_nroxanno` int(11) NOT NULL,
  `movi_fecha` date NOT NULL,
  `movi_usuariofila1` char(100) NOT NULL,
  `movi_usuariofila2` char(100) NOT NULL,
  `movi_referencia` char(100) NOT NULL,
  `movi_elaboradopor` char(70) NOT NULL,
  `movi_tipo_is` char(1) NOT NULL,
  `movi_pers` int(11) NOT NULL,
  `movi_depe` int(11) NOT NULL,
  PRIMARY KEY (`codi_movi`),
  KEY `movi_nroxanno` (`movi_nroxanno`)
) ENGINE=MyISAM AUTO_INCREMENT=16348 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_bienes_movdetalle
CREATE TABLE IF NOT EXISTS `mp_bienes_movdetalle` (
  `codi_movi` int(11) NOT NULL,
  `codi_bien` int(11) NOT NULL,
  `bien_estado` char(1) NOT NULL,
  `bien_cantidad` smallint(6) NOT NULL,
  `movi_tipo_is` char(1) NOT NULL,
  KEY `codi_movi` (`codi_movi`),
  KEY `codi_bien` (`codi_bien`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_bingo
CREATE TABLE IF NOT EXISTS `mp_bingo` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `dni` char(8) NOT NULL,
  `numero` int(3) NOT NULL,
  `columna` int(1) NOT NULL,
  `fila` int(1) NOT NULL,
  `bingo` int(2) NOT NULL,
  `digi` int(6) NOT NULL,
  `fdig` char(14) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8441 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_capacitacion_documento
CREATE TABLE IF NOT EXISTS `mp_capacitacion_documento` (
  `codi_docu` int(6) NOT NULL AUTO_INCREMENT,
  `nomb_docu` text NOT NULL,
  `sumi_docu` text NOT NULL,
  `dire_docu` varchar(200) NOT NULL,
  `driv_docu` varchar(200) NOT NULL,
  `codi_tema` int(3) NOT NULL,
  `fech_docu` char(10) NOT NULL,
  `esta_docu` int(1) NOT NULL,
  `digi_docu` int(6) NOT NULL,
  `fdig_docu` char(14) NOT NULL,
  PRIMARY KEY (`codi_docu`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_comp_compras
CREATE TABLE IF NOT EXISTS `mp_comp_compras` (
  `codi_comp` int(6) NOT NULL AUTO_INCREMENT,
  `nomb_comp` text NOT NULL,
  `inic_comp` char(14) NOT NULL,
  `fina_comp` char(14) NOT NULL,
  `codi_rubr` int(3) NOT NULL,
  `flag_mail` int(1) NOT NULL,
  `esta_comp` int(1) NOT NULL,
  `digi_comp` int(6) NOT NULL,
  `fdig_comp` char(14) NOT NULL,
  `comp_cerr` tinyint(1) NOT NULL,
  `codi_gana` int(11) NOT NULL,
  PRIMARY KEY (`codi_comp`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_comp_lugarentrega
CREATE TABLE IF NOT EXISTS `mp_comp_lugarentrega` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_comp_procprov
CREATE TABLE IF NOT EXISTS `mp_comp_procprov` (
  `proc_auto` int(11) NOT NULL AUTO_INCREMENT,
  `proc_codi` int(11) NOT NULL,
  `proc_prov` int(11) NOT NULL,
  `proc_rubr` int(11) NOT NULL,
  `proc_fech` datetime NOT NULL,
  `proc_luge` int(11) NOT NULL,
  `proc_dias` int(11) NOT NULL,
  `proc_mont` decimal(9,2) NOT NULL,
  `proc_incigv` char(1) NOT NULL,
  `proc_resu` tinyint(1) NOT NULL,
  PRIMARY KEY (`proc_auto`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_comp_proveedores
CREATE TABLE IF NOT EXISTS `mp_comp_proveedores` (
  `codi_prov` int(6) NOT NULL AUTO_INCREMENT,
  `nomb_prov` varchar(200) NOT NULL,
  `nomb_come` char(100) CHARACTER SET utf8 NOT NULL,
  `nruc_prov` char(11) NOT NULL,
  `dire_prov` char(100) CHARACTER SET utf8 NOT NULL,
  `mail_prov` char(50) CHARACTER SET utf8 NOT NULL,
  `fono_prov` char(25) CHARACTER SET utf8 NOT NULL,
  `repr_legal` char(50) CHARACTER SET utf8 NOT NULL,
  `cont_prov` char(50) CHARACTER SET utf8 NOT NULL,
  `rnp_prov` tinyint(1) NOT NULL,
  `mype_prov` tinyint(1) NOT NULL,
  `tipo_rubr` tinyint(1) NOT NULL,
  `codi_rubr` int(3) NOT NULL,
  `deta_acti` text NOT NULL,
  `esta_prov` int(1) NOT NULL,
  `digi_prov` int(6) NOT NULL,
  `fdig_prov` char(14) NOT NULL,
  `pass_prov` char(15) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`codi_prov`),
  KEY `nruc_prov` (`nruc_prov`),
  KEY `codi_rubr` (`codi_rubr`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_concurso_examen
CREATE TABLE IF NOT EXISTS `mp_concurso_examen` (
  `codi_exam` int(6) NOT NULL AUTO_INCREMENT,
  `nomb_exam` varchar(100) NOT NULL,
  `fech_exam` char(10) NOT NULL,
  `acti_exam` int(1) NOT NULL,
  `digi_exam` int(6) NOT NULL,
  `fdig_exam` char(14) NOT NULL,
  `esta_exam` int(1) NOT NULL,
  PRIMARY KEY (`codi_exam`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_concurso_plazas
CREATE TABLE IF NOT EXISTS `mp_concurso_plazas` (
  `codi_plaz` int(6) NOT NULL AUTO_INCREMENT,
  `codi_proc` int(6) NOT NULL,
  `nomb_plaz` char(15) NOT NULL,
  `codi_carg` int(3) NOT NULL,
  `digi_plaz` int(6) NOT NULL,
  `fdig_plaz` char(14) NOT NULL,
  `esta_plaz` int(1) NOT NULL,
  PRIMARY KEY (`codi_plaz`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_concurso_postulantes
CREATE TABLE IF NOT EXISTS `mp_concurso_postulantes` (
  `codi_post` int(6) NOT NULL AUTO_INCREMENT,
  `codi_plaz` int(6) NOT NULL,
  `docu_post` char(20) NOT NULL,
  `appa_post` varchar(100) NOT NULL,
  `apma_post` varchar(100) NOT NULL,
  `nomb_post` varchar(100) NOT NULL,
  `tdoc_post` char(10) NOT NULL,
  `regi_post` char(10) NOT NULL,
  `mail_post` varchar(100) NOT NULL,
  `celu_post` char(50) NOT NULL,
  `regi_asis` int(1) NOT NULL,
  `fdig_asis` char(14) NOT NULL,
  `digi_post` int(6) NOT NULL,
  `fdig_post` char(14) NOT NULL,
  `esta_post` int(1) NOT NULL,
  PRIMARY KEY (`codi_post`)
) ENGINE=MyISAM AUTO_INCREMENT=8068 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_concurso_proceso
CREATE TABLE IF NOT EXISTS `mp_concurso_proceso` (
  `codi_proc` int(6) NOT NULL AUTO_INCREMENT,
  `codi_exam` int(6) NOT NULL,
  `nume_proc` int(3) NOT NULL,
  `anno_proc` char(4) NOT NULL,
  `regi_proc` int(3) NOT NULL,
  `digi_proc` int(6) NOT NULL,
  `fdig_proc` char(14) NOT NULL,
  `esta_proc` int(1) NOT NULL,
  PRIMARY KEY (`codi_proc`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cons_audi
CREATE TABLE IF NOT EXISTS `mp_cons_audi` (
  `iden_audi` int(6) NOT NULL AUTO_INCREMENT,
  `sede_audi` char(4) NOT NULL,
  `anno_audi` char(4) NOT NULL,
  `mess_audi` char(2) NOT NULL,
  `expe_audi` char(16) NOT NULL,
  `audi_audi` char(6) NOT NULL,
  `arch_audi` varchar(50) NOT NULL,
  `digi_audi` int(6) NOT NULL,
  `fdig_audi` char(14) NOT NULL,
  `esta_audi` int(1) NOT NULL,
  PRIMARY KEY (`iden_audi`),
  KEY `expe_audi` (`expe_audi`)
) ENGINE=MyISAM AUTO_INCREMENT=29320 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cons_audi_oper
CREATE TABLE IF NOT EXISTS `mp_cons_audi_oper` (
  `iden_auop` int(6) NOT NULL AUTO_INCREMENT,
  `sede_audi` char(4) NOT NULL,
  `anno_audi` char(4) NOT NULL,
  `mess_audi` char(2) NOT NULL,
  `expe_audi` char(16) NOT NULL,
  `audi_audi` char(6) NOT NULL,
  `arch_audi` varchar(100) NOT NULL,
  `iden_oper` int(6) NOT NULL,
  `ndoc_oper` char(12) NOT NULL,
  `acce_audi` int(2) NOT NULL,
  `dire_auop` char(15) NOT NULL,
  `fdig_auop` char(14) DEFAULT NULL,
  `esta_auop` int(1) NOT NULL,
  PRIMARY KEY (`iden_auop`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cons_audi_sede
CREATE TABLE IF NOT EXISTS `mp_cons_audi_sede` (
  `codi_sede` char(2) NOT NULL,
  `nomb_sede` varchar(30) NOT NULL,
  PRIMARY KEY (`codi_sede`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cons_firm
CREATE TABLE IF NOT EXISTS `mp_cons_firm` (
  `id_registro` int(11) NOT NULL AUTO_INCREMENT,
  `f_programada` datetime DEFAULT NULL,
  `f_firma` datetime DEFAULT NULL,
  `x_observacion` text DEFAULT NULL,
  `x_ape_paterno` varchar(255) DEFAULT NULL,
  `x_ape_materno` varchar(255) DEFAULT NULL,
  `x_nombres` varchar(255) DEFAULT NULL,
  `c_usuario` varchar(100) DEFAULT NULL,
  `x_nom_instancia` varchar(255) DEFAULT NULL,
  `c_estado` varchar(50) DEFAULT NULL,
  `c_persona` varchar(100) DEFAULT NULL,
  `tx_formato` varchar(255) DEFAULT NULL,
  `tx_doc_id` varchar(100) DEFAULT NULL,
  `fecha_carga` datetime NOT NULL,
  `numero_archivo_carga` int(11) NOT NULL,
  PRIMARY KEY (`id_registro`),
  UNIQUE KEY `uq_firma_unica` (`tx_doc_id`,`tx_formato`,`f_firma`)
) ENGINE=MyISAM AUTO_INCREMENT=85860 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cons_firm_procesados
CREATE TABLE IF NOT EXISTS `mp_cons_firm_procesados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) NOT NULL,
  `hash_contenido` varchar(64) NOT NULL,
  `numero_archivo_carga_ref` int(11) NOT NULL,
  `fecha_procesado` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_hash_contenido` (`hash_contenido`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cpbi_auditoria
CREATE TABLE IF NOT EXISTS `mp_cpbi_auditoria` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `codi_bien` int(9) NOT NULL,
  `iden_oper` int(6) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `accion` varchar(255) NOT NULL COMMENT 'Ej: Creación, Actualización, Cambio de estado, Asignación',
  `detalles_anteriores` text DEFAULT NULL,
  `detalles_nuevos` text DEFAULT NULL,
  PRIMARY KEY (`id_auditoria`),
  KEY `fk_auditoria_bien_idx` (`codi_bien`),
  KEY `fk_auditoria_oper_idx` (`iden_oper`),
  CONSTRAINT `fk_auditoria_bien_fk` FOREIGN KEY (`codi_bien`) REFERENCES `mp_cpbi_bienes` (`codi_bien`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_auditoria_oper_fk` FOREIGN KEY (`iden_oper`) REFERENCES `mp_admi_oper` (`iden_oper`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_cpbi_bienes
CREATE TABLE IF NOT EXISTS `mp_cpbi_bienes` (
  `codi_bien` int(9) NOT NULL AUTO_INCREMENT,
  `codi_distfiscal` int(11) NOT NULL,
  `nume_regi` char(20) CHARACTER SET utf8 NOT NULL,
  `corr_regi` int(6) NOT NULL,
  `anno_regi` char(4) NOT NULL,
  `desc_bien` text NOT NULL,
  `marc_bien` varchar(50) NOT NULL,
  `seri_bien` varchar(50) NOT NULL,
  `tplg_bien` varchar(20) NOT NULL,
  `id_tipo_bien` int(11) DEFAULT NULL,
  `nume_carp` varchar(60) NOT NULL,
  `codi_deli` int(3) NOT NULL,
  `codi_depe` int(6) NOT NULL,
  `codi_fisc` int(6) NOT NULL,
  `codi_ubic` int(3) NOT NULL,
  `codi_esta` int(3) NOT NULL,
  `codi_epro` int(3) NOT NULL,
  `cali_defi` int(1) NOT NULL,
  `fech_inte` date NOT NULL,
  `esta_bien` int(1) NOT NULL,
  `digi_bien` int(6) NOT NULL,
  `fdig_bien` char(14) NOT NULL,
  `digi_oper_id` int(6) DEFAULT NULL,
  `codi_disp` int(11) NOT NULL,
  `agraviante` char(30) CHARACTER SET utf8 NOT NULL,
  `agraviado` char(30) CHARACTER SET utf8 NOT NULL,
  `ruta_archivo_digital` varchar(255) DEFAULT NULL,
  `ubig_provincia` char(6) NOT NULL,
  PRIMARY KEY (`codi_bien`),
  KEY `nume_regi` (`nume_regi`),
  KEY `fk_cpbi_bienes_maes_cpbi_tipos` (`id_tipo_bien`),
  KEY `fk_cpbi_bienes_maes_cpbi_estado` (`codi_esta`),
  KEY `fk_cpbi_bienes_maes_cpbi_ubicacion` (`codi_ubic`),
  KEY `fk_cpbi_bienes_maes_cpbi_estado_proceso` (`codi_epro`),
  CONSTRAINT `fk_cpbi_bienes_maes_cpbi_estado` FOREIGN KEY (`codi_esta`) REFERENCES `mp_maes_cpbi_estado` (`n_codigo`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cpbi_bienes_maes_cpbi_estado_proceso` FOREIGN KEY (`codi_epro`) REFERENCES `mp_maes_cpbi_estado_proceso` (`n_codigo`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cpbi_bienes_maes_cpbi_tipos` FOREIGN KEY (`id_tipo_bien`) REFERENCES `mp_maes_cpbi_tipos` (`id_tipo`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cpbi_bienes_maes_cpbi_ubicacion` FOREIGN KEY (`codi_ubic`) REFERENCES `mp_maes_cpbi_ubicacion` (`n_codigo`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_acta
CREATE TABLE IF NOT EXISTS `mp_elec_acta` (
  `codi_acta` int(6) NOT NULL AUTO_INCREMENT,
  `codi_elec` int(3) NOT NULL,
  `codi_usua` char(8) NOT NULL,
  `codi_loca` int(6) NOT NULL,
  `codi_tact` int(3) NOT NULL,
  `codi_deli` int(6) NOT NULL,
  `dete_inte` int(1) NOT NULL,
  `cant_homb` int(3) NOT NULL,
  `cant_muje` int(3) NOT NULL,
  `digi_acta` int(6) NOT NULL,
  `fdig_acta` char(14) NOT NULL,
  `esta_acta` int(1) NOT NULL,
  PRIMARY KEY (`codi_acta`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_alertas
CREATE TABLE IF NOT EXISTS `mp_elec_alertas` (
  `codi_aler` int(6) NOT NULL AUTO_INCREMENT,
  `codi_elec` int(3) NOT NULL,
  `codi_usua` char(8) NOT NULL,
  `aler_ocur` int(1) NOT NULL,
  `codi_tale` int(3) NOT NULL,
  `fech_aler` char(14) NOT NULL,
  `ubig_aler` char(6) NOT NULL,
  `luga_aler` varchar(200) NOT NULL,
  `deta_aler` text NOT NULL,
  `acci_aler` text NOT NULL,
  `digi_aler` int(6) NOT NULL,
  `fdig_aler` char(14) NOT NULL,
  `esta_aler` int(1) NOT NULL,
  PRIMARY KEY (`codi_aler`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_alertas_lesionados
CREATE TABLE IF NOT EXISTS `mp_elec_alertas_lesionados` (
  `codi_lesi` int(6) NOT NULL AUTO_INCREMENT,
  `codi_aler` int(6) NOT NULL,
  `lesi_fall` int(1) NOT NULL,
  `nomb_lesi` varchar(200) NOT NULL,
  `ndni_lesi` char(20) NOT NULL,
  `sexo_lesi` int(1) NOT NULL,
  `edad_lesi` int(3) NOT NULL,
  `digi_lesi` int(6) NOT NULL,
  `fdig_lesi` char(14) NOT NULL,
  `esta_lesi` int(1) NOT NULL,
  PRIMARY KEY (`codi_lesi`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_asignacion
CREATE TABLE IF NOT EXISTS `mp_elec_asignacion` (
  `codi_asig` int(6) NOT NULL AUTO_INCREMENT,
  `codi_usua` int(6) NOT NULL,
  `codi_loca` int(6) NOT NULL,
  `codi_elec` int(6) NOT NULL,
  `digi_asig` int(6) NOT NULL,
  `fdig_asig` char(14) NOT NULL,
  `esta_asig` int(1) NOT NULL,
  PRIMARY KEY (`codi_asig`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_config
CREATE TABLE IF NOT EXISTS `mp_elec_config` (
  `codi_elec` int(3) NOT NULL AUTO_INCREMENT,
  `nomb_elec` varchar(200) NOT NULL,
  `anno_elec` char(4) NOT NULL,
  `fech_elec` char(8) NOT NULL,
  `habi_elec` int(1) NOT NULL,
  `digi_elec` int(6) NOT NULL,
  `fdig_elec` char(14) NOT NULL,
  `esta_elec` int(1) NOT NULL,
  PRIMARY KEY (`codi_elec`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_coordinaciones
CREATE TABLE IF NOT EXISTS `mp_elec_coordinaciones` (
  `codi_coor` int(6) NOT NULL AUTO_INCREMENT,
  `codi_elec` int(3) NOT NULL,
  `codi_usua` char(8) NOT NULL,
  `codi_inst` int(3) NOT NULL,
  `fech_coor` char(8) NOT NULL,
  `ubig_coor` char(6) NOT NULL,
  `obse_coor` text NOT NULL,
  `digi_coor` int(6) NOT NULL,
  `fdig_coor` char(14) NOT NULL,
  `esta_coor` int(1) NOT NULL,
  PRIMARY KEY (`codi_coor`)
) ENGINE=MyISAM AUTO_INCREMENT=466 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_detenciones
CREATE TABLE IF NOT EXISTS `mp_elec_detenciones` (
  `codi_dete` int(6) NOT NULL AUTO_INCREMENT,
  `codi_elec` int(3) NOT NULL,
  `codi_usua` char(8) NOT NULL,
  `dete_inte` int(1) NOT NULL,
  `fech_dete` char(14) NOT NULL,
  `ubig_dete` char(6) NOT NULL,
  `nomb_dete` varchar(200) NOT NULL,
  `ndni_dete` char(8) NOT NULL,
  `edad_dete` int(3) NOT NULL,
  `sexo_dete` int(1) NOT NULL,
  `codi_inte` int(3) NOT NULL,
  `hora_moti` char(5) NOT NULL,
  `deta_moti` varchar(200) NOT NULL,
  `codi_acci` int(3) NOT NULL,
  `codi_deli` int(3) NOT NULL,
  `luga_inte` varchar(200) NOT NULL,
  `deta_inte` text NOT NULL,
  `digi_dete` int(6) NOT NULL,
  `fdig_dete` char(14) NOT NULL,
  `esta_dete` int(1) NOT NULL,
  PRIMARY KEY (`codi_dete`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_difusion
CREATE TABLE IF NOT EXISTS `mp_elec_difusion` (
  `codi_difu` int(6) NOT NULL AUTO_INCREMENT,
  `codi_elec` int(3) NOT NULL,
  `codi_usua` char(8) NOT NULL,
  `codi_tdif` int(3) NOT NULL,
  `fech_difu` char(8) NOT NULL,
  `ubig_difu` char(6) NOT NULL,
  `obse_difu` text NOT NULL,
  `digi_difu` int(6) NOT NULL,
  `fdig_difu` char(14) NOT NULL,
  `esta_difu` int(1) NOT NULL,
  PRIMARY KEY (`codi_difu`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_locales
CREATE TABLE IF NOT EXISTS `mp_elec_locales` (
  `codi_loca` int(6) NOT NULL AUTO_INCREMENT,
  `nomb_loca` varchar(200) NOT NULL,
  `codi_elec` int(3) NOT NULL,
  `ubig_loca` char(6) NOT NULL,
  `digi_loca` int(6) NOT NULL,
  `fdig_loca` char(14) NOT NULL,
  `esta_loca` int(1) NOT NULL,
  PRIMARY KEY (`codi_loca`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_prevencion
CREATE TABLE IF NOT EXISTS `mp_elec_prevencion` (
  `codi_prev` int(6) NOT NULL AUTO_INCREMENT,
  `codi_elec` int(3) NOT NULL,
  `codi_usua` char(8) NOT NULL,
  `codi_tpre` int(3) NOT NULL,
  `fech_prev` char(8) NOT NULL,
  `ubig_prev` char(6) NOT NULL,
  `obse_prev` text NOT NULL,
  `digi_prev` int(6) NOT NULL,
  `fdig_prev` char(14) NOT NULL,
  `esta_prev` int(1) NOT NULL,
  PRIMARY KEY (`codi_prev`)
) ENGINE=MyISAM AUTO_INCREMENT=897 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_elec_usuario_local
CREATE TABLE IF NOT EXISTS `mp_elec_usuario_local` (
  `codi_usua` char(8) NOT NULL,
  `codi_loca` int(6) NOT NULL,
  PRIMARY KEY (`codi_usua`,`codi_loca`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_fotocheck_personal
CREATE TABLE IF NOT EXISTS `mp_fotocheck_personal` (
  `codi_pers` int(6) NOT NULL AUTO_INCREMENT,
  `ndni_pers` char(8) CHARACTER SET latin1 NOT NULL,
  `appe_pers` varchar(100) CHARACTER SET latin1 NOT NULL,
  `nomb_pers` varchar(100) CHARACTER SET latin1 NOT NULL,
  `codi_adic` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `codi_depe` int(3) NOT NULL,
  `codi_carg` int(3) NOT NULL,
  `codi_regi` int(3) NOT NULL,
  `habi_impr` int(1) NOT NULL,
  `esta_impr` int(1) NOT NULL,
  `fech_impr` char(8) COLLATE utf8_unicode_ci NOT NULL,
  `esta_pers` int(1) NOT NULL,
  `digi_pers` int(6) NOT NULL,
  `fdig_pers` char(14) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`codi_pers`)
) ENGINE=MyISAM AUTO_INCREMENT=965 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_fotocheck_secigra
CREATE TABLE IF NOT EXISTS `mp_fotocheck_secigra` (
  `codi_pers` int(6) NOT NULL AUTO_INCREMENT,
  `ndni_pers` char(8) NOT NULL,
  `appe_pers` varchar(100) NOT NULL,
  `nomb_pers` varchar(100) NOT NULL,
  `codi_adic` varchar(20) NOT NULL,
  `codi_depe` int(3) NOT NULL,
  `codi_carg` int(3) NOT NULL,
  `codi_regi` int(3) NOT NULL,
  `habi_impr` int(1) NOT NULL,
  `esta_impr` int(1) NOT NULL,
  `fech_impr` char(8) NOT NULL,
  `esta_pers` int(1) NOT NULL,
  `digi_pers` int(6) NOT NULL,
  `fdig_pers` char(14) NOT NULL,
  PRIMARY KEY (`codi_pers`)
) ENGINE=MyISAM AUTO_INCREMENT=248 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_fsup_mpar_ingreso
CREATE TABLE IF NOT EXISTS `mp_fsup_mpar_ingreso` (
  `codi_ingr` int(6) NOT NULL AUTO_INCREMENT,
  `carp_depe` int(3) NOT NULL,
  `carp_anno` char(4) NOT NULL,
  `carp_caso` int(4) NOT NULL,
  `carp_cuad` int(4) NOT NULL,
  `orig_depe` int(4) NOT NULL,
  `orig_fisc` int(4) NOT NULL,
  `orig_tipo` int(3) NOT NULL,
  `ingr_foli` int(6) NOT NULL,
  `ingr_obse` text NOT NULL,
  `ingr_esta` int(1) NOT NULL,
  `ingr_digi` int(6) NOT NULL,
  `ingr_fdig` char(14) NOT NULL,
  PRIMARY KEY (`codi_ingr`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_horascompensa_cabecera
CREATE TABLE IF NOT EXISTS `mp_horascompensa_cabecera` (
  `comp_autogen` int(11) NOT NULL AUTO_INCREMENT,
  `comp_nroexpediente` int(11) NOT NULL,
  `comp_anoexpediente` char(4) NOT NULL,
  `comp_personal` int(11) NOT NULL,
  `comp_fecharegistro` date NOT NULL,
  PRIMARY KEY (`comp_autogen`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_horascompensa_detalle
CREATE TABLE IF NOT EXISTS `mp_horascompensa_detalle` (
  `comp_id` int(11) NOT NULL,
  `comp_institucion` char(50) NOT NULL,
  `comp_tema` char(70) NOT NULL,
  `comp_intervalofechas` char(30) NOT NULL,
  `comp_modalidad` char(1) NOT NULL,
  `comp_horas` tinyint(4) NOT NULL,
  `comp_tp` char(1) NOT NULL,
  KEY `comp_id` (`comp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_horascompensa_sobretiempo
CREATE TABLE IF NOT EXISTS `mp_horascompensa_sobretiempo` (
  `sobr_autogen` int(11) NOT NULL AUTO_INCREMENT,
  `sobr_personal` int(11) NOT NULL,
  `sobr_fecha` date NOT NULL,
  `sobr_horas` char(5) NOT NULL,
  `sobr_expcea` char(20) NOT NULL,
  `sobr_anocea` char(4) NOT NULL,
  PRIMARY KEY (`sobr_autogen`),
  KEY `sobr_personal` (`sobr_personal`)
) ENGINE=MyISAM AUTO_INCREMENT=10814 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_horascompensa_vacaciones
CREATE TABLE IF NOT EXISTS `mp_horascompensa_vacaciones` (
  `vaca_autogen` int(11) NOT NULL AUTO_INCREMENT,
  `vaca_personal` int(11) NOT NULL,
  `vaca_expcea` char(20) NOT NULL,
  `vaca_anocea` char(4) NOT NULL,
  `vaca_resolucion` char(20) NOT NULL,
  `vaca_fecemision` date NOT NULL,
  `vaca_periodo` char(20) NOT NULL,
  `vaca_fechaini` date NOT NULL,
  `vaca_fechafin` date NOT NULL,
  `vaca_incluyesabdom` char(1) NOT NULL,
  PRIMARY KEY (`vaca_autogen`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_inve_mant
CREATE TABLE IF NOT EXISTS `mp_inve_mant` (
  `codi_inve` int(2) NOT NULL AUTO_INCREMENT,
  `nomb_inve` varchar(100) NOT NULL,
  `fech_inve` date NOT NULL,
  `acti_inve` int(1) NOT NULL,
  `digi_inve` int(3) NOT NULL,
  `fdig_inve` char(14) NOT NULL,
  `esta_inve` int(1) NOT NULL,
  PRIMARY KEY (`codi_inve`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_inve_regi
CREATE TABLE IF NOT EXISTS `mp_inve_regi` (
  `codi_regi` int(6) NOT NULL AUTO_INCREMENT,
  `codi_inve` int(3) NOT NULL,
  `codi_loca` int(3) NOT NULL,
  `codi_depe` int(5) NOT NULL,
  `usua_inve` char(12) NOT NULL,
  `codi_patr` char(15) NOT NULL,
  `lati_regi` varchar(20) NOT NULL,
  `long_regi` varchar(20) NOT NULL,
  `iest_regi` char(7) NOT NULL,
  `iuso_regi` char(2) NOT NULL,
  `iare_regi` varchar(20) NOT NULL,
  `iobs_regi` text NOT NULL,
  `iubi_regi` int(3) NOT NULL,
  `idep_regi` int(4) NOT NULL,
  `iusu_regi` int(5) NOT NULL,
  `digi_regi` int(6) NOT NULL,
  `fdig_regi` char(14) NOT NULL,
  `esta_regi` int(1) NOT NULL,
  PRIMARY KEY (`codi_regi`)
) ENGINE=InnoDB AUTO_INCREMENT=902 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_inve_sele
CREATE TABLE IF NOT EXISTS `mp_inve_sele` (
  `codi_oper` int(6) NOT NULL,
  `codi_loca` int(3) NOT NULL,
  `codi_depe` int(4) NOT NULL,
  `codi_usua` int(6) NOT NULL,
  PRIMARY KEY (`codi_oper`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_inve_siga
CREATE TABLE IF NOT EXISTS `mp_inve_siga` (
  `codigo_patrimonial` char(12) NOT NULL,
  `codigo_barra` char(7) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` char(30) NOT NULL,
  `nro_serie` char(20) NOT NULL,
  `color` char(20) NOT NULL,
  `nombre` char(10) NOT NULL,
  `medidas` char(20) NOT NULL,
  `fecha_alta` char(22) NOT NULL,
  `ubicac_fisica` varchar(200) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `docum_identidad` char(12) NOT NULL,
  `observaciones` varchar(100) NOT NULL,
  `nombre_depend` varchar(200) NOT NULL,
  `nombre_sede` varchar(200) NOT NULL,
  `uso` char(2) NOT NULL,
  PRIMARY KEY (`codigo_patrimonial`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_inve_view_regi
CREATE TABLE IF NOT EXISTS `mp_inve_view_regi` (
  `codigo_patrimonial` char(12) CHARACTER SET latin1 NOT NULL,
  `codigo_barra` char(7) CHARACTER SET latin1 NOT NULL,
  `descripcion` varchar(200) CHARACTER SET latin1 NOT NULL,
  `marca` varchar(100) CHARACTER SET latin1 NOT NULL,
  `modelo` char(30) CHARACTER SET latin1 NOT NULL,
  `nro_serie` char(20) CHARACTER SET latin1 NOT NULL,
  `color` char(20) CHARACTER SET latin1 NOT NULL,
  `nombre` char(10) CHARACTER SET latin1 NOT NULL,
  `medidas` char(20) CHARACTER SET latin1 NOT NULL,
  `fecha_alta` char(22) CHARACTER SET latin1 NOT NULL,
  `ubicac_fisica` varchar(200) CHARACTER SET latin1 NOT NULL,
  `usuario` varchar(100) CHARACTER SET latin1 NOT NULL,
  `docum_identidad` char(12) CHARACTER SET latin1 NOT NULL,
  `observaciones` varchar(100) CHARACTER SET latin1 NOT NULL,
  `nombre_depend` varchar(200) CHARACTER SET latin1 NOT NULL,
  `nombre_sede` varchar(200) CHARACTER SET latin1 NOT NULL,
  `uso` char(2) CHARACTER SET latin1 NOT NULL,
  `codi_regi` int(6) DEFAULT NULL,
  `codi_inve` int(3) DEFAULT NULL,
  `codi_loca` int(3) DEFAULT NULL,
  `codi_depe` int(5) DEFAULT NULL,
  `usua_inve` char(12) CHARACTER SET latin1 DEFAULT NULL,
  `codi_patr` char(15) CHARACTER SET latin1 DEFAULT NULL,
  `lati_regi` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `long_regi` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `iest_regi` char(7) CHARACTER SET latin1 DEFAULT NULL,
  `iuso_regi` char(2) CHARACTER SET latin1 DEFAULT NULL,
  `iare_regi` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `iobs_regi` text CHARACTER SET latin1 DEFAULT NULL,
  `iubi_regi` int(3) DEFAULT NULL,
  `idep_regi` int(4) DEFAULT NULL,
  `iusu_regi` int(5) DEFAULT NULL,
  `digi_regi` int(6) DEFAULT NULL,
  `fdig_regi` char(14) CHARACTER SET latin1 DEFAULT NULL,
  `esta_regi` int(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_jurisprudencia_documento
CREATE TABLE IF NOT EXISTS `mp_jurisprudencia_documento` (
  `codi_docu` int(9) NOT NULL AUTO_INCREMENT,
  `nomb_docu` text NOT NULL,
  `expe_docu` char(30) NOT NULL,
  `codi_espe` int(3) NOT NULL,
  `esta_docu` int(1) NOT NULL,
  `digi_docu` int(6) NOT NULL,
  `fdig_docu` char(14) NOT NULL,
  PRIMARY KEY (`codi_docu`)
) ENGINE=MyISAM AUTO_INCREMENT=256 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_afp
CREATE TABLE IF NOT EXISTS `mp_maes_afp` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_capacitacion_tema
CREATE TABLE IF NOT EXISTS `mp_maes_capacitacion_tema` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_cargo
CREATE TABLE IF NOT EXISTS `mp_maes_cargo` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_comp_rubro
CREATE TABLE IF NOT EXISTS `mp_maes_comp_rubro` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_concurso_regimen
CREATE TABLE IF NOT EXISTS `mp_maes_concurso_regimen` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
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
  `id_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_deportista_calificado
CREATE TABLE IF NOT EXISTS `mp_maes_deportista_calificado` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_discapacidad
CREATE TABLE IF NOT EXISTS `mp_maes_discapacidad` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_distritofiscal
CREATE TABLE IF NOT EXISTS `mp_maes_distritofiscal` (
  `n_codigo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` char(30) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_elecciones_accionar
CREATE TABLE IF NOT EXISTS `mp_maes_elecciones_accionar` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(300) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_elecciones_acta_tipo
CREATE TABLE IF NOT EXISTS `mp_maes_elecciones_acta_tipo` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(200) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_elecciones_alertas_tipo
CREATE TABLE IF NOT EXISTS `mp_maes_elecciones_alertas_tipo` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(300) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_elecciones_delito
CREATE TABLE IF NOT EXISTS `mp_maes_elecciones_delito` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(200) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_elecciones_difusion
CREATE TABLE IF NOT EXISTS `mp_maes_elecciones_difusion` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(200) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_elecciones_instituciones
CREATE TABLE IF NOT EXISTS `mp_maes_elecciones_instituciones` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(200) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_elecciones_intervencion
CREATE TABLE IF NOT EXISTS `mp_maes_elecciones_intervencion` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(200) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_elecciones_prevencion
CREATE TABLE IF NOT EXISTS `mp_maes_elecciones_prevencion` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(300) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_enfermedades_tipo
CREATE TABLE IF NOT EXISTS `mp_maes_enfermedades_tipo` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_essalud_sedes
CREATE TABLE IF NOT EXISTS `mp_maes_essalud_sedes` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_estado_civil
CREATE TABLE IF NOT EXISTS `mp_maes_estado_civil` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_ffaa
CREATE TABLE IF NOT EXISTS `mp_maes_ffaa` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_fotocheck_cargo
CREATE TABLE IF NOT EXISTS `mp_maes_fotocheck_cargo` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(200) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_fotocheck_dependencia
CREATE TABLE IF NOT EXISTS `mp_maes_fotocheck_dependencia` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(200) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=128 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_fotocheck_rlaboral
CREATE TABLE IF NOT EXISTS `mp_maes_fotocheck_rlaboral` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(200) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_fsup_mpar_tipo
CREATE TABLE IF NOT EXISTS `mp_maes_fsup_mpar_tipo` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(200) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_ginstruccion
CREATE TABLE IF NOT EXISTS `mp_maes_ginstruccion` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_grado_especialidades
CREATE TABLE IF NOT EXISTS `mp_maes_grado_especialidades` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=3452 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_grado_estado
CREATE TABLE IF NOT EXISTS `mp_maes_grado_estado` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_grado_instituciones
CREATE TABLE IF NOT EXISTS `mp_maes_grado_instituciones` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=250 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_grado_nivel
CREATE TABLE IF NOT EXISTS `mp_maes_grado_nivel` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_grupo_sanguineo
CREATE TABLE IF NOT EXISTS `mp_maes_grupo_sanguineo` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_hobbies
CREATE TABLE IF NOT EXISTS `mp_maes_hobbies` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_idiomas
CREATE TABLE IF NOT EXISTS `mp_maes_idiomas` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_idiomas_nivel
CREATE TABLE IF NOT EXISTS `mp_maes_idiomas_nivel` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_item
CREATE TABLE IF NOT EXISTS `mp_maes_item` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_item_contr
CREATE TABLE IF NOT EXISTS `mp_maes_item_contr` (
  `codi_auto` int(11) NOT NULL AUTO_INCREMENT,
  `codi_item` int(3) NOT NULL,
  `codi_loca` int(11) NOT NULL,
  `nro_contr` char(20) NOT NULL DEFAULT '',
  `fech_inic` date NOT NULL DEFAULT '0000-00-00',
  `acti_esta` int(1) NOT NULL,
  PRIMARY KEY (`codi_auto`),
  KEY `codi_item` (`codi_item`),
  KEY `nro_contr` (`nro_contr`),
  KEY `codi_loca` (`codi_loca`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_jurisprudencia_especialidad
CREATE TABLE IF NOT EXISTS `mp_maes_jurisprudencia_especialidad` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_labo_cargos
CREATE TABLE IF NOT EXISTS `mp_maes_labo_cargos` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=15497 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_labo_condic_contractual
CREATE TABLE IF NOT EXISTS `mp_maes_labo_condic_contractual` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_labo_motivo_cese
CREATE TABLE IF NOT EXISTS `mp_maes_labo_motivo_cese` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_lengua_materna
CREATE TABLE IF NOT EXISTS `mp_maes_lengua_materna` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_modalidad_trabajo
CREATE TABLE IF NOT EXISTS `mp_maes_modalidad_trabajo` (
  `n_codigo` int(11) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(11) NOT NULL,
  PRIMARY KEY (`n_codigo`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_mpar_tdoc
CREATE TABLE IF NOT EXISTS `mp_maes_mpar_tdoc` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_nivel_educativo
CREATE TABLE IF NOT EXISTS `mp_maes_nivel_educativo` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_notif_tdocumento
CREATE TABLE IF NOT EXISTS `mp_maes_notif_tdocumento` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` char(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_ocupacion
CREATE TABLE IF NOT EXISTS `mp_maes_ocupacion` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_pais
CREATE TABLE IF NOT EXISTS `mp_maes_pais` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=600 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_personal
CREATE TABLE IF NOT EXISTS `mp_maes_personal` (
  `iden_pers` int(11) NOT NULL AUTO_INCREMENT,
  `ndoc_pers` char(8) NOT NULL,
  `appa_pers` char(50) NOT NULL,
  `apma_pers` char(50) NOT NULL,
  `nomb_pers` char(100) NOT NULL,
  `codi_depe` int(3) NOT NULL,
  `codi_carg` int(3) NOT NULL,
  `regi_labo` int(2) NOT NULL,
  `fech_ingr` date NOT NULL,
  `uo_presup` char(50) NOT NULL,
  `esta_pers` int(1) NOT NULL,
  `digi_pers` int(6) NOT NULL,
  `fdig_pers` char(14) NOT NULL,
  PRIMARY KEY (`iden_pers`)
) ENGINE=MyISAM AUTO_INCREMENT=815 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_pertenencia_etnica
CREATE TABLE IF NOT EXISTS `mp_maes_pertenencia_etnica` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(70) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_planilla_clasificadores
CREATE TABLE IF NOT EXISTS `mp_maes_planilla_clasificadores` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(30) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_regimen_laboral
CREATE TABLE IF NOT EXISTS `mp_maes_regimen_laboral` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_sexo
CREATE TABLE IF NOT EXISTS `mp_maes_sexo` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_sorteo
CREATE TABLE IF NOT EXISTS `mp_maes_sorteo` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_sorteo_sedes
CREATE TABLE IF NOT EXISTS `mp_maes_sorteo_sedes` (
  `n_codigo` int(2) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_tdependencia
CREATE TABLE IF NOT EXISTS `mp_maes_tdependencia` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_tdocumento
CREATE TABLE IF NOT EXISTS `mp_maes_tdocumento` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_tipo_domicilio
CREATE TABLE IF NOT EXISTS `mp_maes_tipo_domicilio` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_tipo_entidad
CREATE TABLE IF NOT EXISTS `mp_maes_tipo_entidad` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_tipo_familiar
CREATE TABLE IF NOT EXISTS `mp_maes_tipo_familiar` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_tipo_via
CREATE TABLE IF NOT EXISTS `mp_maes_tipo_via` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_maes_tpdisposicion
CREATE TABLE IF NOT EXISTS `mp_maes_tpdisposicion` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(50) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_movs_item
CREATE TABLE IF NOT EXISTS `mp_movs_item` (
  `codi_movitem` int(11) NOT NULL AUTO_INCREMENT,
  `codi_item` int(3) NOT NULL,
  `codi_loca` int(11) NOT NULL,
  `nro_contr` char(20) NOT NULL DEFAULT '',
  `cicl_fact` char(30) NOT NULL,
  `nro_reci` char(20) NOT NULL,
  `fech_vcto` date NOT NULL,
  `fech_pago` date NOT NULL DEFAULT '0000-00-00',
  `mont_pago` decimal(9,2) NOT NULL,
  `acti_esta` int(1) NOT NULL,
  PRIMARY KEY (`codi_movitem`),
  KEY `codi_item` (`codi_item`),
  KEY `nro_contr` (`nro_contr`),
  KEY `codi_loca` (`codi_loca`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_mpar_asignacion
CREATE TABLE IF NOT EXISTS `mp_mpar_asignacion` (
  `codi_orig` int(6) NOT NULL,
  `codi_dest` int(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_mpar_carpetas
CREATE TABLE IF NOT EXISTS `mp_mpar_carpetas` (
  `codi_mpar` int(9) NOT NULL AUTO_INCREMENT,
  `anno_mpar` char(4) NOT NULL,
  `nume_mpar` int(6) NOT NULL,
  `tdoc_mpar` int(1) NOT NULL,
  `mpar_cbar` char(30) NOT NULL,
  `codi_depe` int(6) NOT NULL,
  `codi_pers` int(6) NOT NULL,
  `fech_asig` char(14) NOT NULL,
  `digi_asig` int(6) NOT NULL,
  `obse_mpar` text NOT NULL,
  `depe_mpar` int(6) NOT NULL,
  `esta_mpar` int(1) NOT NULL,
  `digi_mpar` int(6) NOT NULL,
  `fdig_mpar` char(14) NOT NULL,
  PRIMARY KEY (`codi_mpar`)
) ENGINE=MyISAM AUTO_INCREMENT=2232 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_mpar_despachos
CREATE TABLE IF NOT EXISTS `mp_mpar_despachos` (
  `codi_depe` int(6) NOT NULL,
  `codi_pers` int(6) NOT NULL,
  `flag_desp` int(1) NOT NULL,
  PRIMARY KEY (`codi_depe`,`codi_pers`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_mpar_mpartes
CREATE TABLE IF NOT EXISTS `mp_mpar_mpartes` (
  `codi_depe` int(6) NOT NULL,
  PRIMARY KEY (`codi_depe`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_noticias
CREATE TABLE IF NOT EXISTS `mp_noticias` (
  `codi_noti` int(6) NOT NULL AUTO_INCREMENT,
  `titu_noti` varchar(100) NOT NULL,
  `subt_noti` varchar(200) NOT NULL,
  `cont_noti` text NOT NULL,
  `fech_noti` char(8) NOT NULL,
  `digi_noti` int(6) NOT NULL,
  `fdig_noti` char(14) NOT NULL,
  `esta_noti` int(1) NOT NULL,
  PRIMARY KEY (`codi_noti`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_notif_destinatario_frecuente
CREATE TABLE IF NOT EXISTS `mp_notif_destinatario_frecuente` (
  `iden_dest` int(6) NOT NULL AUTO_INCREMENT,
  `nomb_dest` varchar(100) NOT NULL,
  `dire_dest` varchar(100) NOT NULL,
  `digi_dest` int(6) NOT NULL,
  `fdig_dest` char(14) NOT NULL,
  `esta_dest` int(1) NOT NULL,
  PRIMARY KEY (`iden_dest`)
) ENGINE=MyISAM AUTO_INCREMENT=468 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_notif_documentos
CREATE TABLE IF NOT EXISTS `mp_notif_documentos` (
  `iden_docu` int(6) NOT NULL AUTO_INCREMENT,
  `cbar_docu` char(30) NOT NULL,
  `nume_docu` mediumint(9) NOT NULL,
  `iden_tipo` int(2) NOT NULL,
  `iden_remi` int(6) NOT NULL,
  `depe_remi` int(6) NOT NULL,
  `iden_dest` int(6) NOT NULL,
  `nomb_dest` varchar(100) NOT NULL,
  `dire_dest` varchar(100) NOT NULL,
  `freg_docu` date NOT NULL,
  `fasi_docu` date NOT NULL,
  `fret_docu` date NOT NULL,
  `fdev_docu` date NOT NULL,
  `iden_guia` int(6) NOT NULL,
  `iden_mens` int(6) NOT NULL,
  `hreg_docu` time NOT NULL,
  `iden_guia_reti` int(6) NOT NULL,
  `iden_guia_devo` int(6) NOT NULL,
  `espe_docu` char(1) NOT NULL,
  `remi_docu` char(15) NOT NULL,
  `dest_frec` int(3) NOT NULL,
  `digi_docu` int(6) NOT NULL,
  `fdig_docu` char(14) NOT NULL,
  `esta_docu` int(1) NOT NULL,
  PRIMARY KEY (`iden_docu`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_notif_guia_cabecera
CREATE TABLE IF NOT EXISTS `mp_notif_guia_cabecera` (
  `iden_guia` int(6) NOT NULL AUTO_INCREMENT,
  `nume_guia` smallint(6) NOT NULL,
  `anno_guia` char(4) NOT NULL,
  `fgen_guia` date NOT NULL,
  `ugen_guia` int(6) NOT NULL,
  `iden_mens` int(6) NOT NULL,
  `iden_zona` int(6) NOT NULL,
  `espe_guia` char(1) NOT NULL,
  `iden_fisc` int(6) NOT NULL,
  `iden_dest` int(6) NOT NULL,
  `digi_guia` int(6) NOT NULL,
  `fdig_guia` char(14) NOT NULL,
  `esta_guia` int(1) NOT NULL,
  PRIMARY KEY (`iden_guia`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_notif_guia_detalle
CREATE TABLE IF NOT EXISTS `mp_notif_guia_detalle` (
  `iden_deta` int(6) NOT NULL AUTO_INCREMENT,
  `iden_guia` int(6) NOT NULL,
  `iden_docu` int(6) NOT NULL,
  `fgen_deta` date NOT NULL,
  `ugen_deta` int(6) NOT NULL,
  `orde_deta` int(3) NOT NULL,
  `reas_deta` char(1) NOT NULL,
  `cbar_deta` char(50) NOT NULL,
  `digi_deta` int(6) NOT NULL,
  `fdig_deta` char(14) NOT NULL,
  `esta_deta` int(1) NOT NULL,
  PRIMARY KEY (`iden_deta`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_notif_guia_detalle_temporal
CREATE TABLE IF NOT EXISTS `mp_notif_guia_detalle_temporal` (
  `iden_sesi` char(50) NOT NULL,
  `iden_docu` int(6) NOT NULL,
  `digi_temp` int(6) NOT NULL,
  `fdig_temp` char(8) NOT NULL,
  `esta_temp` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_notif_zonas
CREATE TABLE IF NOT EXISTS `mp_notif_zonas` (
  `iden_zona` int(6) NOT NULL AUTO_INCREMENT,
  `nomb_zona` varchar(100) NOT NULL,
  `ubig_zona` char(6) NOT NULL,
  `desc_zona` varchar(200) NOT NULL,
  `digi_zona` int(6) NOT NULL,
  `fdig_zona` char(14) NOT NULL,
  `esta_zona` int(1) NOT NULL,
  PRIMARY KEY (`iden_zona`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_patr_inve_falt
CREATE TABLE IF NOT EXISTS `mp_patr_inve_falt` (
  `codi_falt` int(6) NOT NULL AUTO_INCREMENT,
  `codi_inve` int(3) NOT NULL,
  `codi_patr` char(15) NOT NULL,
  `nomb_resp` varchar(100) NOT NULL,
  `obse_falt` varchar(100) NOT NULL,
  `digi_falt` int(6) NOT NULL,
  `fdig_falt` char(14) NOT NULL,
  `esta_falt` int(1) NOT NULL,
  PRIMARY KEY (`codi_falt`)
) ENGINE=MyISAM AUTO_INCREMENT=304 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_patr_inve_mant
CREATE TABLE IF NOT EXISTS `mp_patr_inve_mant` (
  `codi_inve` int(3) NOT NULL AUTO_INCREMENT,
  `nomb_inve` varchar(100) NOT NULL,
  `fech_inve` date NOT NULL,
  `acti_inve` int(1) NOT NULL,
  `digi_inve` int(3) NOT NULL,
  `fdig_inve` char(14) NOT NULL,
  `esta_inve` int(1) NOT NULL,
  PRIMARY KEY (`codi_inve`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_patr_inve_regi
CREATE TABLE IF NOT EXISTS `mp_patr_inve_regi` (
  `codi_regi` int(6) NOT NULL AUTO_INCREMENT,
  `codi_inve` int(3) NOT NULL,
  `codi_loca` int(3) NOT NULL,
  `usua_inve` char(12) NOT NULL,
  `codi_patr` char(15) NOT NULL,
  `lati_regi` varchar(20) NOT NULL,
  `long_regi` varchar(20) NOT NULL,
  `obse_regi` varchar(50) NOT NULL,
  `digi_regi` int(6) NOT NULL,
  `fdig_regi` char(14) NOT NULL,
  `esta_regi` int(1) NOT NULL,
  PRIMARY KEY (`codi_regi`)
) ENGINE=MyISAM AUTO_INCREMENT=6299 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_patr_inve_usua_temp
CREATE TABLE IF NOT EXISTS `mp_patr_inve_usua_temp` (
  `codi_usua` int(6) NOT NULL AUTO_INCREMENT,
  `ndoc_usua` char(12) NOT NULL,
  `appa_usua` varchar(50) NOT NULL,
  `apma_usua` varchar(50) NOT NULL,
  `nomb_usua` varchar(50) NOT NULL,
  `digi_usua` int(3) NOT NULL,
  `fdig_usua` char(14) NOT NULL,
  `esta_usua` int(1) NOT NULL,
  PRIMARY KEY (`codi_usua`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_patr_siga
CREATE TABLE IF NOT EXISTS `mp_patr_siga` (
  `codigo_patrimonial` char(12) NOT NULL,
  `codigo_barra` char(7) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` char(30) NOT NULL,
  `nro_serie` char(20) NOT NULL,
  `color` char(20) NOT NULL,
  `nombre` char(10) NOT NULL,
  `medidas` char(20) NOT NULL,
  `fecha_alta` char(22) NOT NULL,
  `ubicac_fisica` varchar(200) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `docum_identidad` char(12) NOT NULL,
  `observaciones` varchar(100) NOT NULL,
  PRIMARY KEY (`codigo_patrimonial`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_patr_siga_0001
CREATE TABLE IF NOT EXISTS `mp_patr_siga_0001` (
  `nombre_sede` varchar(100) NOT NULL,
  `codigo_patrimonial` char(12) NOT NULL,
  `codigo_barra` char(7) NOT NULL,
  `descripción` varchar(200) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` char(30) NOT NULL,
  `nro_serie` char(20) NOT NULL,
  `color` char(20) NOT NULL,
  `estado` char(10) NOT NULL,
  `fecha_alta` char(20) NOT NULL,
  `ubicac_fisica` varchar(200) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `observaciones` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_patr_siga_usua
CREATE TABLE IF NOT EXISTS `mp_patr_siga_usua` (
  `empleado` char(12) NOT NULL,
  `tipo_empleado` char(1) NOT NULL,
  `fecha_ingreso` char(19) NOT NULL,
  `estado_civil` char(1) NOT NULL,
  `sexo_empleado` char(1) NOT NULL,
  `grado_inst` char(1) NOT NULL,
  `estado` char(1) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `fecha_reg` char(19) NOT NULL,
  `cuser_id` char(20) NOT NULL,
  `sec_ejec` char(4) NOT NULL,
  `entidad_externa` char(20) NOT NULL,
  `centro_costo` char(20) NOT NULL,
  `docum_ident` char(12) NOT NULL,
  `codigo_prof` char(3) NOT NULL,
  `flag_interno` char(1) NOT NULL,
  `nro_colegiatura` char(12) NOT NULL,
  `nombre_cc` varchar(50) NOT NULL,
  `nombre_prof` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_personal
CREATE TABLE IF NOT EXISTS `mp_personal` (
  `codi_pers` int(11) NOT NULL AUTO_INCREMENT,
  `pers_apepat` char(30) NOT NULL,
  `pers_apemat` char(30) NOT NULL,
  `pers_nombres` char(30) NOT NULL,
  `pers_fecnac` date NOT NULL,
  `pers_estciv` char(1) NOT NULL,
  `pers_dni` char(8) NOT NULL,
  `pers_lugarnac` char(30) NOT NULL,
  `pers_dire` char(50) NOT NULL,
  `pers_distr` char(30) NOT NULL,
  `pers_refedir` char(50) NOT NULL,
  `pers_tlffijo` char(12) NOT NULL,
  `pers_celu` char(12) NOT NULL,
  `pers_emailper` char(50) NOT NULL,
  `pers_emailinst` char(50) NOT NULL,
  `pers_nomape_per1` char(50) NOT NULL,
  `pers_nrocel_per1` char(12) NOT NULL,
  `pers_nomape_per2` char(50) NOT NULL,
  `pers_nrocel_per2` char(12) NOT NULL,
  `pers_grains` char(30) NOT NULL,
  `pers_prof1` char(30) NOT NULL,
  `pers_prof2` char(30) NOT NULL,
  `pers_nrocole` char(10) NOT NULL,
  `pers_fecing` date NOT NULL,
  `pers_cargo` char(30) NOT NULL,
  `pers_depe` int(11) NOT NULL,
  `pers_reglab` char(20) NOT NULL,
  `pers_plapres` char(30) NOT NULL,
  `pers_conyuge` char(50) NOT NULL,
  `pers_hijo1` char(50) NOT NULL,
  `pers_fechijo1` date NOT NULL,
  `pers_sexohijo1` char(1) NOT NULL,
  `pers_hijo2` char(50) NOT NULL,
  `pers_fechijo2` date NOT NULL,
  `pers_sexohijo2` char(1) NOT NULL,
  `pers_hijo3` char(50) NOT NULL,
  `pers_fechijo3` date NOT NULL,
  `pers_sexohijo3` char(1) NOT NULL,
  `pers_hijo4` char(50) NOT NULL,
  `pers_fechijo4` date NOT NULL,
  `pers_sexohijo4` char(1) NOT NULL,
  `pers_hijo5` char(50) NOT NULL,
  `pers_fechijo5` date NOT NULL,
  `pers_sexohijo5` char(1) NOT NULL,
  `pers_padre` char(50) NOT NULL,
  `pers_padredir` char(50) NOT NULL,
  `pers_madre` char(50) NOT NULL,
  `pers_madredir` char(50) NOT NULL,
  `pers_essalud` char(1) NOT NULL,
  `pers_centroate` char(50) NOT NULL,
  `pers_eps` char(1) NOT NULL,
  `pers_tpsangre` char(10) NOT NULL,
  `pers_alergenf` char(30) NOT NULL,
  `pers_discap` char(50) NOT NULL,
  `pers_conadis` char(1) NOT NULL,
  `pers_otroidi` char(30) NOT NULL,
  `pers_hobfut` char(1) NOT NULL,
  `pers_hobbas` char(1) NOT NULL,
  `pers_hobnat` char(1) NOT NULL,
  `pers_hobpin` char(1) NOT NULL,
  `pers_hobfro` char(1) NOT NULL,
  `pers_hobbai` char(1) NOT NULL,
  `pers_hobcoc` char(1) NOT NULL,
  `pers_otrahab` char(50) NOT NULL,
  `codcargopea` int(11) NOT NULL,
  `meta` char(4) NOT NULL,
  `asignacionfamiliar` tinyint(1) NOT NULL,
  `eps` tinyint(1) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `clas_haberes` char(20) NOT NULL,
  `clas_benefextra` char(20) NOT NULL,
  `clas_bonofiscal` char(20) NOT NULL,
  `clas_go` char(20) NOT NULL,
  `clas_25retardo` char(20) NOT NULL,
  `clas_aguinaldo` char(20) NOT NULL,
  `clas_cafae` char(20) NOT NULL,
  `clas_escolaridad` char(20) NOT NULL,
  `clas_essalud9porc` char(20) NOT NULL,
  `clas_eps225` char(20) NOT NULL,
  `clas_fondopens6porc` char(20) NOT NULL,
  `clas_grati9porc` char(20) NOT NULL,
  PRIMARY KEY (`codi_pers`),
  KEY `pers_dni` (`pers_dni`)
) ENGINE=MyISAM AUTO_INCREMENT=1324 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_plan_escalaremunerativa
CREATE TABLE IF NOT EXISTS `mp_plan_escalaremunerativa` (
  `n_codigo` int(11) NOT NULL AUTO_INCREMENT,
  `esccargo` char(50) NOT NULL,
  `escnivel` char(10) NOT NULL,
  `escdecretoley` int(5) NOT NULL,
  `escremunerabasica` decimal(9,2) NOT NULL,
  `escbonificajurisdiccional` decimal(9,2) NOT NULL,
  `escdietas` decimal(9,2) NOT NULL,
  `escaguinaldo` decimal(9,2) NOT NULL,
  `escbenefextra` decimal(9,2) NOT NULL,
  `esccafae` decimal(9,2) NOT NULL,
  `escgastosope` decimal(9,2) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_plan_plazasvacantes
CREATE TABLE IF NOT EXISTS `mp_plan_plazasvacantes` (
  `autogen` int(11) NOT NULL AUTO_INCREMENT,
  `mesplaza` int(2) NOT NULL,
  `anoplaza` int(4) NOT NULL,
  `meta` char(4) NOT NULL,
  `codcargo` int(11) NOT NULL,
  `nroplazas` int(2) NOT NULL,
  PRIMARY KEY (`autogen`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_sorteo_participante
CREATE TABLE IF NOT EXISTS `mp_sorteo_participante` (
  `codi_part` int(4) NOT NULL AUTO_INCREMENT,
  `ndni_part` char(8) NOT NULL,
  `nomb_part` varchar(100) NOT NULL,
  `tele_part` char(9) NOT NULL,
  `codi_sede` int(3) NOT NULL,
  `gano_part` int(1) NOT NULL,
  `fdig_part` char(14) NOT NULL,
  `esta_part` int(1) NOT NULL,
  PRIMARY KEY (`codi_part`)
) ENGINE=MyISAM AUTO_INCREMENT=386 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_tipo_via
CREATE TABLE IF NOT EXISTS `mp_tipo_via` (
  `n_codigo` int(3) NOT NULL AUTO_INCREMENT,
  `x_nombre` varchar(100) NOT NULL,
  `n_estado` int(1) NOT NULL,
  PRIMARY KEY (`n_codigo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_ubigeo
CREATE TABLE IF NOT EXISTS `mp_ubigeo` (
  `id_ubigeo` int(6) NOT NULL,
  `ubigeo_reniec` char(6) NOT NULL,
  `ubigeo_inei` char(6) NOT NULL,
  `departamento_inei` char(2) NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `provincia_inei` varchar(100) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `distrito` varchar(100) NOT NULL,
  `region` varchar(100) NOT NULL,
  `macroregion_inei` varchar(100) NOT NULL,
  `macroregion_minsa` varchar(100) NOT NULL,
  `iso_3166_2` char(6) NOT NULL,
  `fips` char(2) NOT NULL,
  `superficie` float NOT NULL,
  `altitud` float NOT NULL,
  `latitud` char(20) NOT NULL,
  `longitud` char(20) NOT NULL,
  PRIMARY KEY (`id_ubigeo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_visi_registro
CREATE TABLE IF NOT EXISTS `mp_visi_registro` (
  `iden_visi` int(6) NOT NULL AUTO_INCREMENT,
  `tdoc_visi` int(2) NOT NULL,
  `ndoc_visi` char(20) NOT NULL,
  `nomb_visi` varchar(100) NOT NULL,
  `appa_visi` varchar(100) NOT NULL,
  `apma_visi` varchar(100) NOT NULL,
  `fech_visi` date NOT NULL,
  `ingr_visi` time NOT NULL,
  `sali_visi` time NOT NULL,
  `iden_loca` int(3) NOT NULL,
  `iden_depe` int(6) NOT NULL,
  `iden_pers` int(6) NOT NULL,
  `piso_visi` char(2) NOT NULL,
  `iden_empr` int(6) NOT NULL,
  `obse_visi` varchar(200) NOT NULL,
  `digi_visi` int(6) NOT NULL,
  `fdig_visi` char(14) NOT NULL,
  `esta_visi` int(1) NOT NULL,
  PRIMARY KEY (`iden_visi`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.mp_voluntariado
CREATE TABLE IF NOT EXISTS `mp_voluntariado` (
  `codi_volu` int(6) NOT NULL AUTO_INCREMENT,
  `nomb_volu` char(200) NOT NULL,
  `docu_volu` char(8) NOT NULL,
  `mail_volu` char(100) NOT NULL,
  `celu_volu` char(20) NOT NULL,
  `coor_volu` char(200) NOT NULL,
  `carr_volu` char(200) NOT NULL,
  `depe_volu` char(200) NOT NULL,
  `anno_volu` char(4) NOT NULL,
  `flag_volu` int(1) NOT NULL,
  `habi_impr` int(1) NOT NULL,
  `esta_impr` int(1) NOT NULL,
  `digi_volu` int(6) NOT NULL,
  `fdig_volu` char(14) NOT NULL,
  `esta_volu` int(1) NOT NULL,
  PRIMARY KEY (`codi_volu`)
) ENGINE=MyISAM AUTO_INCREMENT=1662 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para procedimiento mpfnarequipa_siga.obtener_datos_inventario
DELIMITER //
CREATE PROCEDURE `obtener_datos_inventario`(OUT `codi_inve` INT(3), OUT `fech_inve` CHAR(14), OUT `nomb_inve` VARCHAR(50))
select codi_inve,fech_inve,nomb_inve from mp_inve_mant where acti_inve='1' AND esta_inve='1' order by fech_inve limit 1//
DELIMITER ;

-- Volcando estructura para tabla mpfnarequipa_siga.perfiles
CREATE TABLE IF NOT EXISTS `perfiles` (
  `id_perfil` int(11) NOT NULL AUTO_INCREMENT,
  `perfil` varchar(50) NOT NULL,
  `id_nivel_doc` int(11) NOT NULL,
  PRIMARY KEY (`id_perfil`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.temp_actualizacion_pers
CREATE TABLE IF NOT EXISTS `temp_actualizacion_pers` (
  `ndoc_pers` char(12) NOT NULL,
  `iden_nafp` int(11) DEFAULT NULL,
  `cusp_pers` char(20) DEFAULT NULL,
  `iden_depe` int(11) DEFAULT NULL,
  `iden_rlab` int(11) DEFAULT NULL,
  `iden_carg` int(11) DEFAULT NULL,
  `fing_pers` char(8) DEFAULT NULL,
  `iden_pres` int(11) DEFAULT NULL,
  `essa_pers` int(11) DEFAULT NULL,
  `teps_pers` int(11) DEFAULT NULL,
  `acti_pers` int(11) DEFAULT NULL,
  PRIMARY KEY (`ndoc_pers`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.temp_admi_pers
CREATE TABLE IF NOT EXISTS `temp_admi_pers` (
  `ndoc_pers` char(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iden_sexo` int(11) NOT NULL,
  `appa_pers` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apma_pers` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomb_pers` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iden_nafp` int(11) NOT NULL,
  `cusp_pers` char(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iden_depe` int(11) NOT NULL,
  `iden_rlab` int(11) NOT NULL,
  `iden_carg` int(11) NOT NULL,
  `fing_pers` char(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fnac_pers` char(12) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.temp_cuentas
CREATE TABLE IF NOT EXISTS `temp_cuentas` (
  `docu` char(20) NOT NULL,
  `pate` varchar(100) NOT NULL,
  `mate` varchar(100) NOT NULL,
  `nomb` varchar(100) NOT NULL,
  `carg` varchar(50) NOT NULL,
  `regi` char(10) NOT NULL,
  `fisi` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla mpfnarequipa_siga.temp_depe_loca
CREATE TABLE IF NOT EXISTS `temp_depe_loca` (
  `codi_depe` int(11) NOT NULL,
  `codi_loca` int(11) DEFAULT NULL,
  PRIMARY KEY (`codi_depe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
