<?php
##
## 	Connexion aux bases de donnees
##
function connexion()	{
	mysql_connect(BDD_HOST, BDD_USER, BDD_PASSWORD);
	$return = mysql_select_db(BDD_NAME);
	
	unset($nom_utilisateur);
	unset($mot_de_passe);
	unset($nom_bdd);
	return $return;
}
?>
