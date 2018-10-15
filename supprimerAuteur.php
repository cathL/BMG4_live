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
        <title>BMG - Municipale de Groville</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="styles/screen.css" />
    </head>
    <body>
        <?php include("include/header.php") ; ?>
        <?php include("include/menu.php") ; ?>
        <div id="content">
            <h2>Gestion des auteurs</h2>
            <?php
                // variables pour la gestion des erreurs
                $tabErreurs = array(); 
                $hasErrors = false;
                $suppOK = -1; // -1 == impossible, 1 == ok, 0 == fait
                // connexion à la base de données                    
                $cnx = connectDB();
                // récupération de l'identifiant du auteur passé dans l'URL
                if (isset($_GET["id"])) {
                    $intID = intval((htmlentities($_GET["id"])));
                    // récupération des données  dans la base
                    $strSQL = "SELECT nom_auteur, prenom_auteur, alias "
                        ."FROM auteur "
                        ."WHERE id_auteur = ".$intID;
                    $lAuteur = getRows($cnx, $strSQL);
                    if ($lAuteur->rowCount() == 1) {
                        $ligne = $lAuteur->fetch();
                        $strNom = $ligne[0];
                        $strPrenom = $ligne[1];
                        $strAlias = $ligne[2];
                    }
                    else {
                        $tabErreurs["Erreur"] = "Cet auteur n'existe pas !";
                        $tabErreurs["ID"] = $intID;
                        $hasErrors = true;
                    }
                    $lAuteur->closeCursor();
                    // l'auteur peut-il être supprimé
                    if (!$hasErrors) {
                        // rechercher des ouvrages de cet auteur
                        $strSQL = "SELECT COUNT(*)  "
                            ."FROM auteur_ouvrage "
                            ."WHERE id_auteur = ".$intID;
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
                            $tabErreurs["Erreur"] = "Cet auteur est référencé par des ouvrages, suppression impossible !";
                            $tabErreurs["ID"] = $intID;
                            $tabErreurs["Nom"] = $strNom;
                            $tabErreurs["Prénom"] = $strPrenom;
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
                        $intID = $_POST["hidID"];
                        $strNom = $_POST["hidNom"];
                        $strPrenom = $_POST["hidPrenom"];                        
                        // suppression dans la base de données
                        $strSQL = "DELETE FROM auteur "
                            ."WHERE id_auteur = ". $intID;
                        try {
                            $res = execSQL($cnx, $strSQL);
                            if ($res) {                                    
                                echo '<span class="info">L\'auteur '
                                    .$strNom.' '
                                    .$strPrenom.' a été supprimé</span>';
                                $suppOK = 0;                            
                            }
                            else {
                                $tabErreurs["Erreur"] = 'Une erreur s\'est produite lors de l\'opération de suppression !';
                                $tabErreurs["ID"] = $intID;
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
                        $tabErreurs["Erreur"] = "Aucun auteur n'a été transmis pour modification !";
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
                    <form action="supprimerAuteur.php" method="post">
                        <input type="hidden" name="hidID" value="<?php echo $intID ?>" />
                        <input type="hidden" name="hidNom" value="<?php echo $strNom ?>" />
                        <input type="hidden" name="hidPrenom" value="<?php echo $strPrenom ?>" />
                        <div class="corps-form">
                            <fieldset>
                                <legend>Supprimer un auteur</legend>
                                <table>
                                        <tr>
                                            <td>
                                                <span>
                                                    ID :
                                                </span>
                                            </td>
                                            <td>
                                                <span>
                                                    <?php echo $intID ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <span>
                                                    Nom :
                                                </span>
                                            </td>
                                            <td>
                                                <span> 
                                                    <?php echo $strNom ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <span>
                                                    Prénom :
                                                </span>
                                            </td>
                                            <td>
                                                <span> 
                                                    <?php echo $strPrenom ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <span>
                                                    Alias :
                                                </span>
                                            </td>
                                            <td>
                                                <span> 
                                                    <?php echo $strAlias ?>
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