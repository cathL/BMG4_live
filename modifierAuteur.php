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
                    // initialisation des variables
                    $strNom = '';
                    $strPrenom = '';
                    $strAlias = '';
                    $strNotes = '';
                    // variables pour la gestion des erreurs
                    $tabErreurs = array(); 
                    $hasErrors = false;
                    $afficherForm = true;
                    // connexion à la base de données
                    $cnx = connectDB();                    
                    // récupération du code
                    if (isset($_GET["id"])) {
                        $intID = intval(htmlentities($_GET["id"]));
                        // récupération des données dans la base
                        $strSQL = "SELECT nom_auteur, prenom_auteur, alias, notes "
                            ."FROM auteur "
                            ."WHERE id_auteur = ".$intID;
                        $lAuteur = getRows($cnx, $strSQL);
                        if ($lAuteur->rowCount() == 1) {
                            $ligne = $lAuteur->fetch();                            
                            $strNom = $ligne[0];
                            $strPrenom = $ligne[1];
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
                        // traitement du formulaire
                        if (isset($_POST["cmdValider"])) {
                            // mémoriser les données pour les réafficher dans le formulaire
                            $intID = intval($_POST["txtID"]);
                            // récupération des valeurs saisies
                            if (!empty($_POST["txtNom"])) {
                                $strNom = ucfirst($_POST["txtNom"]);
                            }
                            if (!empty($_POST["txtPrenom"])) {
                                $strPrenom = ucfirst(($_POST["txtPrenom"]));
                            }
                            if (!empty($_POST["txtAlias"])) {
                                $strAlias = ucfirst(($_POST["txtAlias"]));
                            }
                            if (!empty($_POST["txtNotes"])) {
                                $strNotes = ucfirst($_POST["txtNotes"]);
                            }                            
                            // test zones obligatoires
                            if (!empty($strNom)) {
                                // les zones obligatoires sont présentes
                                // tests de cohérence
                            }
                            else {
                                if (empty($strNom)) {
                                    $tabErreurs["Nom"] = "Le nom doit être renseigné !";
                                }
                                $hasErrors = true;
                            }
                            if (!$hasErrors) {
                                // mise à jour dans la base de données
                                $strSQL = "UPDATE auteur SET nom_auteur = '"
                                        .$strNom."', prenom_auteur = '"
                                        .$strPrenom."', alias = '"
                                        .$strAlias."', notes = '"
                                        .$strNotes."' "
                                        ."WHERE id_auteur = ".$intID;
                                try {
                                    $res = $cnx->exec($strSQL);
                                    if ($res) {                                    
                                        echo '<span class="info">L\'auteur '
                                            .$strNom.' '
                                            .$strPrenom.' a été modifié</span>';
                                        $afficherForm = false;
                                    }
                                    else {
                                        $tabErreurs["Erreur"] = 'Une erreur s\'est produite lors de l\'opération de mise à jour !';
                                        $tabErreurs["ID"] = $intID;
                                        $tabErreurs["Nom"] = $strNom;
                                        $tabErreurs["Prenom"] = $strPrenom;
                                        $tabErreurs["Alias"] = $strAlias;
                                        $tabErreurs["Notes"] = $strNotes;
                                        // en phase de test, on peut ajouter le SQL :
                                        $tabErreurs["SQL"] = $strSQL;
                                        $hasErrors = true;
                                    }
                                }
                                catch (PDOException $e) {
                                    $tabErreurs["Erreur"] = 'Une exception a été levée !';
                                    $hasErrors = true;
                                }
                            }
                        }
                        else {
                            // pas d'id dans l'url ni clic sur Valider : c'est anormal
                            $tabErreurs["Erreur"] = "Aucun auteur n'a été transmis pour modification !";
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
                        <form action="modifierAuteur.php" method="post">
                            <div class="corps-form">
                                <fieldset>
                                    <legend>Modifier un auteur</legend>
                                    <table>
                                        <tr>
                                            <td>
                                                <label for="txtID">
                                                    ID :
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" id="txtID" 
                                                    name="txtID"
                                                    size="5"
                                                    readonly="readonly"
                                                    value="<?php echo $intID ?>"
                                                />
                                            </td>
                                        </tr>                                        
                                        <tr>
                                            <td>
                                                <label for="txtNom">
                                                    Nom :
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" id="txtNom" 
                                                    name="txtNom"
                                                    size="50" maxlength="128"
                                                    value="<?php echo $strNom ?>"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="txtPrenom">
                                                    Prénom :
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" id="txtPrenom" 
                                                    name="txtPrenom"
                                                    size="50" maxlength="128"
                                                    value="<?php echo $strPrenom ?>"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="txtAlias">
                                                    Alias :
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" id="txtAlias" 
                                                    name="txtAlias"
                                                    size="50" maxlength="128"
                                                    value="<?php echo $strAlias ?>"
                                                />
                                            </td>
                                        </tr>                                        
                                        <tr>
                                            <td valign="top">
                                                <label for="txtNotes">
                                                    Notes :
                                                </label>
                                            </td>
                                            <td>
                                                <textarea id="txtNotes" 
                                                    name="txtNotes" 
                                                    rows="20" 
                                                    cols="80"><?php echo $strNotes ?></textarea>                                                
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