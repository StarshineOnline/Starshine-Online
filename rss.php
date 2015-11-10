<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
require_once('inc/variable.inc.php');
require_once('class/baseClass.class.php');
require_once('class/db.class.php');
require_once('fonction/time.inc.php');
require_once('fonction/base.inc.php');
require_once('fonction/equipement.inc.php');
include_once(root.'fonction/security.inc.php');
include_once(root.'connect.php');

//R�cup�ration du code
if(array_key_exists('code', $_GET))
{
	$requete = "SELECT id_perso FROM perso_code WHERE code = '".sSQL($_GET['code'])."'";
	$req = $db->query($requete);
	if($db->num_rows == 1)
	{
		$row = $db->read_assoc($req);
		$joueur = recupperso($row['id_perso']);
		$prochain_level = prochain_level($joueur['level']);
		$pourcent_level = progression_level(level_courant($joueur['exp']));
?>
<rss version="2.0">
	<channel>
		<title>Starshine-Online Player Viewer</title>
		<link>http://www.starshine-online.com/</link>
		<description>Suivi d'un personnage de SSO.</description>
		<lastBuildDate>Thu, 18 Sep 2008 08:35:25 +0000</lastBuildDate>
		<generator>SSO Viewer</generator>
		<item>
			<title><?php echo $joueur->get_nom(); ?></title>
			<link>http://www.starshine-online.com/interface.php</link>
			<description></description>
			<pubDate>Thu, 18 Sep 2008 08:35:25 +0000</pubDate>
		</item>
		<item>
			<title>HP : <?php echo $joueur['hp']; ?> / <?php echo $joueur['hp_max']; ?> - PA : <?php echo $joueur['pa']; ?> / 180</title>
			<link>http://www.starshine-online.com/interface.php</link>
			<description></description>
			<pubDate>Thu, 18 Sep 2008 08:35:25 +0000</pubDate>
		</item>
		<item>
			<title>MP : <?php echo $joueur['mp']; ?> / <?php echo $joueur['mp_max']; ?> - X : <?php echo $joueur['x']; ?> - Y : <?php echo $joueur['y']; ?></title>
			<link>http://www.starshine-online.com/interface.php</link>
			<description></description>
			<pubDate>Thu, 18 Sep 2008 08:35:25 +0000</pubDate>
		</item>
		<item>
			<title>Honneur : <?php echo $joueur['honneur']; ?> - Star : <?php echo $joueur['star']; ?> - Niveau : <?php echo $joueur['level']; ?> - XP : <?php echo $pourcent_level; ?>%</title>
			<link>http://www.starshine-online.com/interface.php</link>
			<description></description>
			<pubDate>Thu, 18 Sep 2008 08:35:25 +0000</pubDate>
		</item>
		<?php
		foreach($joueur['buff'] as $key => $buff)
		{
			?>
			<item>
				<title><?php echo $buff['nom']; ?></title>
				<link>http://www.starshine-online.com/interface.php</link>
				<description><?php echo $buff['description']; ?></description>
				<pubDate>Thu, 18 Sep 2008 08:35:25 +0000</pubDate>
			</item>
			<?php
		}
		?>
		<?php
		foreach($joueur['debuff'] as $key => $buff)
		{
			?>
			<item>
				<title><?php echo $buff['nom']; ?></title>
				<link>http://www.starshine-online.com/interface.php</link>
				<description><?php echo $buff['description']; ?></description>
				<pubDate>Thu, 18 Sep 2008 08:35:25 +0000</pubDate>
			</item>
			<?php
		}
		?>
	</channel>
</rss>
<?php
	}
}
?>
