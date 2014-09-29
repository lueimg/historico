/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50617
Source Host           : 127.0.0.1:3306
Source Database       : webpsi_criticos

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2014-09-29 11:59:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for gestion_rutina_manual_provision
-- ----------------------------
DROP TABLE IF EXISTS `gestion_rutina_manual_provision`;
CREATE TABLE `gestion_rutina_manual_provision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_gestion` int(11) DEFAULT NULL,
  `origen` varchar(13) NOT NULL DEFAULT '',
  `horas_pedido` decimal(8,2) DEFAULT NULL,
  `fecha_Reg` datetime DEFAULT NULL,
  `ciudad` varchar(5) DEFAULT NULL,
  `codigo_req` int(15) DEFAULT NULL,
  `codigo_del_cliente` int(15) DEFAULT NULL,
  `fono1` varchar(8) DEFAULT NULL,
  `telefono` varbinary(22) DEFAULT NULL,
  `mdf` varchar(5) DEFAULT NULL,
  `obs_dev` varchar(200) DEFAULT NULL,
  `codigosegmento` varchar(2) DEFAULT NULL,
  `estacion` varchar(20) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `distrito` varchar(30) DEFAULT NULL,
  `nomcliente` varchar(80) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `veloc_adsl` varchar(20) DEFAULT NULL,
  `servicio` varchar(35) DEFAULT NULL,
  `tipo_motivo` varchar(25) DEFAULT NULL,
  `tot_aver_cab` varchar(2) DEFAULT NULL,
  `tot_aver_cob` varchar(2) DEFAULT NULL,
  `tot_averias` varchar(2) DEFAULT NULL,
  `fftt` varchar(20) DEFAULT NULL,
  `llave` varchar(25) DEFAULT NULL,
  `dir_terminal` varchar(200) DEFAULT NULL,
  `fonos_contacto` varchar(40) DEFAULT NULL,
  `contrata` varchar(20) DEFAULT NULL,
  `zonal` varchar(5) DEFAULT NULL,
  `wu_nagendas` varchar(2) DEFAULT NULL,
  `wu_nmovimient` varchar(2) DEFAULT NULL,
  `wu_fecha_ult_age` datetime DEFAULT NULL,
  `tot_llam_tec` varchar(4) DEFAULT NULL,
  `tot_llam_seg` varchar(4) DEFAULT NULL,
  `llamadastec15d` varchar(10) DEFAULT NULL,
  `llamadastec30d` varchar(10) DEFAULT NULL,
  `quiebre` varchar(20) DEFAULT NULL,
  `lejano` varchar(20) DEFAULT NULL,
  `des_distrito` varchar(70) DEFAULT NULL,
  `eecc_zon` varchar(20) DEFAULT NULL,
  `zona_movuno` varchar(30) DEFAULT NULL,
  `paquete` varchar(60) DEFAULT NULL,
  `data_multip` varchar(50) DEFAULT NULL,
  `aver_m1` varchar(5) DEFAULT NULL,
  `fecha_data_fuente` datetime DEFAULT NULL,
  `telefono_codclientecms` varchar(20) DEFAULT NULL,
  `rango_dias` varchar(6) DEFAULT NULL,
  `sms1` varchar(200) DEFAULT NULL,
  `sms2` varchar(200) DEFAULT NULL,
  `area2` varchar(20) DEFAULT NULL,
  `tipo_actuacion` varchar(40) DEFAULT NULL,
  `eecc_final` varchar(30) DEFAULT NULL,
  `microzona` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
