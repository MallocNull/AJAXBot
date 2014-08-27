SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT 'admin',
  `password` varchar(256) NOT NULL DEFAULT '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',
  `accountaccess` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `autonomous` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `startday` int(11) NOT NULL DEFAULT '-1',
  `starttime` bigint(20) NOT NULL DEFAULT '-1',
  `periodicity` bigint(20) NOT NULL DEFAULT '1440',
  `randomness` bigint(20) NOT NULL DEFAULT '0',
  `respid` int(11) NOT NULL,
  `parameters` longtext NOT NULL,
  `autolink` int(11) NOT NULL DEFAULT '-1',
  `linkrespond` tinyint(1) NOT NULL DEFAULT '1',
  `timeout` bigint(20) NOT NULL,
  `torandomness` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `friendlyname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL DEFAULT '1',
  `cooldown` bigint(20) NOT NULL DEFAULT '300',
  `parsechatbot` tinyint(1) NOT NULL DEFAULT '0',
  `username` varchar(256) NOT NULL DEFAULT 'BOTMAN360',
  `name` varchar(256) NOT NULL DEFAULT 'AJAX Bot',
  `timezone` varchar(6) NOT NULL DEFAULT '+00:00',
  `dst` tinyint(1) NOT NULL DEFAULT '0',
  `buffersize` int(11) NOT NULL DEFAULT '50',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` varchar(128) NOT NULL,
  `msg` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `navigate` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `findtype` int(11) NOT NULL,
  `locator` varchar(256) NOT NULL,
  `action` int(11) NOT NULL,
  `parameter` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conditions` longtext NOT NULL,
  `respid` int(11) NOT NULL,
  `parameters` longtext,
  `cooldown` bigint(20) NOT NULL DEFAULT '-1',
  `independent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `resptypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `friendlyname` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `updater` (
  `id` int(11) NOT NULL DEFAULT '1',
  `heartbeat` varchar(128) NOT NULL DEFAULT '0',
  `responses` tinyint(1) NOT NULL DEFAULT '0',
  `autonomous` tinyint(1) NOT NULL DEFAULT '0',
  `config` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `admin` () VALUES ()
INSERT INTO `config` () VALUES ()
INSERT INTO `updater` () VALUES ()