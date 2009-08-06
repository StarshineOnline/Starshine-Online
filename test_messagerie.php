<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
$textures = false;
include_once(root.'haut.php');
include_once(root.'haut_site.php');
include_once(root.'class/messagerie_thread.class.php');
include_once(root.'class/messagerie_message.class.php');
include_once(root.'class/messagerie_etat_message.class.php');
include_once(root.'class/messagerie.class.php');

echo '<pre>';
echo '<h2>on créer un objet messagerie pour le perso Mylok (426)</h2>';
$messagerie = new messagerie(426);
print_r($messagerie);

echo '<h2>on récupère tous les threads personnels pour ce perso </h2>';
$messagerie = new messagerie(426);
$messagerie->get_threads('perso');
print_r($messagerie);

echo '<h2>on récupère tous les threads de groupe ainsi que les 5 premiers messages de chaque thread pour ce perso </h2>';
$messagerie = new messagerie(426);
$messagerie->get_threads('groupe', 'DESC', true, 5);
print_r($messagerie);

echo '<h2>on récupère le nombre de message non lus pour ce perso </h2>';
$messagerie = new messagerie(426);
print_r($messagerie->get_non_lu());

echo '<h2>on récupère le nombre de messages non lu pour le thread 2 (et pour ce perso)</h2>';
$messagerie = new messagerie(426);
print_r($messagerie->get_thread_non_lu(2));

echo '<h2>Un exemple de comment générer la première page de message de groupe d\'un perso</h2>';
$messagerie = new messagerie(426);
$messagerie->get_threads('groupe', 'ASC', true, 1);

echo '<ul>';
foreach($messagerie->threads as $thread)
{
	$non_lu = $messagerie->get_thread_non_lu($thread->id_thread);
	if($non_lu > 0) $bold = 'coupcritique';
	else $bold = '';
	?>
	<li class="<?php echo $bold; ?>">(<?php echo $non_lu; ?>) <?php echo $thread->messages[0]->titre; ?> par <?php echo $thread->messages[0]->nom_auteur; ?> le <?php echo $thread->messages[0]->date; ?></li>
	<?php
}
echo '</ul>';

echo '<h2>Un exemple de comment générer l\'affichage du thread 1 pour un perso</h2>';
echo '</pre>';
$messagerie = new messagerie(426);
$messagerie->get_thread(1, 'all', 'DESC');
foreach($messagerie->thread->messages as $message)
{
	if($message->etat == 'non_lu') $image = '<img src="http://www.starshine-online.com/image/favoris.png" style="vertical-align : top;" />';
	else $image = '';
	?>
	<div class="message" style="width : 500px;">
		<h3 class="message_entete"><?php echo $image.' '.$message->titre; ?><span class="xsmall"> par <?php echo $message->nom_auteur; ?> le <?php echo $message->date; ?></span></h3>
		<p class="information_case">
			<span class="xsmall"><?php echo $message->etat; ?></span><br />
			<?php echo $message->message; ?>
		</p>
	</div>
	<?php
}

echo '<h2>Création d\'un message dans le thread 1</h2>';
$messagerie = new messagerie(426);
//$messagerie->envoi_message(1, 0, 'Message créer via test_messagerie.php', 'toutoutititoto', 1098);
?>