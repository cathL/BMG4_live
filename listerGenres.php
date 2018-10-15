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
            <a href="ajouterGenre.php" title="Ajouter">
                Ajouter un genre
            </a>
            <div class="corps-form">
                <!--- afficher la liste des genres -->
                <fieldset>	
                    <legend>Genres</legend>
                    <div id="object-list">
                        <?php
                        // ouvrir une connexion
                        $cnx = connectDB();
                        // récupérer les genres
                        $strSQL = "SELECT code_genre as Code, "
                            . "lib_genre as 'Libellé' "
                            . "FROM genre ";
                        $lesGenres = getRows($cnx, $strSQL);
                        // afficher le nombre de genres      
                        $nbGenres = $lesGenres->rowCount();
                        echo '<span>'.$nbGenres.' genre(s) trouvé(s)'
                                . '</span><br /><br />';
                        // afficher un tableau des genres
                        if ($nbGenres > 0) {
                            // création du tableau
                            echo '<table>';
                            // affichage de l'entete du tableau 
                            echo '<tr>';
                            // création entete tableau avec noms de colonnes  
                            echo '<th>Code</th>';
                            echo '<th>Libellé</th>';
                            echo '</tr>';
                            // affichage des lignes du tableau
                            $n = 0;
                            foreach($lesGenres as $ligne)  {                                                            
                                if (($n % 2) == 1) {
                                    echo '<tr class="impair">';
                                }
                                else {
                                    echo '<tr class="pair">';
                                }
                                // afficher la colonne 1 dans un hyperlien
                                echo '<td><a href="afficherGenre.php?id='
                                    .$ligne[0].'">'.$ligne[0].'</a></td>';
                                // afficher les colonnes suivantes
                                echo '<td>'.$ligne[1].'</td>';
                                echo '</tr>';
                                $n++;
                            }
                            echo '</table>';
                        }
                        else {			
                            echo "Aucun genre trouvé !";
                        }		
                        $lesGenres->closeCursor();
                        disconnectDB($cnx);
                        ?>
                    </div>
                </fieldset>
            </div>
        </div>          
        <?php include("include/footer.php") ; ?>
    </body>
</html>