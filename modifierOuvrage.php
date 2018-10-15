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
    require_once 'include/_forms.lib.php';
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
                    // initialisation des variables
                    $strTitre = '';
                    $intSalle = 1;
                    $strRayon = '';
                    $strGenre = '';
                    $strDate = '';
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
                        $strSQL = "SELECT titre, salle, rayon, code_genre, date_acquisition "
                            ."FROM ouvrage "
                            ."WHERE no_ouvrage = ".$intID;
                        $lOuvrage = getRows($cnx, $strSQL);
                        if ($lOuvrage->rowCount() == 1) {
                            $ligne = $lOuvrage->fetch();                            
                            $strTitre = $ligne[0];
                            $intSalle = $ligne[1];
                            $strRayon = $ligne[2];
                            $strGenre = $ligne[3];
                            $strDate = $ligne[4];
                        }
                        else {
                            $tabErreurs["Erreur"] = "Cet ouvrage n'existe pas !";
                            $tabErreurs["ID"] = $intID;
                            $hasErrors = true;
                            $afficherForm = false;
                        }
                        $lOuvrage->closeCursor();
                        disconnectDB($cnx);
                    }
                    else {
                        // traitement du formulaire
                        if (isset($_POST["cmdValider"])) {
                            // mémoriser les données pour les réafficher dans le formulaire
                            $intID = intval($_POST["txtID"]);
                            // récupération des valeurs saisies
                            if (!empty($_POST["txtTitre"])) {
                                $strTitre = ucfirst($_POST["txtTitre"]);
                            }
                            $intSalle = $_POST["rbnSalle"];
                            if (!empty($_POST["txtRayon"])) {
                                $strRayon = ucfirst($_POST["txtRayon"]);
                            }
                            $strGenre = $_POST["cbxGenres"];
                            if (!empty($_POST["txtRayon"])) {
                                $strDate = $_POST["txtDate"];
                            }
                            // test zones obligatoires
                            if (!empty($strTitre) and 
                                    !empty($strRayon) and
                                    !empty($strDate)) {
                                // tests de cohérence
                                // test de la date d'acquisition
                                $dateAcquisition = new DateTime($strDate);
                                $curDate = new DateTime(date('Y-m-d'));
                                if ($dateAcquisition > $curDate) {
                                    // la date d'acquisition est postérieure à la date du jour
                                    $tabErreurs["Erreur date"] = 'La date d\'acquisition doit être antérieure ou égale à la date du jour';
                                    $tabErreurs["Date"] = $strDate;
                                    $hasErrors = true;
                                }
                                // contrôle du rayon
                                if (!rayonValide($strRayon)) {
                                    $tabErreurs["Erreur rayon"] = 'Le rayon n\'est pas valide, il doit comporter une lettre et un chiffre !';
                                    $tabErreurs["Rayon"] = $strRayon;
                                    $hasErrors = true;
                                }
                            }
                            else {
                                if (empty($strTitre)) {
                                    $tabErreurs["Titre"] = "Le titre doit être renseigné !";
                                }
                                if (empty($strRayon)) {
                                    $tabErreurs["Rayon"] = "Le rayon doit être renseigné !";
                                }
                                if (empty($strDate)) {
                                    $tabErreurs["Acqisition"] = "La date d'acquisition doit être renseignée !";
                                }
                                $hasErrors = true;
                            }
                            if (!$hasErrors) {
                                // mise à jour dans la base de données
                                $strSQL = "UPDATE ouvrage SET titre = '".$strTitre
                                        ."', salle = ".$intSalle
                                        .", rayon = '".$strRayon
                                        ."', code_genre = '".$strGenre
                                        ."', date_acquisition = '".$strDate
                                        ."' WHERE no_ouvrage = ".$intID;
                                try {
                                    $res = $cnx->exec($strSQL);
                                    if ($res) {                                    
                                        echo '<span class="info">L\'ouvrage '
                                            .$strTitre.' a été modifié</span>';
                                        $afficherForm = false;
                                    }
                                    else {
                                        $tabErreurs["Erreur"] = 'Une erreur s\'est produite lors de l\'opération de mise à jour !';
                                        $tabErreurs["ID"] = $intID;
                                        $tabErreurs["Titre"] = $strTitre;
                                        $tabErreurs["Salle"] = $intSalle;
                                        $tabErreurs["Rayon"] = $strRayon;
                                        $tabErreurs["Genre"] = $strGenre;
                                        $tabErreurs["Date"] = $strDate;
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
                            $tabErreurs["Erreur"] = "Aucun ouvrage n'a été transmis pour modification !";
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
                        <form action="modifierOuvrage.php" method="post">
                            <div class="corps-form">
                                <fieldset>
                                    <legend>Modifier un ouvrage</legend>
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
                                                <label for="txtTitre">
                                                    Titre :
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" id="txtTitre" 
                                                    name="txtTitre"
                                                    size="50" maxlength="128"
                                                    value="<?php echo $strTitre ?>"
                                                />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="rbnSalle">Salle :</label>
                                            </td>
                                            <td>
                                                <input type="radio" id="rbnSalle" name="rbnSalle" value="1" 
                                                    <?php
                                                        if ($intSalle == 1) {
                                                            echo 'checked="checked"';
                                                        }
                                                    ?>
                                                />
                                                <label>1</label>
                                                <input type="radio" id="rbnSalle" name="rbnSalle" value="2" 
                                                    <?php
                                                        if ($intSalle == 2) {
                                                            echo 'checked="checked"';
                                                        }
                                                    ?>
                                                />
                                                <label>2</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="txtRayon">
                                                    Rayon :
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" id="txtRayon" 
                                                    name="txtRayon"
                                                    size="2" maxlength="2"
                                                    <?php
                                                        if (!empty($strRayon)) {
                                                            echo ' value="'.$strRayon.'"';
                                                        }
                                                    ?>
                                                />
                                            </td>
                                        </tr>                                        
                                        <tr>
                                            <td>
                                                <label for="cbxGenres">
                                                    Genre :
                                                </label>
                                            </td>
                                            <td>
                                                <?php 
                                                    $strSQL = "SELECT code_genre, lib_genre FROM genre";
                                                    $lesGenres = getRows($cnx, $strSQL);
                                                    afficherListe($lesGenres,"cbxGenres",$strGenre,"");
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="txtDate">
                                                    Acquisition :
                                                </label>
                                            </td>
                                            <td>
                                                <input 
                                                    type="date" id="txtDate" 
                                                    name="txtDate" 
                                                    <?php
                                                        if (!empty($strDate)) {
                                                            echo ' value="'.$strDate.'"';
                                                        }
                                                        else {
                                                            echo ' value="'.date('Y-m-d').'"';
                                                        }
                                                    ?>
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