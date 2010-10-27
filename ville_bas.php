<?php
if (file_exists('root.php'))
  include_once('root.php');
?><p style="text-align : center;">
<?php
if(is_ville($joueur->get_x(), $joueur->get_y()))
{
?>
<a href="ville.php" onclick="return envoiInfo(this.href,'carte')"><img src="image/ville/retour_ville.png" alt="Retour au menu de la ville" title="Retour au menu de la ville" /></a>
<a href="boutique.php?type=arme" onclick="return envoiInfo(this.href,'carte')" title="Forgeron"><img src="image/ville/forgeron.png" alt="Forgeron" /></a>
<a href="boutique.php?type=armure" onclick="return envoiInfo(this.href,'carte')" title="Armurerie"><img src="image/ville/armurerie.png" alt="Armurerie" /></a>
<a href="hotel.php" onclick="return envoiInfo(this.href,'carte')" title="Hotel des ventes"><img src="image/ville/hotel_des_ventes.png" alt="Hotel des ventes" /></a>
<a href="ecolemagie.php" onclick="return envoiInfo(this.href,'carte')" title="Ecole de magie"><img src="image/ville/ecole_de_magie.png" alt="Ecole de magie" /></a>
<a href="ecolecombat.php" onclick="return envoiInfo(this.href,'carte')" title="Ecole de combat"><img src="image/ville/ecole_de_combat.png" alt="Ecole de combat" /></a>
<a href="bureau_quete.php" onclick="return envoiInfo(this.href,'carte')" title="Bureau des quêtes"><img src="image/ville/bureau_des_quete.png" alt="Bureau des quêtes" /></a>
<a href="taverne.php" onclick="return envoiInfo(this.href,'carte')" title="Taverne"><img src="image/ville/taverne.png" alt="Taverne" /></a>
</p>
<?php
}
else
{
	$construction = construction::create(array('x', 'y'), 
																			 array($joueur->get_x(),
																						 $joueur->get_y()));
	$batiment = new batiment($construction[0]->get_id_batiment());
	if ($batiment->has_bonus('taverne')) {
?>
<a href="taverne.php" onclick="return envoiInfo(this.href,'carte')" title="Taverne"><img src="image/ville/taverne.png" alt="Taverne" /></a>
<?php
	}
	if ($batiment->has_bonus('quete')) {
?>
<a href="bureau_quete.php" onclick="return envoiInfo(this.href,'carte')" title="Bureau des quêtes"><img src="image/ville/bureau_des_quete.png" alt="Bureau des quêtes" /></a>
<?php
	}
}
?>

