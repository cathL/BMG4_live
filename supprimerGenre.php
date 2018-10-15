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
        <title>BMG - Municipale de Groville</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="styles/screen.css" />
    </head>
    <body>
        <?php include("include/header.php") ; ?>
        <?php include("include/menu.php") ; ?>
        <div id="content">
            <h2>Gestion des genres</h2>
            <?php
                // variables pour la gestion des erreurs
                $tabErreurs = array(); 
                $hasErrors = false;
                $suppOK = -1; // -1 == impossible, 1 == ok, 0 == fait
                // connexion à la base de données                    
                $cnx = connectDB();                
                // récupération de l'identifiant du genre passé dans l'URL
                if (isset($_GET["id"])) {
                    $strCode = strtoupper(htmlentities($_GET["id"]));
                    // récupération du libellé dans la base
                    $strSQL = "SELECT lib_genre  "
                        ."FROM genre "
                        ."WHERE code_genre = '".$strCode."'";
                    $leGenre = getRows($cnx, $strSQL);
                    if ($leGenre->rowCount() == 1) {
                        $ligne = $leGenre->fetch();
                        $strLibelle = $ligne[0];
                    }
                    else {
                        $tabErreurs["Erreur"] = "Ce genre n'existe pas !";
                        $tabErreurs["Code"] = $strCode;
                        $hasErrors = true;
                    }
                    $leGenre->closeCursor();
                    if (!$hasErrors) {
                        // rechercher des ouvrages de ce genre
                        $strSQL = "SELECT COUNT(*)  "
                            ."FROM ouvrage "
                            ."WHERE code_genre = '".$strCode."'";
                        try {
                            $lesOuvrages = $cnx->query($strSQL);
                        }
                        catch (PDOException $e) {
                            echo $e->getMessage();
                        }
                        $nbOuvrages = $lesOuvrages->fetchColumn(0);
                        if ($nbOuvrages == 0) {
                            // c'est bon, on peut le supprimer
                            $suppOK = 1;
                        }
                        else {
                            $tabErreurs["Erreur"] = "Ce genre est référencé par des ouvrages, suppression impossible !";
                            $tabErreurs["Code"] = $strCode;
                            $tabErreurs["Ouvrages"] = $nbOuvrages;
                            $hasErrors = true;                        
                        }
                        $lesOuvrages->closeCursor();
                    }
                }
                else {
                    // tests de gestion du formulaire
                    if (isset($_POST["cmdValider"])) {
                        // récupération des valeurs cachées
                        $strCode = $_POST["hidCode"];
                        $strLibelle = $_POST["hidLib"];
                        // suppression de la base de données
                        $strSQL = "DELETE FROM genre WHERE code_genre = '". $strCode."'";
                        try {
                            $res = execSQL($cnx, $strSQL);
                            if ($res) {
                                echo '<span class="info">Le genre '
                                    .$strCode.'-'
                                    .$strLibelle.' a été supprimé</span>';
                                $suppOK = 0;
                            }
                            else {
                                $tabErreurs["Erreur"] = 'Une erreur s\'est produite lors de l\'opération de suppression !';
                                $tabErreurs["Code"] = $strCode;
                                // en phase de test, on peut ajouter le SQL :
                                $tabErreurs["SQL"] = $strSQL;
                                $hasErrors = true;
                            }
                        }
                        catch (PDOException $e) {
                            $tabErreurs["Erreur"] = 'Une exception PDO a été levée !';
                            $tabErreurs["Message"] = $e->getMessage();
                            $hasErrors = true;
                        }
                    }
                    else {
                        // pas d'id dans l'url ni clic sur Valider : c'est anormal
                        $tabErreurs["Erreur"] = "Aucun genre n'a été transmis pour modification !";
                        $hasErrors = true;
                    }
                }
                // affichage des erreurs
                if ($hasErrors) {
                    foreach ($tabErreurs as $code => $libelle) {
                        echo '<span class="erreur">'.$code.' : '.$libelle.'</span>';
                    }
                }                   
                // affichage du formulaire
                if ($suppOK == 1) {
                    ?>
                    <form action="supprimerGenre.php" method="post">
                        <input type="hidden" name="hidCode" value="<?php echo $strCode ?>" />
                        <input type="hidden" name="hidLib" value="<?php echo $strLibelle ?>" />
                        <div class="corps-form">
                            <fieldset>
                                <legend>Supprimer un genre</legend>
                                <table>
                                        <tr>
                                            <td>
                                                <span>
                                                    Code :
                                                </span>
                                            </td>
                                            <td>
                                                <span>
                                                    <?php echo $strCode ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <span>
                                                    Libellé :
                                                </span>
                                            </td>
                                            <td>
                                                <span> 
                                                    <?php echo $strLibelle ?>
                                                </span>
                                            </td>
                                        </tr>
                                </table>
                            </fieldset>
                        </div>
                        <div class="pied-form">
                            <p>
                                <input id="cmdValider" name="cmdValider" 
                                       type="submit"
                                       value="Supprimer"
                                />
                            </p> 
                        </div>
                    </form>
                <?php 
                }
                // déconnexion
                disconnectDB($cnx);
            ?>
        </div>          
        <?php include("include/footer.php") ; ?>
    </body>
</html>