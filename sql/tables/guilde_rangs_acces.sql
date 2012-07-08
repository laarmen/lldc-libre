--
-- Table structure for table `guilde_rangs_acces`
--

DROP TABLE IF EXISTS `guilde_rangs_acces`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `guilde_rangs_acces` (
  `idRang` smallint(5) unsigned NOT NULL auto_increment,
  `idGuilde` smallint(4) unsigned NOT NULL default '0',
  `nomRang` varchar(30) NOT NULL default '',
  `descriptionRang` varchar(255) NOT NULL default 'Ce rang n''a pas encore de description',
  `ecuyer` enum('0','1') NOT NULL default '0',
  `chef` enum('0','1') NOT NULL default '0',
  `configBarre` enum('0','1') NOT NULL default '0',
  `configBlason` enum('0','1') NOT NULL default '0',
  `configNomForum` enum('0','1') NOT NULL default '0',
  `configPageAccueil` enum('0','1') NOT NULL default '0',
  `configIntegration` enum('0','1') NOT NULL default '0',
  `configForum` enum('0','1') NOT NULL default '0',
  `moderationForum` enum('0','1') NOT NULL default '0',
  `diplomatie` enum('0','1') NOT NULL default '0',
  `gestionCoffres` enum('0','1') NOT NULL default '0',
  `gestionMembresSupprimer` enum('0','1') NOT NULL default '0',
  `gestionDemandesIntegration` enum('0','1') NOT NULL default '0',
  `configListeNoire` enum('0','1') NOT NULL default '0',
  `configListeBlanche` enum('0','1') NOT NULL default '0',
  `miseAPrix` enum('0','1') NOT NULL default '0',
  `gestionGarnison` enum('0','1') NOT NULL default '0',
  `gestionChat` enum('0','1') NOT NULL default '0',
  `commerceRoutes` enum('0','1') NOT NULL COMMENT 'Gestion des routes commerciales',
  `commerceConvois` enum('0','1') NOT NULL COMMENT 'Gestion des convois commerciaux',
  PRIMARY KEY  (`idRang`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Acces des rangs des guildes';
SET character_set_client = @saved_cs_client;
