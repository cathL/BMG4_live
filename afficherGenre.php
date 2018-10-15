<?php
/** 
 * Page de gestion des genres

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
            <h2>Gestion des genres</h2>
            <div id="object-list">
                <?php
                // variables pour la gestion des erreurs
                $hasErrors = false;
                $afficherForm = true;
                // ouvrir une connexion
                $cnx = connectDB();
                // récupération du code
                if (isset($_GET["id"])) {
                    $strCode = strtoupper(htmlentities($_GET["id"]));
                    // récupération du libellé dans la base
                    $strSQL = "SELECT lib_genre  "
                        ."FROM genre "
                        ."WHERE code_genre = '".$strCode."'";
                    try {
                        $leGenre = getRows($cnx, $strSQL);
                    }
                    catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                    if ($leGenre->rowCount() == 1) {
                        $ligne = $leGenre->fetch();
                        $strLibelle = $ligne[0];
                    }
                    else {
                        $tabErreurs["Erreur"] = "Ce genre n'existe pas !";
                        $tabErreurs["Code"] = $strCode;
                        $hasErrors = true;
                        $afficherForm = false;
                    }
                    $leGenre->closeCursor();
                    disconnectDB($cnx);
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
                            <legend>Consulter un genre</legend>                        
                            <div id="breadcrumb">
                                <a href="ajouterGenre.php?id=<?php echo $strCode ?>">Ajouter</a>&nbsp;
                                <a href="modifierGenre.php?id=<?php echo $strCode ?>">Modifier</a>&nbsp;
                                <a href="supprimerGenre.php?id=<?php echo $strCode ?>">Supprimer</a>
                            </div>
                            <table>
                                <tr>
                                    <td class="h-entete">
                                        Code :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strCode ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h-entete">
                                        Libellé :
                                    </td>
                                    <td class="h-valeur">
                                        <?php echo $strLibelle ?>
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