-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2015 at 09:26 AM
-- Server version: 5.6.21-70.1-log
-- PHP Version: 5.3.10-1ubuntu3.15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jezzy`
--

-- --------------------------------------------------------

--
-- Table structure for table `catalog`
--

CREATE TABLE IF NOT EXISTS `catalog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_parent` bigint(20) DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `latin` varchar(256) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=Innodb  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `catalog`
--

INSERT INTO `catalog` (`id`, `id_parent`, `name`, `latin`, `hidden`) VALUES
(1, NULL, 'Мобильные', 'mobilnie', 0),
(2, NULL, 'Компьютерные', 'komputernie', 0),
(3, 2, 'Логические', 'logicheskie', 0),
(4, 2, 'Аркадные', 'arkadnyie', 0),
(5, 2, 'Стрелялки', 'strelyalki', 0),
(6, 2, 'Cимуляторы', 'cimulyatoryi', 0),
(7, 2, 'Настольные', 'nastolnyie', 0),
(8, 2, 'Детские', 'detskie', 0),
(9, 2, 'Я ищу', 'ya-ischu', 0);

-- --------------------------------------------------------

--
-- Table structure for table `catalog_games`
--

CREATE TABLE IF NOT EXISTS `catalog_games` (
  `id_catalog` bigint(20) NOT NULL,
  `id_game` bigint(20) NOT NULL
) ENGINE=Innodb DEFAULT CHARSET=utf8;

--
-- Dumping data for table `catalog_games`
--

INSERT INTO `catalog_games` (`id_catalog`, `id_game`) VALUES
(3, 1),
(5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rate` bigint(20) DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `lead` varchar(512) NOT NULL,
  `desc` text NOT NULL,
  `latin` varchar(256) NOT NULL,
  `thumb` varchar(256) NOT NULL,
  `picture` varchar(256) NOT NULL,
  `size` bigint(20) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `identity` varchar(512) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=Innodb DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_game` bigint(20) NOT NULL,
  `url` varchar(256) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=Innodb DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `screens`
--

CREATE TABLE IF NOT EXISTS `screens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_game` bigint(20) NOT NULL,
  `thumb` varchar(256) DEFAULT NULL,
  `picture` varchar(256) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=Innodb DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
