-- MySQL dump 10.9
--
-- Host: localhost    Database: 01_lc2008_dev
-- ------------------------------------------------------
-- Server version	4.1.11-Debian_4sarge7-log


--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `accountid` bigint(20) unsigned NOT NULL auto_increment,
  `username` varchar(20) NOT NULL default '',
  `passwd` varchar(32) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `crdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lugid` bigint(20) unsigned default NULL,
  `acl` varchar(255) default 'client',
  `active` tinyint(1) default NULL,
  PRIMARY KEY  (`accountid`),
  UNIQUE KEY `uk_account_username` (`username`)
) ENGINE=MyISAM;

--
-- Table structure for table `event_account_artikel`
--

DROP TABLE IF EXISTS `event_account_artikel`;
CREATE TABLE `event_account_artikel` (
  `accountid` bigint(20) unsigned NOT NULL default '0',
  `artikelid` bigint(20) unsigned NOT NULL default '0',
  `anzahl` int(11) NOT NULL default '0',
  `crdate` datetime default NULL,
	groesse VARCHAR(10) default NULL,
  PRIMARY KEY  (`accountid`,`artikelid`,`anzahl`)
) ENGINE=MyISAM;

--
-- Table structure for table `event_anmeldung`
--

DROP TABLE IF EXISTS `event_anmeldung`;
CREATE TABLE `event_anmeldung` (
  `anmeldungid` bigint(20) unsigned NOT NULL auto_increment,
  `accountid` bigint(20) unsigned NOT NULL default '0',
  `lugid` bigint(20) unsigned NOT NULL default '0',
  `vorname` varchar(30) NOT NULL default '',
  `nachname` varchar(30) NOT NULL default '',
  `strasse` varchar(60) NOT NULL default '',
  `hausnr` varchar(10) NOT NULL default '',
  `plz` varchar(10) NOT NULL default '',
  `ort` varchar(60) NOT NULL default '',
  `land` char(2) NOT NULL default 'DE',
  `email` varchar(255) NOT NULL default '',
  `gebdat` date NOT NULL default '0000-00-00',
  `landid` tinyint(2) unsigned NOT NULL default '0',
  `vegetarier` tinyint(1) unsigned NOT NULL default '0',
  `arrival` tinyint(2) unsigned NOT NULL default '0',
  `ankunft` varchar(255) NOT NULL default '',
  `abfahrt` varchar(255) NOT NULL default '',
	bemerkung TEXT,
  PRIMARY KEY  (`anmeldungid`)
) ENGINE=MyISAM;

--
-- Table structure for table `event_anmeldung_event`
--

DROP TABLE IF EXISTS `event_anmeldung_event`;
CREATE TABLE `event_anmeldung_event` (
  `anmeldungid` bigint(20) unsigned NOT NULL default '0',
  `eventid` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`anmeldungid`,`eventid`)
) ENGINE=MyISAM;

--
-- Table structure for table `event_artikel`
--

DROP TABLE IF EXISTS `event_artikel`;
CREATE TABLE `event_artikel` (
  `artikelid` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `beschreibung` text,
  `kaufab` datetime default NULL,
  `kaufbis` datetime default NULL,
  `preis` decimal(5,2) default NULL,
  `pic` varchar(255) default NULL,
	groessen VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY  (`artikelid`)
) ENGINE=MyISAM;

--
-- Table structure for table `event_event`
--

DROP TABLE IF EXISTS `event_event`;
CREATE TABLE `event_event` (
  `eventid` bigint(20) unsigned NOT NULL auto_increment,
  `anfang` datetime NOT NULL default '0000-00-00 00:00:00',
  `ende` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(255) NOT NULL default '',
  `beschreibung` text,
  `buchanfang` datetime NOT NULL default '0000-00-00 00:00:00',
  `buchende` datetime NOT NULL default '0000-00-00 00:00:00',
  `quota` int(11) default NULL,
  `charge` float default NULL,
	parent bigint unsigned DEFAULT NULL,
  PRIMARY KEY  (`eventid`)
) ENGINE=MyISAM;

--
-- Table structure for table `event_land`
--

DROP TABLE IF EXISTS `event_land`;
CREATE TABLE `event_land` (
  `landid` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `crdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`landid`)
) ENGINE=MyISAM;

--
-- Table structure for table `event_lug`
--

DROP TABLE IF EXISTS `event_lug`;
CREATE TABLE `event_lug` (
  `lugid` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `abk` varchar(10) default NULL,
  `url` varchar(255) default NULL,
  `crdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`lugid`)
) ENGINE=MyISAM;

--
-- Table structure for table `event_zahlung`
--

DROP TABLE IF EXISTS `event_zahlung`;
CREATE TABLE `event_zahlung` (
  `zahlungid` bigint(20) unsigned NOT NULL auto_increment,
  `accountid` bigint(20) unsigned NOT NULL default '0',
  `crdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `valutadate` datetime NOT NULL default '0000-00-00 00:00:00',
  `valuta` decimal(5,2) default NULL,
  `txt` text,
  PRIMARY KEY  (`zahlungid`)
) ENGINE=MyISAM;


--
-- Table structure for table `news_cat`
--

DROP TABLE IF EXISTS `news_cat`;
CREATE TABLE `news_cat` (
  `catid` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `pic` varchar(255) default NULL,
  PRIMARY KEY  (`catid`)
) ENGINE=MyISAM;

--
-- Table structure for table `news_eintrag`
--

DROP TABLE IF EXISTS `news_eintrag`;
CREATE TABLE `news_eintrag` (
  `eintragid` bigint(20) unsigned NOT NULL auto_increment,
  `title` varchar(80) NOT NULL default '',
  `catid` bigint(20) unsigned NOT NULL default '0',
  `short` text NOT NULL,
  `txt` text NOT NULL,
  `crdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`eintragid`)
) ENGINE=MyISAM;


