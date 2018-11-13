<?php
require_once 'modele/GenreDal.class.php';
require_once 'include/_reference.lib.php';

if (!isset($_REQUEST['action'])) {
    $action = 'listerGenres';
} else {
    $action = $_REQUEST['action'];    
}

// variables pour la gestion des messages
$titrePage = 'Gestion des genres';
// variables pour la gestion des erreurs
$tabErreurs = array(); 
$hasErrors = false;


// variables pour la gestion des messages
$msg = '';    // message passé à la vue v_afficherMessage
$lien = '';   // message passé à la vue v_afficherErreurs

  

switch ($action) {
    case 'listerGenres' : {
            $lesGenres = GenreDal::loadGenres(1);
            $nbGenres = count($lesGenres);
            include 'vues/v_listeGenres.php';   
        }
        break;
    case 'consulterGenre' : {
            
        }
        break;
    case 'ajouterGenre' : {
            // initialisation des variables
           $strCode = '';
           $strLibelle = '';
            // traitement de l'option : saisie ou validation ?
            if (isset($_GET["option"])) {
                $option = htmlentities($_GET["option"]);
            } else {
                $option = 'saisirGenre';
            }
            
            switch ($option) {
                case 'saisirGenre' : {
                        include 'vues/v_ajouterGenre.php';
                    } break;
                case 'validerGenre' : {                     
                        // tests de gestion du formulaire
                        if (isset($_POST["cmdValider"])) {
                            // récupération du libellé
                            if (!empty($_POST["txtLibelle"])) {
                                $strLibelle = ucfirst(htmlentities($_POST["txtLibelle"]));
                            }
                            if (!empty($_POST["txtCode"])) {
                                $strCode = strtoupper(htmlentities($_POST["txtCode"]));
                            }
                            // test zones obligatoires
                            if (!empty($strCode) and ! empty($strLibelle)) {
                                // les zones obligatoires sont présentes
                                // tests de cohérence 
                                // contrôle d'existence d'un genre avec le même code
                                $doublon = GenreDal::loadGenreByID($strCode);
                                if ($doublon != NULL) {
                                    // signaler l'erreur
                                    $tabErreurs[] = 'Il existe déjà un genre avec ce code !';
                                    $hasErrors = true;
                                }
                            }
                            else {
                                // une ou plusieurs valeurs n'ont pas été saisies
                                if (empty($strCode)) {
                                $tabErreurs[] = "Le code doit être renseigné !";
                                }
                                if (empty($strLibelle)) {
                                $tabErreurs[] = "Le libellé doit être renseigné !";
                                }
                                $hasErrors = true;
                            }
                            if (!$hasErrors) {
                                $res = GenreDal::addGenre($strCode,$strLibelle);
                                if ($res > 0) {
                                    $msg = 'Le genre '
                                        . $strCode . '-'
                                        . $strLibelle . ' a été ajouté';
                                    include 'vues/_v_afficherMessage.php'; 
                                    // include 'vues/v_consulterGenre.php'; 
                                }
                                else {
                                    $tabErreurs[] = 'Une erreur s\'est produite dans l\'opération d\'ajout !';
                                    $hasErrors = true;
                                }
                            }
                            if ($hasErrors) {
                                $msg = "L'opération d'ajout n'a pas pu être menée à terme en raison des erreurs suivantes :";
                                $lien = '<a href="index.php?uc=gererGenres&action=ajouterGenre">Retour à la saisie</a>';
                                include 'vues/_v_afficherErreurs.php';
                            }    //hasError = true                         
                        } // cmdValider
                    } // validerGenre
                } break; // switch $option
            } // switch ajouterGenre
        break;
    case 'modifierGenre' : {
            
        }
        break;
    case 'supprimerGenre' : {
            
        }
        break;
    default : {
        include 'vues/_v_home.php';
    }
    
}


