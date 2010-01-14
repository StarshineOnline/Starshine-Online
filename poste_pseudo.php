<?php
if (file_exists('root.php'))
  include_once('root.php');

//JOURNALIER POP DES MONSTRES //

include_once(root.'class/db.class.php');
//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');
include_once(root.'fonction/security.inc.php');

	if(array_key_exists('queryString', $_POST)) 
	{
        $queryString = sSQL($_POST['queryString']);
        if(strlen($queryString) >0) 
        {

	        $requeteSQL = $db->query("SELECT nom FROM perso WHERE nom LIKE '$queryString%' LIMIT 10");
    	    if($db->num_rows($requeteSQL) > 0) 
    	    {
    	    	
    	    	echo '<ul>';
	            while ($result = $db->read_object($requeteSQL))
    	            echo '<li onclick="remplir(\'perso_envoi\', \''.$result->nom.'\', \'suggestion\');">'.$result->nom.'</li>';
    	        echo '</ul>';
        	} 
    	} 
	}
?>