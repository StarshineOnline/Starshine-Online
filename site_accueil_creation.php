<?php
include('./inc/fp.php');
$race = $_GET['race'];
$classe = $_GET['classe'];
if ($classe == 'guerrier') $classe = 'combattant';
if ($classe == 'mage') $classe = 'magicien';
$pseudo = $_GET['pseudo'];
$mdp = $_GET['mdp'];

//Verification sécuritaire
if(!check_secu($pseudo))
{
	echo 'Les caractères spéciaux ne sont pas autorisés';
}
else
{
	//Config punbb groups
	$punbb['elfebois'] = 6;
	$punbb['orc'] = 12;
	$punbb['nain'] = 11;
	$punbb['troll'] = 14;
	$punbb['humain'] = 8;
	$punbb['vampire'] = 15;
	$punbb['elfehaut'] = 7;
	$punbb['humainnoir'] = 9;
	$punbb['scavenger'] = 13;
	$punbb['barbare'] = 5;
	$punbb['mortvivant'] = 10;
	$requete = "SELECT ID FROM perso WHERE nom = '".$pseudo."'";
	$req = $db->query($requete);
	$nombre = $db->num_rows;
	if ($nombre > 0)
	{
		echo 'Erreur nom déjà utilisé';
	}
	else
	{
		include('inc/race.inc.php');
		include('inc/classe.inc.php');
		
		echo '
		<h3>Bienvenue dans Starshine-Online</h3>
		<div style="padding:5px">
			Vous venez de créer un '.$Gtrad[$race].' '.$classe.' du nom de '.$pseudo.'<br />';
			?>
			N'hésitez pas à aller voir régulièrement les informations fournies dans votre forum de race, et de regarder le message de votre roi.<br />
			Bon jeu !<br />
		</div>
		<br />
		<?php
		
		$caracteristiques = $Trace[$race];
		if ($classe == 'combattant')
		{
			$caracteristiques['vie'] = $caracteristiques['vie'] + 1;
			$caracteristiques['force'] = $caracteristiques['force'] + 1;
			$caracteristiques['dexterite'] = $caracteristiques['dexterite'] + 1;
			$sort_jeu = '';
			$sort_combat = '';
			$comp_combat = '7;8';
		}
		else
		{
			$caracteristiques['energie'] = $caracteristiques['energie'] + 1;
			$caracteristiques['volonte'] = $caracteristiques['volonte'] + 1;
			$caracteristiques['puissance'] = $caracteristiques['puissance'] + 1;
			$sort_jeu = '1';
			$sort_combat = '1';
			$comp_combat = '';
		}

		$requete = "SELECT star_nouveau_joueur FROM royaume WHERE ID = ".$caracteristiques['numrace'];
		$req_s = $db->query($requete);
		$row_s = $db->read_row($req_s);

		$nom = trim($pseudo);
		$password = md5($mdp);
		$exp = 0;
		$level = 1;
		$star = $row_s[0];
		$vie = $caracteristiques['vie'];
		$force = $caracteristiques['force'];
		$dexterite = $caracteristiques['dexterite'];
		$puissance = $caracteristiques['puissance'];
		$volonte = $caracteristiques['volonte'];
		$energie = $caracteristiques['energie'];

		if($classe == 'combattant')
		{
			$value = 0;
			$facteur_magie = 2;
		}
		else
		{
			$value = 1;
			$facteur_magie = 1;
		}
		$sort_vie = $value;
		$sort_element = $value;
		$sort_mort = $value;
		$requete = "SELECT id FROM classe WHERE nom = '".ucwords($classe)."'";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$classe_id = $row['id'];
		$hp = floor(sqrt($vie) * 70);
		$hp_max = $hp;
		$mp = $energie * $G_facteur_mana;
		$mp_max = $mp;
		$regen_hp = time();
		$maj_mp = time();
		$maj_hp = time();
		$x = $Trace[$race]['spawn_x'];
		$y = $Trace[$race]['spawn_y'];
		$inventaire = 'O:10:"inventaire":12:{s:4:"cape";N;s:5:"mains";N;s:11:"main_droite";N;s:11:"main_gauche";N;s:5:"torse";N;s:4:"tete";N;s:8:"ceinture";N;s:6:"jambes";N;s:5:"pieds";N;s:3:"dos";N;s:5:"doigt";N;s:3:"cou";N;}';
		$quete = '';
		$time = time();
		
		$requete = "INSERT INTO perso(`ID`,`nom`,`password`,`exp`,`level`,`star`,`vie`,`forcex`,`dexterite`,`puissance`,`volonte`,`energie`,`race`,`classe`, `classe_id`, `inventaire`,`pa`,`dernieraction`, `x`,`y`,`hp`,`hp_max`,`mp`,`mp_max`,`regen_hp`,`maj_mp`,`maj_hp`,`sort_jeu`,`sort_combat`, `comp_combat`, `quete`, `sort_vie`, `sort_element`, `sort_mort`, `facteur_magie`)
		VALUES ('','$nom','$password','$exp','$level','$star','$vie','$force','$dexterite','$puissance','$volonte','$energie','$race','$classe', $classe_id, '$inventaire','180','$time',$x,$y,'$hp','$hp_max','$mp','$mp_max','$regen_hp','$maj_mp','$maj_hp', '$sort_jeu', '$sort_combat', '$comp_combat', '$quete', '$sort_vie', '$sort_element', '$sort_mort', '$facteur_magie')";
		if(!$db->query($requete))
		{
			echo $requete.'<br />';
		}
		else
		{
		    //Envoi du message au joueur
		    $id_groupe = 0;
		    $id_dest = 0;
		    $id_thread = 0;
		    $id_dest = $db->last_insert_id();
		    $messagerie = new messagerie(0);
		    $titre = 'Bienvenue sur Starshine-Online';
		    $message = 'Vous faites maintenant parti de la grande aventure Starshine-Online.
Pour vous aidez plusieurs outils sont a votre disposition :
L\'aide : [url]http://wiki.starshine-online.com[/url]
Le forum : [url]http://forum.starshine-online.com[/url]
Votre forum de race : [url]http://forum.starshine-online.com/viewforum.php?id='.$Trace[$race]['forum_id'].'[/url]
Les logins et mot de passe pour se connecter a ces forums sont les m�mes que ceux du jeu.

En espérant que votre périple se passera bien.
Bon jeu !';
		    $messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe);
		}
		require('connect_forum.php');
		//Création de l'utilisateur dans le forum
		$requete = "INSERT INTO punbbusers(`group_id`, `username`, `password`, `language`, `style`, `registered`) VALUES('".$punbb[$race]."', '$nom', '".sha1($mdp)."', 'French', 'SSO', '".time()."')";
		$db_forum->query($requete);
	}
}