--
-- Table structure for table `guerre_interguilde`
--

DROP TABLE IF EXISTS `guerre_interguilde`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `guerre_interguilde` (
  `idGuerre` mediumint(7) unsigned NOT NULL auto_increment,
  `idGuilde1` smallint(4) unsigned NOT NULL default '0',
  `idGuilde2` smallint(4) unsigned NOT NULL default '0',
  `dateDebut` int(10) unsigned NOT NULL default '0',
  `dateFin` int(10) unsigned NOT NULL default '0',
  `demandePaix` enum('0','1','2') NOT NULL default '0' COMMENT '0 si tout le monde veut se battre, 1 si la 1ere guilde demande la paix, 2 si la seconde la demande.',
  PRIMARY KEY  (`idGuerre`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
