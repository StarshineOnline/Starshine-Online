<?php
		if (isset($_GET['javascript'])) include('inc/fp.php');
		if((is_ville($_SESSION['position']) == 1) AND (!array_key_exists('ville', $_GET) OR (array_key_exists('ville', $_GET) AND $_GET['ville'] == 'no')))
		{
			return_ville('<img src="image/affiche_ville.png" alt="Acc�der � la ville">', $_SESSION['position']);
		}
		else
		{
			?>
			<a class="rose_a" OnClick="javascript:envoiInfo('deplacement.php', 'centre')"><img src="image/affiche_carte.png" alt="Afficher la carte"></a>
			<?php
		}
		require_once('menu_mess.php');
	?>