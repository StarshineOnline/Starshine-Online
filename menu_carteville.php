<?php
if (file_exists('root.php'))
  include_once('root.php');

		if (isset($_GET['javascript'])) include_once(root.'inc/fp.php');
		if((is_ville($_SESSION['position']) == 1) AND (!array_key_exists('ville', $_GET) OR (array_key_exists('ville', $_GET) AND $_GET['ville'] == 'no')))
		{
			return_ville('<img src="image/interface/affiche_ville.png" alt="Accéder à la ville" />', $_SESSION['position']);
		}
		else
		{
			?>
			<a class="rose_a" onclick="envoiInfo('deplacement.php', 'centre')"><img src="image/interface/affiche_carte.png" alt="Afficher la carte" /></a>
			<?php
		}
		require_once('menu_mess.php');
	?>