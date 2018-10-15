<?php
/** 
 * Page de gestion des auteurs

  * @author 
  * @package default
 */

    session_start();
    // inclure les bibliothèques de fonctions
    require_once 'include/_config.inc.php';
    require_once 'include/_data.lib.php';
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
            <h2>Gestion des auteurs</h2>
            <div id="object-list">
                <?php
                // variables pour la gestion des erreurs
                $hasErrors = false;
                $afficherForm = true;
                // connexion à la base de données
                $cnx = connectDB();
                // récupération de l'ID
                if (isset($_GET["id"])) {
                    $intID = intval(htmlentities($_GET["id"]));
                    // récupération du libellé dans la base
                    $strSQL = "SELECT nom_auteur, prenom_auteur, alias, notes "
                        ."FROM auteur "
                        ."WHERE id_auteur = ".$intID;
                    $lAuteur = getRows($cnx, $strSQL);
                    if ($lAuteur->rowCount() == 1) {
                        $ligne = $lAuteur->fetch();
                        $strNomAuteur = $ligne[0];
                        $strPrenomAuteur = $ligne[1];
                        $strAlias = $ligne[2];
                        $strNotes = $ligne[3];
                    }
                    else {
                        $tabErreurs["Erreur"] = "Cet auteur n'existe pas !";
                        $tabErreurs["ID"] = $intID;
                        $hasErrors = true;
                        $afficherForm = false;
                    }
                    $lAuteur->closeCursor();
                }
                else {
                    // pas d'id dans l'url ni clic sur Valider : c'est anormal
                    $tabErreurs["Erreur"] = "Aucun genre n'a été transmis pour consultation !";
                    $hasErrors = true;
                    $afficherForm = false;
                }
                // affichage des erreurs
                if ($hasErrors) {
                    foreach ($tabErreurs as $code => $libelle) {
                        echo '<span class="erreur">'.$code.' : '.$libelle.'</span>';
                    }
                }
                if ($afficherForm) {
                    // affichage des données
                    ?>                    
                    <div class="corps-form">
                        <fieldset>
                            <legend>Consulter un auteur</legend>                        
                            <div id="breadcrumb">
                                <a href="ajouterAuteur.php?id=<?php echo $intID ?>">Ajouter</a>&nbsp;
                                <a href="modifierAuteur.php?id=<?php echo $intID ?>">Modifier</a>&nbsp;
                                <a href="supprimerAuteur.php?id=<?php echo $intID ?>">Supprimer</a>
                            </div>
                            <table>
                                <tr>
                                    <td class="h-entete">
                                        ID :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $intID ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Nom :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strNomAuteur ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Prénom :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strPrenomAuteur ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Alias :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strAlias ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Notes :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strNotes ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>                    
                    </div>
                <?php
                }
                // déconnexion
                disconnectDB($cnx);
                ?>
            </div>
        </div>
        <?php include("include/footer.php") ; ?>
    </body>
</html>