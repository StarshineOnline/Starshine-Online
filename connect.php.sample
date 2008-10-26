<?php // -*- php -*-
/**
 * file connect.php
 * Objet pour l'accès à la base de données.
 * Définit les paramètres d'accès et instancie l'objet permettant d'accéder à la
 * base de données.  
 */

/// Adresse du serveur de base de données.
$cfg["sql"]['host'] = "localhost";

/// Nom d'utilisateur pour la base de données.
$cfg["sql"]['user'] = "root";
// Mot de passe pour la base de données.
$cfg["sql"]['pass'] = "";
/// Nom de la base de donneés.
$cfg["sql"]['db'] = "starshine";


$cfg["sql"]['encoding'] = "utf8";///< Encodage de la base de donneés.

// Paramètres locaux, à ne pas mettre dans le SVN
//if (file_exists('connect.local.php')) include_once('connect.local.php');

/// Objet gérant les accès à la base de données.
$db = new db($cfg);

?>