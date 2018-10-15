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
                    // initialisation des variables
                    $strLibelle = '';
                    // variables pour la gestion des erreurs
                    $tabErreurs = array(); 
                    $hasErrors = false;
                    $afficherForm = true;
                    // connexion à la base de données
                    $cnx = connectDB();
                    // récupération du code
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
                            $afficherForm = false;
                        }
                        $leGenre->closeCursor();
                    }
                    else {
                        // si on a cliqué sur Valider
                        if (isset($_POST["cmdValider"])) {
                            // mémoriser les valeurs pour les réafficher dans le formulaire
                            $strCode = $_POST["txtCode"];
                            $strLibelle = ucfirst(htmlentities($_POST["txtLibelle"]));
                            // test zones obligatoires
                            if (!empty($strLibelle)) {
                                // les zones obligatoires sont présentes
                                // tests de cohérence
                            }
                            else {
                                if (empty($strLibelle)) {
                                    $tabErreurs["Erreur"] = "Le libellé doit être renseigné !";
                                    $tabErreurs["Code"] = $strCode;
                                }
                                $hasErrors = true;
                            }
                            if (!$hasErrors) {
                                // mise à jour dans la base de données
                                $strSQL = "UPDATE genre SET lib_genre = '"
                                        .$strLibelle."' WHERE code_genre = '"
                                        .$strCode."'";
                                try {
                                    $res = execSQL($cnx, $strSQL);
                                    if ($res) {                                    
                                        echo '<span class="info">Le genre '
                                            .$strCode.'-'
                                            .$strLibelle.' a été modifié</span>';
                                        $afficherForm = false;
                                    }
                                    else {
                                        $tabErreurs["Erreur"] = 'Une erreur s\'est produite lors de l\'opération de mise à jour !';
                                        $tabErreurs["Code"] = $strCode;
                                        $tabErreurs["Libellé"] = $strLibelle;
                                        // en phase de test, on peut ajouter le SQL :
                                        $tabErreurs["SQL"] = $strSQL;
                                        $hasErrors = true;
                                    }
                                }
                                catch (PDOException $e) {
                                    $tabErreurs["Code"] = 'Une exception a été levée !';
                                    $hasErrors = true;
                                }
                            }
                        }
                        else {
                            // pas d'id dans l'url ni clic sur Valider : c'est anormal
                            $tabErreurs["Erreur"] = "Aucun genre n'a été transmis pour modification !";
                            $hasErrors = true;
                            $afficherForm = false;
                        }
                    }
                    // affichage des erreurs
                    if ($hasErrors) {
                        foreach ($tabErreurs as $code => $libelle) {
                            echo '<span class="erreur">'.$code.' : '.$libelle.'</span>';
                        }
                    }
                    // affichage du formulaire
                    if ($afficherForm) {
                        ?>                    
                        <form action="modifierGenre.php" method="post">
                            <div class="corps-form">
                                <fieldset>
                                    <legend>Modifier un genre</legend>
                                    <table>
                                        <tr>
                                            <td>
                                                <label for="txtCode">
                                                    Code :
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" id="txtCode" 
                                                    name="txtCode"
                                                    size="3" maxlength="3"
                                                    readonly="readonly"
                                                    value="<?php echo $strCode ?>"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <label for="txtLibelle">
                                                    Libellé :
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" id="txtLibelle" 
                                                    name="txtLibelle"
                                                    size="50" maxlength="50"
                                                    value="<?php echo $strLibelle ?>"
                                                />
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
                            <div class="pied-form">
                                <p>
                                    <input id="cmdValider" name="cmdValider" 
                                           type="submit"
                                           value="Modifier"
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
        </div>          
        <?php include("include/footer.php") ; ?>
    </body>
</html>