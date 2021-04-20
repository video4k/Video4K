-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.4.18-MariaDB - mariadb.org binary distribution
-- Server Betriebssystem:        Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Exportiere Datenbank Struktur für video4k
DROP DATABASE IF EXISTS `video4k`;
CREATE DATABASE IF NOT EXISTS `video4k` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `video4k`;

-- Exportiere Struktur von Tabelle video4k.actors
DROP TABLE IF EXISTS `actors`;
CREATE TABLE IF NOT EXISTS `actors` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.actors: ~0 rows (ungefähr)
DELETE FROM `actors`;
/*!40000 ALTER TABLE `actors` DISABLE KEYS */;
/*!40000 ALTER TABLE `actors` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.actors_index
DROP TABLE IF EXISTS `actors_index`;
CREATE TABLE IF NOT EXISTS `actors_index` (
  `EID` mediumint(8) unsigned NOT NULL,
  `AID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`EID`,`AID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.actors_index: ~0 rows (ungefähr)
DELETE FROM `actors_index`;
/*!40000 ALTER TABLE `actors_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `actors_index` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.api_query
DROP TABLE IF EXISTS `api_query`;
CREATE TABLE IF NOT EXISTS `api_query` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UID` mediumint(8) unsigned NOT NULL,
  `tag` varchar(255) NOT NULL,
  `links` text NOT NULL,
  `tries` tinyint(1) unsigned DEFAULT 0,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.api_query: ~0 rows (ungefähr)
DELETE FROM `api_query`;
/*!40000 ALTER TABLE `api_query` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_query` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.bulk_query
DROP TABLE IF EXISTS `bulk_query`;
CREATE TABLE IF NOT EXISTS `bulk_query` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UID` mediumint(8) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `BID` varchar(32) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `BID` (`BID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.bulk_query: ~0 rows (ungefähr)
DELETE FROM `bulk_query`;
/*!40000 ALTER TABLE `bulk_query` DISABLE KEYS */;
/*!40000 ALTER TABLE `bulk_query` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.contact
DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `mail` varchar(30) DEFAULT NULL,
  `subject` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.contact: ~0 rows (ungefähr)
DELETE FROM `contact`;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.crawler_query
DROP TABLE IF EXISTS `crawler_query`;
CREATE TABLE IF NOT EXISTS `crawler_query` (
  `ID` mediumint(8) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `priority` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.crawler_query: ~0 rows (ungefähr)
DELETE FROM `crawler_query`;
/*!40000 ALTER TABLE `crawler_query` DISABLE KEYS */;
/*!40000 ALTER TABLE `crawler_query` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.directors
DROP TABLE IF EXISTS `directors`;
CREATE TABLE IF NOT EXISTS `directors` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.directors: ~0 rows (ungefähr)
DELETE FROM `directors`;
/*!40000 ALTER TABLE `directors` DISABLE KEYS */;
/*!40000 ALTER TABLE `directors` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.directors_index
DROP TABLE IF EXISTS `directors_index`;
CREATE TABLE IF NOT EXISTS `directors_index` (
  `EID` mediumint(8) unsigned NOT NULL,
  `DID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`EID`,`DID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.directors_index: ~0 rows (ungefähr)
DELETE FROM `directors_index`;
/*!40000 ALTER TABLE `directors_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `directors_index` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.directory
DROP TABLE IF EXISTS `directory`;
CREATE TABLE IF NOT EXISTS `directory` (
  `ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `MID` mediumint(8) unsigned NOT NULL,
  `type` tinyint(1) unsigned DEFAULT NULL,
  `name_de` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `year` smallint(4) unsigned DEFAULT 0,
  `released` int(10) unsigned DEFAULT 0,
  `rating` decimal(3,0) unsigned DEFAULT NULL,
  `duration` smallint(4) unsigned DEFAULT 0,
  `cover` varchar(32) DEFAULT NULL,
  `plot_de` text DEFAULT NULL,
  `plot_en` text DEFAULT NULL,
  `trailer_de` varchar(255) DEFAULT NULL,
  `trailer_en` varchar(255) DEFAULT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `MID` (`MID`),
  KEY `type` (`type`),
  KEY `name_de` (`name_de`),
  KEY `name_en` (`name_en`),
  KEY `rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.directory: ~0 rows (ungefähr)
DELETE FROM `directory`;
/*!40000 ALTER TABLE `directory` DISABLE KEYS */;
/*!40000 ALTER TABLE `directory` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.genres
DROP TABLE IF EXISTS `genres`;
CREATE TABLE IF NOT EXISTS `genres` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9461 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.genres: ~28 rows (ungefähr)
DELETE FROM `genres`;
/*!40000 ALTER TABLE `genres` DISABLE KEYS */;
INSERT INTO `genres` (`ID`, `name`) VALUES
	(6, 'Action'),
	(4773, 'Adult'),
	(42, 'Adventure'),
	(26, 'Animation'),
	(851, 'Biography'),
	(1, 'Comedy'),
	(36, 'Crime'),
	(2, 'Documentary'),
	(7, 'Drama'),
	(19, 'Family'),
	(28, 'Fantasy'),
	(9460, 'Film-Noir'),
	(105, 'Game-Show'),
	(31, 'History'),
	(87, 'Horror'),
	(79, 'Music'),
	(443, 'Musical'),
	(8, 'Mystery'),
	(5888, 'News'),
	(3, 'Reality-TV'),
	(24, 'Romance'),
	(14, 'Sci-Fi'),
	(2385, 'Short'),
	(94, 'Sport'),
	(1136, 'Talk-Show'),
	(9, 'Thriller'),
	(75, 'War'),
	(336, 'Western');
/*!40000 ALTER TABLE `genres` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.genres_index
DROP TABLE IF EXISTS `genres_index`;
CREATE TABLE IF NOT EXISTS `genres_index` (
  `EID` mediumint(8) unsigned NOT NULL,
  `GID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`EID`,`GID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.genres_index: ~0 rows (ungefähr)
DELETE FROM `genres_index`;
/*!40000 ALTER TABLE `genres_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `genres_index` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.hoster
DROP TABLE IF EXISTS `hoster`;
CREATE TABLE IF NOT EXISTS `hoster` (
  `ID` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `URL` varchar(255) NOT NULL,
  `removestring` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `active` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `priority` tinyint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `URL` (`URL`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.hoster: ~14 rows (ungefähr)
DELETE FROM `hoster`;
/*!40000 ALTER TABLE `hoster` DISABLE KEYS */;
INSERT INTO `hoster` (`ID`, `name`, `URL`, `removestring`, `icon`, `type`, `active`, `priority`) VALUES
	(1, 'StreamCloud', 'http://streamcloud.eu/', 'Datei nicht gefunden', 'http://streamcloud.eu/favicon.ico', 0, 1, 0),
	(3, 'NowVideo', 'http://www.nowvideo.sx/', 'file no longer exists', 'http://www.nowvideo.sx/favicon.ico', 0, 1, 0),
	(4, 'EcoStream', 'http://www.ecostream.tv/', 'File not found', NULL, 0, 1, 0),
	(5, 'VIVO', 'http://vivo.sx/', 'The requested file could not be found', 'http://vivo.sx/favicon.ico', 0, 1, 0),
	(6, 'MovShare', 'http://www.movshare.net/', 'This file no longer exists on our servers', 'http://www.movshare.net/images/favicon.ico', 0, 1, 0),
	(7, 'PromptFile', 'http://www.promptfile.com/', 'The file you requested does not exist', 'http://www.promptfile.com/favicon.ico', 0, 1, 0),
	(8, 'VidBull', 'http://vidbull.com/', 'vidbull.com\\/.html', 'http://www.vidbull.com/favicon.ico', 0, 1, 0),
	(9, 'MooShare', 'http://mooshare.biz/', 'Video Not Found', 'http://mooshare.biz/favicon.ico', 0, 1, 0),
	(10, 'FileNuke', 'http://filenuke.com/', 'file.mp4 \\(367 MB\\)', NULL, 0, 1, 0),
	(11, 'OBOOM', 'https://www.oboom.com/', NULL, 'https://www.oboom.com/favicon.ico', 1, 1, 0),
	(13, 'Share-Online', 'http://www.share-online.biz/', 'Download nicht möglich', 'http://www.share-online.biz/favicon.ico', 1, 1, 0),
	(14, 'Uploadable', 'http://www.uploadable.ch/', 'File not available', 'http://www.uploadable.ch/favicon.ico', 1, 1, 0),
	(15, 'RapidGator', 'http://rapidgator.net/', 'File not found', 'http://rapidgator.net/favicon.ico', 1, 1, 0),
	(16, 'PowerWatch', 'http://powerwatch.pw/', 'File Not Found', 'http://powerwatch.pw/favicon.ico', 0, 1, 0);
/*!40000 ALTER TABLE `hoster` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.languages
DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `ID` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `symbol` varchar(5) NOT NULL,
  `text` varchar(64) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `symbol` (`symbol`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.languages: ~13 rows (ungefähr)
DELETE FROM `languages`;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` (`ID`, `symbol`, `text`) VALUES
	(1, 'DE', 'Deutsch'),
	(2, 'EN', 'English'),
	(3, 'ES', 'Español'),
	(4, 'FR', 'Français'),
	(5, 'GR', 'ελληνικά'),
	(6, 'HR', 'Hrvatski'),
	(7, 'IT', 'Italiano'),
	(8, 'JP', '日本人'),
	(9, 'NL', 'Nederlands'),
	(10, 'RU', 'русский'),
	(11, 'TR', 'Türkçe'),
	(12, 'EN-DE', 'English (DE Sub)'),
	(13, 'JP-DE', 'Japan (DE Sub)');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.languages_index
DROP TABLE IF EXISTS `languages_index`;
CREATE TABLE IF NOT EXISTS `languages_index` (
  `EID` mediumint(8) unsigned NOT NULL,
  `LID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`EID`,`LID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.languages_index: ~0 rows (ungefähr)
DELETE FROM `languages_index`;
/*!40000 ALTER TABLE `languages_index` DISABLE KEYS */;
/*!40000 ALTER TABLE `languages_index` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.links
DROP TABLE IF EXISTS `links`;
CREATE TABLE IF NOT EXISTS `links` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `EID` mediumint(8) unsigned NOT NULL,
  `UID` mediumint(8) unsigned NOT NULL,
  `HID` smallint(4) unsigned NOT NULL,
  `URL` varchar(255) NOT NULL,
  `language` smallint(4) unsigned NOT NULL,
  `season` smallint(4) unsigned DEFAULT 1,
  `episode` smallint(4) unsigned DEFAULT 0,
  `quality` tinyint(1) NOT NULL DEFAULT -1,
  `active` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `URL` (`URL`),
  KEY `EID` (`EID`),
  KEY `language` (`language`),
  KEY `active` (`active`),
  KEY `HID` (`HID`),
  KEY `season` (`season`),
  KEY `episode` (`episode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.links: ~0 rows (ungefähr)
DELETE FROM `links`;
/*!40000 ALTER TABLE `links` DISABLE KEYS */;
/*!40000 ALTER TABLE `links` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.releases
DROP TABLE IF EXISTS `releases`;
CREATE TABLE IF NOT EXISTS `releases` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `IMDB` varchar(9) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.releases: ~0 rows (ungefähr)
DELETE FROM `releases`;
/*!40000 ALTER TABLE `releases` DISABLE KEYS */;
/*!40000 ALTER TABLE `releases` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `ID` mediumint(8) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`,`name`),
  KEY `value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.sessions: ~0 rows (ungefähr)
DELETE FROM `sessions`;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `DOMAIN` varchar(20) NOT NULL,
  `TITLE` varchar(20) NOT NULL,
  `CAPTCHA_KEY_PRIVATE` varchar(40) NOT NULL,
  `CAPTCHA_KEY_PUBLIC` varchar(40) NOT NULL,
  `STATIC_URL` varchar(255) NOT NULL,
  `ANALYTICS_ID` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.settings: ~0 rows (ungefähr)
DELETE FROM `settings`;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`DOMAIN`, `TITLE`, `CAPTCHA_KEY_PRIVATE`, `CAPTCHA_KEY_PUBLIC`, `STATIC_URL`, `ANALYTICS_ID`) VALUES
	('video4k.to', 'video4K.to', '', '', 'static.video4k.to', '');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.updates
DROP TABLE IF EXISTS `updates`;
CREATE TABLE IF NOT EXISTS `updates` (
  `EID` mediumint(8) unsigned NOT NULL,
  `LID` smallint(4) unsigned NOT NULL,
  `season` smallint(4) NOT NULL DEFAULT -1,
  `episode` smallint(4) NOT NULL DEFAULT -1,
  `datestamp` date NOT NULL,
  PRIMARY KEY (`EID`,`LID`,`datestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.updates: ~0 rows (ungefähr)
DELETE FROM `updates`;
/*!40000 ALTER TABLE `updates` DISABLE KEYS */;
/*!40000 ALTER TABLE `updates` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle video4k.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL,
  `password` varchar(32) NOT NULL,
  `api` varchar(32) DEFAULT NULL,
  `rights` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `signup` int(10) unsigned NOT NULL,
  `access` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `api` (`api`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle video4k.users: ~0 rows (ungefähr)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
