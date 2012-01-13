/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
  `accountid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `crdate` datetime NOT NULL,
  `lugid` bigint(20) unsigned DEFAULT NULL,
  `acl` varchar(255) DEFAULT 'client',
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`accountid`),
  UNIQUE KEY `uk_account_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_domain`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_domain` (
  `domainid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `crdate` datetime NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`domainid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_page`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_page` (
  `pageid` int(11) NOT NULL AUTO_INCREMENT,
  `domainid` int(11) NOT NULL,
  `parentpageid` int(11) DEFAULT NULL,
  `pagetypeid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Pagetitle',
  `content` text COMMENT 'non-binary content',
  `crdate` datetime NOT NULL,
  `keywords` text COMMENT 'Keywords for this page.',
  `navorder` int(11) DEFAULT '100',
  `acl` varchar(100) DEFAULT NULL,
  `alias` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`pageid`),
  KEY `fk_content_page_pagetype` (`pagetypeid`),
  KEY `fk_content_page_domain` (`domainid`),
  CONSTRAINT `fk_content_page_domain` FOREIGN KEY (`domainid`) REFERENCES `content_domain` (`domainid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_page_pagetype` FOREIGN KEY (`pagetypeid`) REFERENCES `content_pagetype` (`pagetypeid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='Pages';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_pagetype`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_pagetype` (
  `pagetypeid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `plugin` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`pagetypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domain_artikel`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domain_artikel` (
  `domainid` int(11) NOT NULL,
  `artikelid` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`domainid`,`artikelid`),
  KEY `fk_da_artikel` (`artikelid`),
  CONSTRAINT `fk_da_artikel` FOREIGN KEY (`artikelid`) REFERENCES `event_artikel` (`artikelid`),
  CONSTRAINT `fk_da_domain` FOREIGN KEY (`domainid`) REFERENCES `content_domain` (`domainid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domain_event`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domain_event` (
  `domainid` int(11) NOT NULL,
  `eventid` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`domainid`,`eventid`),
  KEY `fk_domain_event_domainid` (`domainid`),
  KEY `fk_domain_event_eventid` (`eventid`),
  CONSTRAINT `fk_domain_event_domainid` FOREIGN KEY (`domainid`) REFERENCES `content_domain` (`domainid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_domain_event_eventid` FOREIGN KEY (`eventid`) REFERENCES `event_event` (`eventid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_account_artikel`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_account_artikel` (
  `accountartikelid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `accountid` bigint(20) unsigned NOT NULL,
  `artikelid` bigint(20) unsigned NOT NULL,
  `anzahl` int(11) NOT NULL,
  `crdate` datetime DEFAULT NULL,
  `groesse` varchar(10) DEFAULT NULL,
  `bezahlt` datetime DEFAULT NULL,
  PRIMARY KEY (`accountartikelid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_anmeldung`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_anmeldung` (
  `anmeldungid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `accountid` bigint(20) unsigned NOT NULL,
  `lugid` bigint(20) unsigned NOT NULL,
  `vorname` varchar(30) NOT NULL,
  `nachname` varchar(30) NOT NULL,
  `strasse` varchar(60) NOT NULL,
  `hausnr` varchar(10) NOT NULL,
  `plz` varchar(10) NOT NULL,
  `land` char(2) NOT NULL DEFAULT 'DE',
  `email` varchar(255) NOT NULL,
  `gebdat` date NOT NULL,
  `landid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `vegetarier` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `arrival` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `ankunft` varchar(255) NOT NULL DEFAULT '',
  `abfahrt` varchar(255) NOT NULL DEFAULT '',
  `bemerkung` text,
  `admin_bemerkung` text,
  `barcode` varchar(20) DEFAULT NULL,
  `ort` varchar(60) NOT NULL,
  `crdate` datetime DEFAULT NULL,
  PRIMARY KEY (`anmeldungid`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_anmeldung_event`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_anmeldung_event` (
  `anmeldungid` bigint(20) unsigned NOT NULL,
  `eventid` bigint(20) unsigned NOT NULL,
  `accountid` bigint(20) unsigned DEFAULT NULL,
  `bezahlt` datetime DEFAULT NULL,
  PRIMARY KEY (`anmeldungid`,`eventid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_artikel`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_artikel` (
  `artikelid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `beschreibung` text,
  `kaufab` datetime DEFAULT NULL,
  `kaufbis` datetime DEFAULT NULL,
  `preis` decimal(5,2) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `groessen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`artikelid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_event`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_event` (
  `eventid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `anfang` datetime NOT NULL,
  `ende` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `beschreibung` text,
  `buchanfang` datetime NOT NULL,
  `buchende` datetime NOT NULL,
  `quota` int(11) DEFAULT NULL,
  `charge` float DEFAULT NULL,
  `parent` bigint(20) unsigned DEFAULT NULL,
  `onlythisingroup` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `alwaysallowedinthisgroup` tinyint(4) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `barzahlung` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventid`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_land`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_land` (
  `landid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `crdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`landid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_lug`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_lug` (
  `lugid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `abk` varchar(10) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `crdate` datetime NOT NULL,
  PRIMARY KEY (`lugid`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_programm`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_programm` (
  `programmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eventid` int(11) NOT NULL DEFAULT '0',
  `titel` varchar(255) DEFAULT NULL,
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ende` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `beschreibung` text,
  `kategorie` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`programmid`)
) ENGINE=MyISAM AUTO_INCREMENT=181 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_zahlung`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_zahlung` (
  `zahlungid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `accountid` bigint(20) unsigned NOT NULL,
  `crdate` datetime NOT NULL,
  `valutadate` datetime NOT NULL,
  `valuta` decimal(5,2) DEFAULT NULL,
  `txt` text,
  PRIMARY KEY (`zahlungid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logins`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logins` (
  `loginid` int(11) NOT NULL AUTO_INCREMENT,
  `accountid` bigint(20) unsigned NOT NULL,
  `logintime` datetime NOT NULL,
  PRIMARY KEY (`loginid`) USING BTREE,
  KEY `fk_logins_accountid_account` (`accountid`),
  CONSTRAINT `fk_logins_accountid_account` FOREIGN KEY (`accountid`) REFERENCES `account` (`accountid`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news_cat`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_cat` (
  `catid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `pic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`catid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news_eintrag`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_eintrag` (
  `eintragid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL,
  `catid` bigint(20) unsigned NOT NULL,
  `short` text NOT NULL,
  `txt` text NOT NULL,
  `crdate` datetime NOT NULL,
  `author` varchar(50) NOT NULL,
  `domainid` int(11) NOT NULL,
  `accountid` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`eintragid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sponsoren`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sponsoren` (
  `sponsorenid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `crdate` datetime NOT NULL,
  PRIMARY KEY (`sponsorenid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

INSERT INTO event_lug (lugid,name,abk,url,crdate) VALUES (1,'Linux User Group Flensburg e.V.','LUGFL','http://www.lugfl.de',NOW());
INSERT INTO account (username,passwd,acl,crdate) VALUES ('admin',MD5('admin'),'admin',NOW());
INSERT INTO `content_pagetype` VALUES (1,'text/html','Text_Html'),(2,'text/wiki','Text_Wiki'),(3,'plugin/login','Plugin_Login'),(4,'plugin/events','Plugin_Events'),(5,'plugin/news','Plugin_News'),(6,'plugin/mycamp-rechnung','Plugin_MyCamp_Rechnung');


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

