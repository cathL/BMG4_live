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
            <h2>Gestion des ouvrages</h2>
            <?php
                // variables pour la gestion des erreurs
                $tabErreurs = array(); 
                $hasErrors = false;
                $suppOK = -1; // -1 == impossible, 1 == ok, 0 == fait
                // connexion à la base de données                    
                $cnx = connectDB();
                // récupération de l'identifiant du ouvrage passé dans l'URL
                if (isset($_GET["id"])) {
                    $intID = intval((htmlentities($_GET["id"])));
                    // récupération des données  dans la base
                    $strSQL = "SELECT titre, lib_genre, auteur "
                        ."FROM v_ouvrages "
                        ."WHERE no_ouvrage = ".$intID;
                    $lOuvrage = getRows($cnx, $strSQL);
                    if ($lOuvrage->rowCount() == 1) {
                        $ligne = $lOuvrage->fetch();
                        $strTitre = $ligne[0];
                        $strGenre = $ligne[1];
                        $strAuteur = $ligne[2];
                    }
                    else {
                        $tabErreurs["Erreur"] = "Cet ouvrage n'existe pas !";
                        $tabErreurs["ID"] = $intID;
                        $hasErrors = true;
                    }
                    $lOuvrage->closeCursor();
                    // l'ouvrage peut-il être supprimé
                    if (!$hasErrors) {
                        // rechercher des prêts pour cet ouvrage
                        $strSQL = "SELECT COUNT(*) "
                            ."FROM pret "
                            ."WHERE no_ouvrage = ".$intID;
                        try {
                            $lesPrets = $cnx->query($strSQL);
                        }
                        catch (PDOException $e) {
                            echo $e->getMessage();
                        }
                        $nbPrets = $lesPrets->fetchColumn(0);
                        if ($nbPrets == 0) {
                            // c'est bon, on peut le supprimer
                            $suppOK = 1;
                        }
                        else {
                            $tabErreurs["Erreur"] = "Cet ouvrage est référencé par des prêts, suppression impossible !";
                            $tabErreurs["ID"] = $intID;
                            $tabErreurs["Titre"] = $strTitre;
                            $tabErreurs["Prêts"] = $nbPrets;
                            $hasErrors = true;                        
                        }
                        $lesPrets->closeCursor();
                    }
                }
                else {
                    // tests de gestion du formulaire
                    if (isset($_POST["cmdValider"])) {
                        // récupération des valeurs cachées
                        $intID = $_POST["hidID"];
                        $strTitre = $_POST["hidTitre"];
                        $strGenre = $_POST["hidGenre"];
                        $strAuteur = $_POST["hidAuteur"];
                        // suppression dans la base de données
                        $strSQL = "DELETE FROM ouvrage "
                            ."WHERE no_ouvrage = ". $intID;
                        try {
                            $res = execSQL($cnx, $strSQL);
                            if ($res) {                                    
                                echo '<span class="info">L\'ouvrage '
                                    .$strTitre.' par '
                                    .$strAuteur.' a été supprimé</span>';
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
                        $tabErreurs["Erreur"] = "Aucun ouvrage n'a été transmis pour modification !";
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
                    <form action="supprimerOuvrage.php" method="post">
                        <input type="hidden" name="hidID" value="<?php echo $intID ?>" />
                        <input type="hidden" name="hidTitre" value="<?php echo $strTitre ?>" />
                        <input type="hidden" name="hidGenre" value="<?php echo $strGenre ?>" />
                        <input type="hidden" name="hidAuteur" value="<?php echo $strAuteur ?>" />
                        <div class="corps-form">
                            <fieldset>
                                <legend>Supprimer un ouvrage</legend>
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
                                                    Titre :
                                                </span>
                                            </td>
                                            <td>
                                                <span> 
                                                    <?php echo $strTitre ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <span>
                                                    Genre :
                                                </span>
                                            </td>
                                            <td>
                                                <span> 
                                                    <?php echo $strGenre ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <span>
                                                    Auteur :
                                                </span>
                                            </td>
                                            <td>
                                                <span> 
                                                    <?php echo $strAuteur ?>
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