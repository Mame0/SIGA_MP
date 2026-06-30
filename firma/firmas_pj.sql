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

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
