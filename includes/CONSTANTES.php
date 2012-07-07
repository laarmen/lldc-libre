<?php
/**
 * Fichier contenant toutes les constantes du jeu
 */
require_once('SITE_CONSTANTES.php');

define('TIME', time());

define('CACHE_DIR', ROOT_DIR.'cache/');	# Repertoire de cache
define('CRON_DIR', ROOT_DIR.'cron/');	# Repertoire des crons
define('FONCTIONS_DIR', ROOT_DIR.'fonctions/');	# Repertoire des fonctions
define('INCLUDES_DIR', ROOT_DIR.'includes/');	# Repertoire des includes
define('IMAGES_DIR', ROOT_DIR.'images/');	# Repertoire des includes
define('MEMBRES_DIR', ROOT_DIR.'membres/');	# Espace membres
define('TEMPLATES_DIR', ROOT_DIR.'templates/'); # Repertoire des templates

define('IMAGES_URL', ROOT_URL.'images/');


/**	Design 			*/
if(empty($_SESSION['idduj']))	{
	define('BOUTON_ID', '2');
}

