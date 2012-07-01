-- Adminer 3.3.4 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `control_gastos` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `control_gastos`;

CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `creation_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`user_id`),
  CONSTRAINT `account_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `movement` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `account_id` int(11) NOT NULL,
  `amount` decimal(9,2) NOT NULL,
  `date` date NOT NULL,
  `type` tinyint(1) NOT NULL,
  `concept` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cuenta_id` (`account_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `movement_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movement_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `amount` decimal(9,2) unsigned NOT NULL,
  `concept` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `pass` char(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `name` varchar(100) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2012-06-30 23:26:17
