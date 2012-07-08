--
-- Table structure for table `guilde_rangs`
--

DROP TABLE IF EXISTS `guilde_rangs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `guilde_rangs` (
  `idduj` mediumint(7) unsigned NOT NULL default '0',
  `idRang` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`idduj`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table reliant un joueur à son rang';
SET character_set_client = @saved_cs_client;
