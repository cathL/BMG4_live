<?php

/*
 * PHP - Bibliothèques de fonctions
 * Fonctions génériques d'accès aux données
 */

/**
 * Connexion au serveur et à la base
 * 
 * Les valeurs sont dans le fichier de configuration
 * @return      un objet de la classe PDO
 */
function connectDB() {
    try {
        $dsn = 'mysql:host='.DB_SERVER.';dbname='.DB_DATABASE;
        $extraParams = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        $pdo = new PDO($dsn, DB_USER, DB_PWD, $extraParams);
    } catch (PDOException $e) {
        $pdo = null;
    }
    return $pdo;
}

/**
 * Déconnexion
 * 
 * @param       $connexion : un objet de la classe PDO représentant la connexion à fermer
 */
function disconnectDB($connexion) {
    $connexion = null;
}

/**
 * Exécute une requête SQL et retourne un jeu d'enregistrements
 * 
 * @param 	$sql : la requête SQL à exécuter
 * @return 	un objet de la classe PDOStatement
 */
function getRows($cnx, $sql) {
    if ($cnx != null) {
        try {
            $res = $cnx->query($sql);
        } catch (PDOException $e) {
            $res = false;
        }
    } else {
        $res = false;
    }
    return $res;
}

/**
 * Exécute une requête SQL et retourne une valeur
 * 
 * @param 	$sql : la requête SQL à exécuter
 * @result 	une valeur
 */
function getValue($cnx, $sql) {
    if ($cnx != null) {
        try {
            $res = $cnx->query($sql);
            $value = $res->fetchColumn(0);
            $res->closeCursor();
        } catch (PDOException $e) {
            $value = false;
        }
    } else {
        $value = false;
    }
    return $value;
}

/**
 * Exécute une requête SQL et retourne le ombre d'enregistrements affectés
 * 
 * @param 	$sql : la requête SQL à exécuter
 * @result 	une valeur entière
 */
function execSQL($cnx, $sql) {
    if ($cnx != null) {
        try {
            $affectedRows = $cnx->exec($sql);
        } catch (PDOException $e) {
            $affectedRows = false;
        }
    } else {
        $affectedRows = false;
    }
    return $affectedRows;
}


