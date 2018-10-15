<?php
/** 
 * Page de gestion des ouvrages

  * @author 
  * @package default
 */

    session_start();
    // inclure les bibliothèques de fonctions
    require_once 'include/_config.inc.php';
    require_once 'include/_data.lib.php';
    require_once 'include/_metier.lib.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>BMG - Bibliothèque municipale de Groville</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="styles/screen.css" />
    </head>
    <body>
        <?php include("include/header.php") ; ?>
        <?php include("include/menu.php") ; ?>
        <div id="content">
            <h2>Gestion des ouvrages</h2>
            <div id="object-list">
                <?php
                // variables pour la gestion des erreurs
                $hasErrors = false;
                // récupération de l'ID
                if (isset($_GET["id"])) {
                    $intID = intval(htmlentities($_GET["id"]));
                    // connexion à la base de données
                    $cnx = connectDB();
                    // récupération du libellé dans la base
                    $strSQL = "SELECT no_ouvrage as ID, "
                            ."titre, "
                            ."acquisition, "
                            ."lib_genre, "
                            ."salle, "
                            ."rayon, "
                            ."dernier_pret, "
                            ."disponibilite, "
                            ."auteur "
                            ."FROM v_ouvrages "
                            ."WHERE no_ouvrage = ".$intID;
                    $lOuvrage = getRows($cnx, $strSQL);
                    if ($lOuvrage->rowCount() == 1) {
                        $ligne = $lOuvrage->fetch();
                        $strTitre = $ligne[1];
                        $strAcquisition = $ligne[2];
                        $strGenre = $ligne[3];
                        $strSalle = $ligne[4];
                        $strRayon = $ligne[5];
                        $strDernierPret = $ligne[6];
                        $strDispo = $ligne[7];
                        $strAuteur = $ligne[8];
                    }
                    else {
                        $tabErreurs["Erreur"] = "Cet ouvrage n'existe pas !";
                        $tabErreurs["ID"] = $intID;
                        $hasErrors = true;
                    }
                    $lOuvrage->closeCursor();
                    disconnectDB($cnx);
                }                    
                // affichage des erreurs
                if ($hasErrors) {
                    foreach ($tabErreurs as $code => $libelle) {
                        echo '<span class="erreur">'.$code.' : '.$libelle.'</span>';
                    }
                }           
                else {
                    // affichage des données
                    ?>                    
                    <div class="corps-form">
                        <fieldset>
                            <legend>Consulter un ouvrage</legend>                        
                            <div id="breadcrumb">
                                <a href="ajouterOuvrage.php">Ajouter</a>&nbsp;
                                <a href="modifierOuvrage.php?id=<?php echo $intID ?>">Modifier</a>&nbsp;
                                <a href="supprimerOuvrage.php?id=<?php echo $intID ?>">Supprimer</a>
                            </div>
                            <table>
                                <tr>
                                    <td class="h-entete">
                                        ID :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $intID ?>
                                    </td>
                                    <td class="right h-valeur" rowspan="8">
                                        <?php echo couvertureOuvrage($intID, $strTitre) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Titre :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strTitre ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Auteur(s) :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strAuteur ?>
                                    </td>
                                </tr>                                
                                <tr>
                                    <td class="h-entete">
                                        Date d'acquisition :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strAcquisition ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Genre :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strGenre ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Salle et rayon :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strSalle.', '.$strRayon ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Dernier prêt :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strDernierPret ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Disponibilité :
                                    </td>
                                    <td class="h-valeur">
                                        <?php 
                                            if ($strDispo == "D") {
                                                echo '<img src="img/dispo.png" alt="" />';
                                            }
                                            else {
                                                echo '<img src="img/emprunte.png" alt="" />';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>                    
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <?php include("include/footer.php") ; ?>
    </body>
</html>