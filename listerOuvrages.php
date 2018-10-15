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
            <a href="ajouterOuvrage.php" title="Ajouter">
                Ajouter un ouvrage
            </a>
            <div class="corps-form">
                <!--- afficher la liste des ouvrages -->
                <fieldset>	
                    <legend>Ouvrages</legend>
                    <div id="object-list">
                        <?php
                        $cnx = connectDB();
                        // récupérer les ouvrages
                        $strSQL = "SELECT no_ouvrage as ID, "
                                ."titre, "
                                ."lib_genre, "
                                ."auteur, "
                                ."salle, "
                                ."rayon, "
                                ."dernier_pret, "
                                ."disponibilite "
                                ."FROM v_ouvrages "
                                ."ORDER BY titre;";
                        $lesOuvrages = getRows($cnx, $strSQL);
                        // afficher le nombre d'ouvrages      
                        $nbOuvrages = $lesOuvrages->rowCount();
                        echo '<span>'.$nbOuvrages.' ouvrage(s) trouvé(s)'
                                . '</span><br /><br />';
                        // afficher un tableau des ouvrages
                        if ($nbOuvrages > 0) {
                            // création du tableau
                            echo '<table>';
                            // affichage de l'entête du tableau 
                            echo '<tr>'
                                    .'<th>ID</th>'
                                    .'<th>Titre</th>'
                                    .'<th>Genre</th>'
                                    .'<th>Auteur</th>'
                                    .'<th>Salle</th>'
                                    .'<th>Rayon</th>'
                                    .'<th>Dernier prêt</th>'
                                    .'<th>Disponibilité</th>'
                                    .'</tr>';
                            // affichage des lignes du tableau
                            $n = 0;
                            while ($ligne = $lesOuvrages->fetch())  {                                                            
                                if (($n % 2) == 1) {
                                    echo '<tr class="impair">';
                                }
                                else {
                                    echo '<tr class="pair">';
                                }
                                // afficher la colonne 1 dans un hyperlien
                                echo '<td><a href="afficherOuvrage.php?id='
                                    .$ligne[0].'">'.$ligne[0].'</a></td>';
                                // afficher les colonnes suivantes
                                echo '<td>'.$ligne[1].'</td>';
                                echo '<td>'.$ligne[2].'</td>';
                                if ($ligne[3] == 'Indéterminé') {
                                    echo '<td class="erreur">'.$ligne[3].'</td>';
                                }
                                else {
                                    echo '<td>'.$ligne[3].'</td>';
                                }
                                echo '<td>'.$ligne[4].'</td>';
                                echo '<td>'.$ligne[5].'</td>';
                                echo '<td>'.$ligne[6].'</td>';
                                if ($ligne[7] == 'D') {
                                    echo '<td class="center"><img src="img/dispo.png"</td>';
                                }
                                else {
                                    echo '<td class="center"><img src="img/emprunte.png"</td>';
                                }
                                echo '</tr>';
                                $n++;
                            }
                            echo '</table>';
                        }
                        else {			
                            echo "Aucun ouvrage trouvé !";
                        }		
                        $lesOuvrages->closeCursor();
                        disconnectDB($cnx);
                        ?>
                    </div>
                </fieldset>
            </div>
        </div>          
        <?php include("include/footer.php") ; ?>
    </body>
</html>