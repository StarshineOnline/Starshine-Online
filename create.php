<?php
include('haut.php');

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

include('haut_site.php');
if ($maintenance)
{
	echo 'Starshine-online est actuellement en refonte compl�te, l\'exp�rience acqu�rie gr�ce � l\'alpha m\'a permis de voir les gros probl�mes qui pourraient se poser.<br />
	Je vais donc travailler sur la b�ta.<br />';
}
else
{
	include('menu.php');
	?>
	<div id="contenu">
		<div id="centre2">
		<div class="titre">Cr�ation d'un nouveau personnage :</div>
<?php

if (isset($_GET['direction'])) $direction = $_GET['direction'];
elseif (isset($_POST['direction'])) $direction = $_POST['direction'];

$races = array('barbare', 'elfebois', 'elfehaut', 'humain', 'humainnoir', 'mortvivant', 'nain', 'orc', 'scavenger', 'troll', 'vampire');
foreach($races as $race)
{
	$requete = "SELECT star_nouveau_joueur FROM royaume WHERE ID = ".$Trace[$race]['numrace'];
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$stars[$race] = $row[0];
	$requete = "SELECT propagande FROM motk WHERE id_royaume = ".$Trace[$race]['numrace'];
	$req = $db->query($requete);
	$row = $db->read_row($req);
	$propa = $row[0];
	$propa = htmlspecialchars(stripslashes($propa));
	$propa = str_replace('[br]', '<br />', $propa);
	$propagande[$race] = $propa;
}
//PHASE 0
if(!isset($direction))
{
	//Acceptation de la charte
	?>
		<div class="titre">
			Charte d'Admission au Jeu : Starshine-Online
		</div>
		<p>
		Starshine-online est un jeu php gratuit cr�� par des b�n�voles. La pr�sente charte a pour but de permettre aux joueurs de jouer dans l'environement le plus agr�able possible, s'ils respectent les quelques r�gles �tablies ci-dessous.<br />
		Tout manquement ou tout abus fera l'objet de sanctions pouvant aller jusqu'au banissement d�finitif du joueur.<br />
		De mani�re g�n�rale, il est attendu de la part des joueurs un comportement fair-play visant � respecter l'amusement de tous.<br />
		</p>
		<h3>(1) Inscription :</h3>
		<p>
		Le multi-compte (cr�ation de plusieurs personnages pour un seul joueur) est formellement interdit et entrainera le bannissement d�finitif des joueurs utilisant ce syst�me.<br />
		La participation au jeu est enti�rement gratuite et l'inscription est ouverte � tous les internautes disposant d'une adresse Email personnelle et active et d'un acc�s � Internet r�gulier.<br />
		Ce qui se passe dans le monde de Starshine est purement imaginatif, et toute ressemblance avec le monde actuel serait purement involontaire.<br />
		Si vous �tes plusieurs joueurs � partager la m�me connexion, vous devez le d�clarer aux administrateurs.<br />
		</p>
		<h3>(2) Donn�es Personnelles :</h3>
		<p>
		Certaines informations enregistr�es dans notre base de donn�e devant imp�rativement rester confidentielles pour le bon fonctionnement du jeu, le joueur demandant la suppression ou une copie d'une partie ou de toutes les informations de son Personnage ne pourra continuer � jouer avec cet avatar.<br />
		Toutes les donn�es du jeu sont consultables par l'Equipe du jeu, sans restriction.<br />
		</p>
		<h3>(3) Saisies :</h3>
		<p>
		Toute saisie qui sera jug�e comme une atteinte � la morale entra�nera l'�limination imm�diate et sans pr�avis du compte du joueur. Ceci inclu, de mani�re non exhaustive, les Forums, le Chat, les Messages envoy�s aux autres joueurs, les Noms et Descriptifs des personnages, d'objets cr��s ou modifi�s par le joueur. Les Emails envoy�s � d'autres joueurs et ayant trait au jeu seront �galement soumis � cette r�gle.<br />
		Il est �galement interdit d'y faire de la publicit� pour un quelconque produit ou une quelconque marque.<br />
		</p>
		<h3>(4) Vente de comptes :</h3>
		<p>
		La vente pour de l'argent ou des biens mat�riels d'un compte et/ou d'�l�ments du jeu est formellement interdite.<br />
		</p>
		<h3>(5) Multi-Compte :</h3>
		<p>
		Chaque joueur ne peut poss�der qu'un seul et unique compte. Un joueur ne peut donc inscrire et jouer qu'un seul personnage. Si un cas de multi-compte (le fait de jouer plusieurs personnages) est d�cel� et confirm�, les comptes incrimin�s seront purement et simplement supprim�s.<br />
		</p>
		<h3>(6) Jouer un autre personnage :</h3>
		<p>
		Pour plus de convivialit�, il est autoris� occasionnellement de jouer le personnage d'un ami s'il ne peut jouer lui m�me. L'occasionnel se transformant en habitude sera consid�r� comme du multi-compte : chaque joueur doit toujours rester seul ma�tre de son personnage.<br />
		La communication du mot de passe d'un personnage est sous la seule responsabilit� du joueur.<br />
		</p>
		<h3>(7) Echange entre roi et joueurs :</h3>
		<p>
		Starshine �tant un jeu permettant � certains joueurs (les rois) d'en "diriger" d'autres, chaque joueur cr�ant un nouveau personnage doit accepter ce fait, et doit accepter le fait que son roi puisse lui donner des ordres. Un joueur n'est cependant pas tenu de suivre les ordres de son roi.<br />
		Cependant, le Roi jouissant d'un titre sp�cial, il lui sera possible de sanctionner lui m�me des joueurs. Les joueurs cr�ant un personnage dans le monde de Starshine doivent donc accepter cet etat de fait.<br />
		En contrepartie, le Roi doit parler avec respect � ses sujets et doit tenir avec "roleplay" et respect son r�le et sa position. Tout manquement � ces r�gles sera soumis � des sanctions de la part des administrateurs du jeu.<br />
		</p>
		<h3>(8) Exp�rience :</h3>
		<p>
		Starshine-online �tant un jeu de groupe, le partage d'exp�rience et d'�quipement doit se faire �quitablement entre chaque joueur d'un groupe. Le sacrifice de l'�volution d'un joueur pour acc�l�rer la progression d'un autre, et plus g�n�ralement l'utilisation d'un compte au profit d'un autre, sera consid�r� comme du multi-compte.<br />
		</p>
		<h3>(9) Suicide de joueurs arrang� :</h3>
		<p>
		Les joueurs pouvant se tuer entre eux, il est interdit � un joueur de laisser tuer son personnage volontairement par un autre joueur ou d'organiser la mort de son personnage afin de profiter ou faire profiter de la situation et/ou de l'exp�rience et de l'honneur acquis � l'occasion de cette mort.<br />
		Si de l'exp�rience ou honneur venait � �tre gagn�e suite � une mort arrang�e, les joueurs concern�s seront soumis � des sanctions pouvant aller jusqu'au bannissement du compte.<br />
		</p>
		<h3>(10) Messagerie interne :</h3>
		<p>
		Dans le cadre du jeu, une messagerie interne est mise � disposition de tous les personnages. Cette messagerie est une composante du jeu et des interactions entre joueurs, elle ne peut donc pas �tre d�tourn�e � des fins priv�es et personnelles en dehors du contexte du jeu.<br />
		</p>
		<h3>(11) Automatisation :</h3>
		<p>
		Un personnage doit �tre contr�l� par un joueur : il est formellement interdit d'automatiser les actions par quelque moyen que ce soit (script, programme, site web, etc.).<br />
		</p>
		<h3>(12) Bugs :</h3>
		<p>
		Starshine-online est un d�veloppement amateur fait par des personnes 100% b�n�voles. Aussi, le jeu n'est pas � l'abri de bugs et incoh�rences. Chaque joueur est tenu de rapporter (par mail ou sur le forum) au plus vite tout probl�me d�cel�, et toute utilisation abusive ou volontaire d'un bug ou d'une faille se verra sanctionn�e et pourra se solder par la suppression du compte du joueur concern�.<br />
		Des compensations pourront intervenir de la part des admins, mais les admins seront seuls juges.<br />
		</p>
		<h3>(13) Charte d'admission :</h3>
		<p>
		La pr�sente charte doit �tre lue et accept�e dans son int�gralit� pour pouvoir jouer � Starshine-Online.<br />
		Elle est susceptible d'�tre modifi�e par les gestionnaires du jeu. Le cas �ch�ant, il sera demand� aux joueurs d'accepter explicitement les modifications apport�es pour pouvoir continuer � jouer.<br />
		<br />
		ElAndy - Administrateur | Source de la charte : <a href="http://www.mountyhall.com/">Mounty Hall</a><br />
		</p>
<br />
<form method="post" action="create.php?direction=phase1" style="text-align : center;" />
<input type="checkbox" name="charte" id="charte" value="Ok" /> J'ai lu, compris et j'accepte la charte<br />
<input type="submit" value="J'accepte et je passe � l'�tape suivante" />
</form>
</div>
	<?php
}

//PHASE 1
elseif ($direction == 'phase1' AND $_POST['charte'] == 'Ok')
{
?>
<script type="text/javascript">
function switch_race()
{
	race = document.getElementById("race").options.selectedIndex;
	race = document.getElementById("race").options[race].value;
	for(i = 0; i < document.getElementById("race").options.length; i++)
	{
		irace = document.getElementById("race").options[i].value;
		document.getElementById(irace).style.display = 'none';
	}
	document.getElementById(race).style.display = 'block';
	switch_classe();
}
function switch_classe()
{
	classe = document.getElementById("classe").options.selectedIndex;
	classe = document.getElementById("classe").options[classe].value;
	if(classe == 'combattant')
	{
		classe = 'guerrier';
		document.getElementById('magicien').style.display = 'none';
		document.getElementById('combattant').style.display = 'block';
	}
	else
	{
		classe = 'mage';
		document.getElementById('magicien').style.display = 'block';
		document.getElementById('combattant').style.display = 'none';
	}
	race = document.getElementById("race").options.selectedIndex;
	race = document.getElementById("race").options[race].value;
	envoiInfo('switch_classe.php?race=' + race + '&classe=' + classe, 'img' + race);
}
</script>
<table border="0" style="width : 700px; border : 0px; background-color : #E4EAF2;">
<tr>
	<td colspan="2">
		<a href="tuto.php">Aide de jeu, pour comprendre dans de plus amples d�tails comment joueur</a><br />
		N'h�sitez pas � faire le tour des races pour en voir toutes les diff�rences, et � passer votre curseur sur les attributs (force, dext�rit�, etc) pour avoir des d�tails sur leur fonctionnement.<br />
		Pour un �quilibrage du jeu, les peuples ayant le moins de joueurs recoivent plus de stars � la cr�ation du personnage.<br />
		<br />
		<strong>Un compte sur le forum sera cr�� automatiquement avec vos informations du jeu.</strong>
	</td>
</tr>
<tr>
	<td style="width : 230px;">
<form action="create.php" method="POST">
Quel sera votre nom ?<br />
<input type="text" name="nom" /><br />
<br />
Veuillez indiquer un mot de passe :<br />
<input type="password" name="password" /><br />
Veuillez confirmer votre mot de passe :<br />
<input type="password" name="password2" /><br />
<br />
Choisissez une race :<br />
<select name="race" id="race" onChange="switch_race();">
	<?php
	$true = true;
	$requete = "SELECT race FROM royaume WHERE race != '' ORDER BY star_nouveau_joueur DESC, race ASC";
	$req = $db->query($requete);
	while($row = $db->read_row($req))
	{
		if($true)
		{
			$race_1 = $row[0];
			$true = false;
		}
		echo '<option value="'.$row[0].'">'.$Gtrad[$row[0]].'</option>';
	}
	?>
</select>
<br />
Choisissez une classe :<br />
<select name="classe" id="classe" onchange="switch_classe();">
	<option value="combattant">Combattant</option>
	<option value="magicien">Magicien</option>
</select><br />
<br />
<input type="hidden" name="direction" value="phase2" />
<input type="submit" value="Cr�er ce personnage" />
</form>
</td>
<td style="vertical-align : top;">
	<?php
	$races = array('barbare', 'elfebois', 'elfehaut', 'humain', 'humainnoir', 'mortvivant', 'nain', 'orc', 'scavenger', 'troll', 'vampire');
	foreach($races as $race)
	{
		if($race == $race_1) $style = ''; else $style = 'display : none;';
		$image = 'image/'.$race;
		if (file_exists($image.'_guerrier.png')) $image .= '_guerrier.png';
		elseif(file_exists($image.'_guerrier.gif')) $image .= '_guerrier.gif';
		elseif (file_exists($image.'_mage.png')) $image .= '_mage.png';
		elseif(file_exists($image.'_mage.gif')) $image .= '_mage.gif';
		elseif (file_exists($image.'.png')) $image .= '.png';
		else $image .= '.gif';
		echo '
		<div id="'.$race.'" style="'.$style.'">
			<h3><div id="img'.$race.'" style="display : inline;"><img src="'.$image.'" style="vertical-align : middle;" /></div> '.$Gtrad[$race].'</h3>
			<strong>Stars au d�but du jeu :</strong> '.$stars[$race].'<br />
			<br />
			<strong>Passif :</strong><br />
			'.$Trace[$race]['passif'].'<br />
			<br />
			<strong>Caract�ristiques :</strong><br />
			<table>
			<tr>
				<td>
					<span onmousemove="javascript:afficheInfo(\'vie'.$race.'\', \'block\', event); document.getElementById(\'vie'.$race.'\').style.zIndex = 2;" onmouseout="javascript:afficheInfo(\'vie'.$race.'\', \'none\', event );" />Vie</span>
					<div class="infobox" id="vie'.$race.'">
						Caract�rise vos points de vie
					</div>
				</td>
				<td>
					'.$Trace[$race]['vie'].'
				</td>
			</tr>
			<tr>
				<td>
					<span onmousemove="javascript:afficheInfo(\'force'.$race.'\', \'block\', event); document.getElementById(\'force'.$race.'\').style.zIndex = 2;" onmouseout="javascript:afficheInfo(\'force'.$race.'\', \'none\', event );" />Force</span>
					<div class="infobox" id="force'.$race.'">
						Augmente vos d�gats physiques, et vous permet de porter de meilleurs armures.
					</div>
				</td>
				<td>
					'.$Trace[$race]['force'].'
				</td>
			</tr>
			<tr>
				<td>
					<span onmousemove="javascript:afficheInfo(\'dex'.$race.'\', \'block\', event); document.getElementById(\'dex'.$race.'\').style.zIndex = 2;" onmouseout="javascript:afficheInfo(\'dex'.$race.'\', \'none\', event );" />Dext�rit�</span>
					<div class="infobox" id="dex'.$race.'">
						Augmente vos chances de toucher, d\'esquiver et de porter des coups critiques
					</div>
				</td>
				<td>
					'.$Trace[$race]['dexterite'].'
				</td>
			</tr>
			<tr>
				<td>
					<span onmousemove="javascript:afficheInfo(\'puiss'.$race.'\', \'block\', event); document.getElementById(\'puiss'.$race.'\').style.zIndex = 2;" onmouseout="javascript:afficheInfo(\'puiss'.$race.'\', \'none\', event );" />Puissance</span>
					<div class="infobox" id="puiss'.$race.'">
						Augmente vos d�gats magiques.
					</div>
				</td>
				<td>
					'.$Trace[$race]['puissance'].'
				</td>
			</tr>
			<tr>
				<td>
					<span onmousemove="javascript:afficheInfo(\'vol'.$race.'\', \'block\', event); document.getElementById(\'vol'.$race.'\').style.zIndex = 2;" onmouseout="javascript:afficheInfo(\'vol'.$race.'\', \'none\', event );" />Volont�</span>
					<div class="infobox" id="vol'.$race.'">
						Augmente vos chances de lancer un sort, d\'esquiver un sort, ou de toucher une cible avec un sort.
					</div>
				</td>
				<td>
					'.$Trace[$race]['volonte'].'
				</td>
			</tr>
			<tr>
				<td>
					<span onmousemove="javascript:afficheInfo(\'ene'.$race.'\', \'block\', event); document.getElementById(\'ene'.$race.'\').style.zIndex = 2;" onmouseout="javascript:afficheInfo(\'ene'.$race.'\', \'none\', event );" />Energie</span>
					<div class="infobox" id="ene'.$race.'">
						Caract�rise vos points de mana
					</div>
				</td>
				<td>
					'.$Trace[$race]['energie'].'
				</td>
			</tr>
			</table>
			<br />
			<strong>Affinit�s magiques :</strong><br />
			Magie de la Vie : '.$Gtrad['affinite'.$Trace[$race]['affinite_sort_vie']].'<br />
			Magie de la Mort : '.$Gtrad['affinite'.$Trace[$race]['affinite_sort_mort']].'<br />
			Magie Elementaire : '.$Gtrad['affinite'.$Trace[$race]['affinite_sort_element']].'<br />
			<br />
			<strong>Propagande Royale :</strong><br />
			'.$propagande[$race].'
			<br />
		</div>';
	}
	?>
	<strong>Bonus de classe :</strong><br />
	<div id="combattant" style="">
		+1 en vie, force et dext�rit�
	</div>
	<div id="magicien" style="display : none;">
		+1 en puissance, volont� et �nergie
	</div>
</td>
</tr>
</table>
<?php
}
//PHASE 2
elseif ($direction == 'phase2')
{
	//Verification d'usage
	if (($_POST['password'] != $_POST['password2']) OR ($_POST['password'] == ''))
	{
		echo 'Erreur dans votre mot de passe';
	}
	elseif ($_POST['nom'] == '')
	{
		echo 'Erreur dans votre pseudo';
	}
	//Verification s�curitaire
	elseif(!check_secu($_POST['nom']))
	{
		echo 'Les caract�res sp�ciaux ne sont pas autoris�s';
	}
	else
	{
	$requete = "SELECT * FROM perso WHERE nom = '".$_POST['nom']."'";
	$req = $db->query($requete);
	$nombre = $db->num_rows;
	if ($nombre > 0)
	{
		echo 'Erreur nom d�j� utilis�';		
	}
	else
	{
		include('inc/race.inc.php');
		include('inc/classe.inc.php');
		
		echo 'Vous venez de cr�er un '.$Gtrad[$_POST['race']].' '.$_POST['classe'].' du nom de '.$_POST['nom'].'<br />';
		?>
		N'h�sitez pas � aller voir r�guli�rement les informations fournies dans votre forum de race, et de regarder le message de votre roi.<br />
		Bon jeu, et bienvenue dans l'univers de Starshine !<br />
		<br />
		<?php
		
		$caracteristiques = $Trace[$_POST['race']];
		if ($_POST['classe'] == 'combattant')
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
?>

	<a href="index.php">Retour � l'index</a>

<?php
	$nom = trim($_POST['nom']);
	$password = md5($_POST['password']);
	$exp = 0;
	$level = 1;
	$star = $stars[$_POST['race']];
	$vie = $caracteristiques['vie'];
	$force = $caracteristiques['force'];
	$dexterite = $caracteristiques['dexterite'];
	$puissance = $caracteristiques['puissance'];
	$volonte = $caracteristiques['volonte'];
	$energie = $caracteristiques['energie'];
	$race = $_POST['race'];
	$classe = $_POST['classe'];
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
	VALUES ('','$nom','$password','$exp','$level','$star','$vie','$force','$dexterite','$puissance','$volonte','$energie','$race','$classe', $classe_id, '$inventaire','200','$time',$x,$y,'$hp','$hp_max','$mp','$mp_max','$regen_hp','$maj_mp','$maj_hp', '$sort_jeu', '$sort_combat', '$comp_combat', '$quete', '$sort_vie', '$sort_element', '$sort_mort', '$facteur_magie')";
	if(!$db->query($requete))
	{
		echo $requete.'<br />';
	}
	//Cr�ation de l'utilisateur dans le forum
	$requete = "INSERT INTO punbbusers(`group_id`, `username`, `password`, `language`, `style`, `registered`) VALUES('".$punbb[$race]."', '$nom', '".sha1($_POST['password'])."', 'French', 'VbStyle-Sand', '".time()."')";
	$db->query($requete);
	}
	}
}
}
?>
		</p>
	</div>
		</div>
</div>
<?php
include('bas.php');

?>