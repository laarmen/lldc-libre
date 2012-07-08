--
-- Table structure for table `guilde`
--

DROP TABLE IF EXISTS `guilde`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `guilde` (
  `idGuilde` smallint(4) unsigned NOT NULL auto_increment,
  `dateCreation` int(10) unsigned NOT NULL default '0',
  `dernierePassation` int(10) unsigned NOT NULL default '0',
  `nomGuilde` varchar(40) NOT NULL default '',
  `histoireGuilde` text NOT NULL,
  `pointsAcquis` int(14) unsigned NOT NULL default '0',
  `niveau` tinyint(3) unsigned NOT NULL default '1',
  `integrationPossible` enum('0','1') NOT NULL default '1',
  `idChat` mediumint(7) unsigned NOT NULL default '0',
  PRIMARY KEY  (`idGuilde`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Table contenant les guildes';
SET character_set_client = @saved_cs_client;
