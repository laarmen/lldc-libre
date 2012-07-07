--
-- Table structure for table `membres`
--

DROP TABLE IF EXISTS `membres`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `membres` (
  `idduj` mediumint(7) unsigned NOT NULL auto_increment,
  `nomSeigneur` char(30) NOT NULL default '',
  `nomRoyaume` char(30) NOT NULL default '',
  `derniereConnexion` int(10) unsigned NOT NULL default '0',
  `sexe` enum('Seigneur','Dame') NOT NULL default 'Seigneur',
  `peuple` enum('1','2','3','4','5') NOT NULL default '1',
  `points` int(10) unsigned NOT NULL default '0',
  `classement` mediumint(7) unsigned NOT NULL default '0',
  `xp` float unsigned NOT NULL default '0',
  `xpTotale` float unsigned NOT NULL default '0',
  `idGuilde` smallint(4) unsigned NOT NULL default '0',
  `dateIntegrationGuilde` int(10) NOT NULL default '0',
  `dateDemissionGuilde` int(10) unsigned NOT NULL default '0',
  `food` bigint(11) unsigned NOT NULL default '4000',
  `wood` bigint(11) unsigned NOT NULL default '2000',
  `gold` bigint(11) unsigned NOT NULL default '2000',
  `rock` bigint(11) unsigned NOT NULL default '1000',
  `madrens` bigint(11) unsigned NOT NULL default '500',
  `toise` float(14,5) unsigned NOT NULL default '10000.00000',
  `madrensAmneroth` int(7) unsigned NOT NULL default '0',
  `pourcentFood` tinyint(3) unsigned NOT NULL default '25',
  `pourcentWood` tinyint(3) unsigned NOT NULL default '25',
  `pourcentGold` tinyint(3) unsigned NOT NULL default '20',
  `pourcentRock` tinyint(3) unsigned NOT NULL default '20',
  `pourcentBatiment` tinyint(3) unsigned NOT NULL default '10',
  `idZone` tinyint(1) unsigned NOT NULL default '1',
  `moral` float NOT NULL default '100',
  `dateFinProtectionAttenteCombat` int(10) unsigned default NULL,
  `dateFinProtection` int(10) unsigned NOT NULL default '0',
  `dateMiseProtection` int(10) unsigned NOT NULL default '0',
  `palier20k` enum('0','1') NOT NULL default '0',
  `supprime` enum('0','1') NOT NULL default '0',
  `pnj` enum('0','1') NOT NULL default '0',
  `pnjDifficulte` enum('0','1','2','3','4','5') NOT NULL default '0',
  PRIMARY KEY  (`idduj`),
  UNIQUE KEY `nomRoyaume` (`nomRoyaume`),
  UNIQUE KEY `nomSeigneur` (`nomSeigneur`),
  KEY `points` (`points`),
  KEY `xp` (`xp`),
  KEY `idGuilde` (`idGuilde`),
  KEY `supprime` (`supprime`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Table des membres';
SET character_set_client = @saved_cs_client;
