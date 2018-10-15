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
                    // tests de gestion du formulaire
                    if (isset($_POST["cmdValider"])) {
                        // récupération des valeurs saisies
                        if (!empty($_POST["txtNom"])) {
                            $strNom = ucfirst($_POST["txtNom"]);
                        }
                        if (!empty($_POST["txtPrenom"])) {
                            $strPrenom = ucfirst($_POST["txtPrenom"]);
                        }
                        if (!empty($_POST["txtAlias"])) {
                            $strAlias = ucfirst($_POST["txtAlias"]);
                        }
                        if (!empty($_POST["txtNotes"])) {
                            $strNotes = ucfirst($_POST["txtNotes"]);
                        }                        
                        // test zones obligatoires 
                        if (!empty($strNom)) {
                            // les zones obligatoires sont présentes
                        }
                        else {
                            if (empty($strNom)) {
                                $tabErreurs["Nom"] = "Le nom doit être renseigné !";
                            }
                            $hasErrors = true;
                        }
                        if (!$hasErrors) {                            
                            // ajout dans la base de données
                            $strSQL = "INSERT INTO auteur (nom_auteur, prenom_auteur, alias, notes) "
                                    . "VALUES ('"
                                    .$strNom."','"
                                    .$strPrenom."','"
                                    .$strAlias."','"
                                    .$strNotes."')";
                            try {
                                $res = execSQL($cnx, $strSQL);
                                if ($res) {                                    
                                    echo '<span class="info">L\'auteur '
                                        .$strNom.' '
                                        .$strPrenom.' a été ajouté</span>';
                                    $afficherForm = false;
                                }
                                else {
                                    $tabErreurs["Erreur"] = 'Une erreur s\'est produite dans l\'opération d\'ajout !';
                                    $tabErreurs["Nom"] = $strNom;
                                    $tabErreurs["Prenom"] = $strPrenom;
                                    $tabErreurs["Alias"] = $strAlias;
                                    $tabErreurs["Notes"] = $strNotes;                                    
                                    $hasErrors = true;
                                }
                            }
                            catch (PDOException $e) {
                                $tabErreurs["Erreur"] = 'Une exception PDO a été levée !';
                                $hasErrors = true;
                            }
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
                        <form action="ajouterAuteur.php" method="post">
                            <div class="corps-form">
                                <fieldset>
                                    <legend>Ajouter un auteur</legend>
                                    <table>
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
                                                    <?php 
                                                        if (!empty($strPrenom)) {
                                                            echo ' value="'.$strPrenom.'"';
                                                        }
                                                    ?>
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
                                                    <?php 
                                                        if (!empty($strAlias)) {
                                                            echo ' value="'.$strAlias.'"';
                                                        }
                                                    ?>
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
                                                    cols="80"><?php 
                                                        if (!empty($strNotes)) {
                                                            echo $strNotes;
                                                        }
                                                    ?></textarea>
                                            </td>
                                        </tr>
                                    </table>                                
                                </fieldset>
                            </div>
                            <div class="pied-form">
                                <p>
                                    <input id="cmdValider" name="cmdValider" 
                                           type="submit"
                                           value="Ajouter"
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