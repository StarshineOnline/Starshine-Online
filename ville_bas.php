<p style="text-align : center;">
<?php
if (!$W_case) $W_case = $_GET['poscase'];

if(is_ville($W_case))
{
?>
<a href="ville.php?poscase=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href,'carte')"><img src="image/ville/retour_ville.png" alt="Retour au menu de la ville" title="Retour au menu de la ville" /></a>
<a href="boutique.php?type=arme&amp;poscase=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href,'carte')" title="Forgeron"><img src="image/ville/forgeron.png" alt="Forgeron" /></a>
<a href="boutique.php?type=armure&amp;poscase=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href,'carte')" title="Armurerie"><img src="image/ville/armurerie.png" alt="Armurerie" /></a>
<a href="hotel.php?poscase=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href,'carte')" title="Hotel des ventes"><img src="image/ville/hotel_des_ventes.png" alt="Hotel des ventes" /></a>
<a href="ecolemagie.php?poscase=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href,'carte')" title="Ecole de magie"><img src="image/ville/ecole_de_magie.png" alt="Ecole de magie" /></a>
<a href="ecolecombat.php?poscase=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href,'carte')" title="Ecole de combat"><img src="image/ville/ecole_de_combat.png" alt="Ecole de combat" /></a>
<a href="bureau_quete.php?poscase=<?php echo $W_case; ?>" onclick="return envoiInfo(this.href,'carte')" title="Bureau des quêtes"><img src="image/ville/bureau_des_quete.png" alt="Bureau des quètes" /></a>
<?php
}
?>
<a href="taverne.php?poscase=<?php echo $W_case; ?>&amp;fort=<?php echo $W_fort;?>" onclick="return envoiInfo(this.href,'carte')" title="Taverne"><img src="image/ville/taverne.png" alt="Taverne" /></a>

</p>

