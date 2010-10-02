<?php
if (file_exists('../root.php'))
		 include_once('../root.php');

/**
 * @file maladie.class.php
 */

class maladie
{

	/**
	 * Gere la degenerescence
	 */
	static function degenerescence(&$entite)
	{
		if ($entite->is_buff('maladie_degenerescence'))
		{
			$malignite = ($entite->get_buff('maladie_degenerescence', 'effet') / 100)+1;
			print_debug($entite->get_nom().' est sous degenerescence, malignite '.$malignite);
			$entite->set_reserve(floor($entite->get_reserve() / $malignite));
		}
	}
}

?>