<?php
// ----------------------------------------------------------------------------------------
/* BMG
 * 
*/
// ----------------------------------------------------------------------------------------

/**
 * paramètres de configuration de l'appplication
 */

// gestion d'erreur 
ini_set('error_reporting', E_ALL);      // en phase de développement
//ini_set('error_reporting', 0);        // en phase de production 

// constantes pour l'accès à la base de données
define('DB_SERVER', 'localhost');	// serveur MySql
define('DB_DATABASE', 'bmg');		// nom de la base de données
define('DB_USER', 'root');		// nom d'utilisateur
define('DB_PWD', '');                   // mot de passe

// constantes utilisées par l'application
define ('PATH_TO_IMG', 'img/couvertures/');  // chemin d'accès aux images de couverture des ouvrages
define ('DEFAULT_IMG', '0.jpg');             // couverture par défaut
