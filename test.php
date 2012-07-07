<?php
include_once(__DIR__.'/includes/CONSTANTES.php');

require_once(INCLUDES_DIR.'lib/lldc/class_GuildWar.php');
require_once(FONCTIONS_DIR.'overall/connexion.php');

if(!connexion(''))
    echo "T'as fait de la merde, coco! \n";

$results = mysql_fetch_assoc(mysql_query('SELECT * FROM members'));
