<?php
if (!isset($_REQUEST['action'])) {
    $action = 'listerGenres';
} else {
    $action = $_REQUEST['action'];    
}

// variables pour la gestion des messages
$msg = '';    // message passé à la vue v_afficherMessage
$lien = '';   // message passé à la vue v_afficherErreurs


switch ($action) {
    case 'listerGenres' : {
            
        }
        break;
    case 'consulterGenre' : {
            
        }
        break;
    case 'ajouterGenre' : {
            
        }
        break;
    case 'modifierGenre' : {
            
        }
        break;
    case 'supprimerGenre' : {
            
        }
        break;
    default : include 'vues/_v_home.php';
}


