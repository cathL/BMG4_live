<?php
    /**
     * Page d'accueil de l'application CAG

     * Point d'entrée unique de l'application
     * @author 
     * @package default
     */
    // inclure les bibliothèques de fonctions
    require_once 'include/_config.inc.php';
    session_start(); // début de session
    // on simule un utilisateur connecté (en phase de test)
    $_SESSION['id'] = 9999;
    $_SESSION['nom'] = 'Dupont';
    $_SESSION['prenom'] = 'Jean';
    
    include("vues/_v_header.php");
    include("vues/_v_menu.php");
    
    if (isset($uc)) {
        $uc = $_REQUEST['uc']; 
    }
    else {
        $uc = 'home';
}
    switch ($uc) {
        case 'gererGenres' : include 'controleurs/gererGenres.php'; break;
        case 'gererAuteurs' : include 'controleurs/gererAureurs.php'; break;
        case 'gererOuvrages' : include 'controleurs/gererOuvrages.php'; break;
        default : include 'vues/_v_home.php';
    }
    include("vues/_v_footer.php");
?>
    