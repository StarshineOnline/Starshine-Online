<?php
$textures = false;
include('haut.php');
setlocale(LC_ALL, 'fr_FR', 'FRA');
include('haut_site.php');
if ($G_maintenance)
{
	echo 'Starshine-online est actuellement en cours de mis à jour.<br />
	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
}
else
{
	include('menu.php');
	//Si le joueur est connecté on affiche le menu de droite
?>
	<div id="contenu">
		<div id="centre2">
			<h2>Modifications des sorts et compétences 0.5</h2>
			<?php
			require_once('menu_0_5.php');
			?>
        	<h2>Sorts de combat :</h2>
			<h3>Appel de la for&ecirc;t :</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			            <td><font size="2">4</font></td>
			            <td><font size="2">5</font></td>
			
			            <td><font size="2">6</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">2 RM, r&eacute;duit de 1 les RM</font></td>
			            <td><font size="2">1 RM, r&eacute;duit de 1 les RM</font></td>
			
			            <td><font size="2">2 RM, r&eacute;duit de 2 les RM</font></td>
			            <td><font size="2">1 RM, r&eacute;duit de 2 les RM</font></td>
			            <td><font size="2">2 RM, r&eacute;duit de 3 les RM</font></td>
			            <td><font size="2">1 RM, r&eacute;duit de 3 les RM</font></td>
			        </tr>
			
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">2 RM, r&eacute;duit de 1 les RM, dure 5 rounds</font></td>
			            <td><font size="2">2 RM, r&eacute;duit de 1 les RM, dure 6 rounds</font></td>
			            <td><font size="2">2 RM, r&eacute;duit de 1 les RM, dure 7 rounds</font></td>
			
			            <td><font size="2">3 RM, r&eacute;duit de 2 les RM, dure 5 rounds</font></td>
			            <td><font size="2">4 RM, r&eacute;duit de 2 les RM, dure 6 rounds</font></td>
			            <td><font size="2">5 RM, r&eacute;duit de 2 les RM, dure 7 rounds</font></td>
			        </tr>
			    </tbody>
			</table>
			
			</p>
			<h3>B&eacute;n&eacute;diction :</h3>
			<p>Dure 10 rounds (5 rounds avant)</p>
			<p>&nbsp;</p>
			<h3>Aura (toutes) :</h3>
			<p>Dure 20 rounds (10 avant)</p>
			<p>&nbsp;</p>
			<h3>Brulure de mana :</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			            <td><font size="2">4</font></td>
			
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">Br&ucirc;le 2 RM, inflige 2 d&eacute;gats</font></td>
			            <td><font size="2">Br&ucirc;le 2 RM, inflige 4 d&eacute;gats</font></td>
			
			            <td><font size="2">Br&ucirc;le 3 RM, inflige 6 d&eacute;gats</font></td>
			            <td><font size="2">Br&ucirc;le 3 RM, inflige 9 d&eacute;gats</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			
			            <td><font size="2">Br&ucirc;le 5 RM, inflige 5 d&eacute;gats</font></td>
			            <td><font size="2">Br&ucirc;le 6 RM, inflige 6 d&eacute;gats</font></td>
			            <td><font size="2">Br&ucirc;le 6 RM, inflige 9 d&eacute;gats</font></td>
			            <td><font size="2">Br&ucirc;le 6 RM, inflige 12 d&eacute;gats</font></td>
			
			        </tr>
			    </tbody>
			</table>
			<h3>Frappe t&eacute;llurique</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			
			            <td><font size="2">1</font></td>
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			            <td><font size="2">4</font></td>
			            <td><font size="2">5</font></td>
			        </tr>
			
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">Augmente les d&eacute;gats de 1</font></td>
			            <td><font size="2">Augmente les d&eacute;gats de 1</font></td>
			            <td><font size="2">Augmente les d&eacute;gats de 2</font></td>
			
			            <td><font size="2">Augmente les d&eacute;gats de 2</font></td>
			            <td><font size="2">Augmente les d&eacute;gats de 3</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">Augmente les d&eacute;gats de 1, dure 5 rounds<br />
			
			            </font></td>
			            <td><font size="2">Augmente les d&eacute;gats de 1, dure 6 rounds</font></td>
			            <td><font size="2">Augmente les d&eacute;gats de 1, dure 7 rounds</font></td>
			            <td><font size="2">Augmente les d&eacute;gats de 2, dure 5 rounds</font></td>
			            <td><font size="2">Augmente les d&eacute;gats de 2, dure 6 rounds</font></td>
			
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3>Cisaillement du vent</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			            <td><font size="2">4</font></td>
			            <td><font size="2">5</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			
			            <td><font size="2">20% de chance de gagner 1 PA</font></td>
			            <td><font size="2">25% de chance de gagner 1 PA</font></td>
			            <td><font size="2">30% de chance de gagner 1 PA</font></td>
			            <td><font size="2">35% de chance de gagner 1 PA</font></td>
			            <td><font size="2">40% de chance de gagner 1 PA</font></td>
			        </tr>
			
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">12% de chance de gagner 1 PA</font></td>
			            <td><font size="2">14% de chance de gagner 1 PA</font></td>
			            <td><font size="2">16% de chance de gagner 1 PA</font></td>
			            <td><font size="2">18% de chance de gagner 1 PA</font></td>
			
			            <td><font size="2">20% de chance de gagner 1 PA</font></td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3>Liens sylvestres</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			            <td><font size="2">4</font></td>
			            <td><font size="2">5</font></td>
			
			            <td><font size="2">6</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">D&eacute;gats 1 par round, dure 4 rounds</font></td>
			            <td><font size="2">D&eacute;gats 1 par round, dure 5 rounds</font></td>
			
			            <td><font size="2">D&eacute;gats 2 par round, dure 4 rounds</font></td>
			            <td><font size="2">D&eacute;gats 2 par round, dure 5 rounds</font></td>
			            <td><font size="2">D&eacute;gats 3 par round, dure 4 rounds</font></td>
			            <td><font size="2">D&eacute;gats 3 par round, dure 5 rounds</font></td>
			        </tr>
			
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">D&eacute;gats 2 par round, dure 6 rounds</font></td>
			            <td><font size="2">D&eacute;gats 2 par round, dure 7 rounds</font></td>
			            <td><font size="2">D&eacute;gats 2 par round, dure 9 rounds</font></td>
			
			            <td><font size="2">D&eacute;gats 3 par round, dure 6 rounds</font></td>
			            <td><font size="2">D&eacute;gats 3 par round, dure 7 rounds</font></td>
			            <td><font size="2">D&eacute;gats 3 par round, dure 9 rounds</font></td>
			        </tr>
			    </tbody>
			</table>
			
			</p>
			<h3>Pacte de sang</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			            <td><font size="2">2</font></td>
			
			            <td><font size="2">3</font></td>
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			
			            <td><font size="2">Coute 1.5% des HP</font></td>
			            <td><font size="2">Coute 1.9% des HP</font></td>
			            <td><font size="2">Coute 2.3% des HP</font></td>
			            <td><font size="2">Coute 2.7% des HP</font></td>
			            <td><font size="2">Coute 3.1% des HP</font></td>
			            <td><font size="2">Coute 3.5% des HP</font></td>
			
			        </tr>
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">Coute 0.7% des HP</font></td>
			            <td><font size="2">Coute 0.8% des HP</font></td>
			            <td><font size="2">Coute 0.9% des HP</font></td>
			            <td><font size="2">Coute 1% des HP</font></td>
			
			            <td><font size="2">Coute 1.1% des HP</font></td>
			            <td><font size="2">Coute 1.2% des HP</font></td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3>R&eacute;cup&eacute;ration</h3>
			<p>
			
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			            <td><font size="2">4</font></td>
			
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">Redonne 4 HP par round</font></td>
			            <td><font size="2">Redonne 6 HP par round</font></td>
			            <td><font size="2">Redonne 8 HP par round</font></td>
			            <td><font size="2">Redonne 10 HP par round</font></td>
			
			        </tr>
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">Redonne 2 HP par round</font></td>
			            <td><font size="2">Redonne 3 HP par round</font></td>
			            <td><font size="2">Redonne 4 HP par round</font></td>
			            <td><font size="2">Redonne 5 HP par round</font></td>
			
			        </tr>
			    </tbody>
			</table>
			<h3>Boule de feu 4</h3> n&eacute;cessite 120 en magie &eacute;l&eacute;mentaire (100 avant)
			<h3>Boule de feu 5</h3> n&eacute;cessite 180 en magie &eacute;l&eacute;mentaire (160 avant)
			
			<h3>Trait de feu 5</h3> n&eacute;cessite 130 en magie &eacute;l&eacute;mentaire (120 avant)
			</p>
			<h2>Sorts hors combat</h2>
			<h3>Du corps &agrave; l'esprit</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			            <td><font size="2">2</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">Redonne 20 HP</font></td>
			
			            <td><font size="2">Redonne 30 HP</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">Redonne 40 HP</font></td>
			            <td><font size="2">Redonne 60 HP</font></td>
			        </tr>
			
			    </tbody>
			</table>
			</p>
			<h3>Armure en &eacute;pines</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">25 MP / 10% retourn&eacute;<br />
			            </font></td>
			
			            <td><font size="2">28 MP</font><font size="2"> / 20% retourn&eacute;</font></td>
			            <td><font size="2">32 MP</font><font size="2"> / 30% retourn&eacute;</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			
			            <td><font size="2">25 MP</font><font size="2"> / 10% retourn&eacute;</font></td>
			            <td><font size="2">30 MP</font><font size="2"> / 17% retourn&eacute;</font></td>
			            <td><font size="2">37 MP</font><font size="2"> / 25% retourn&eacute;</font></td>
			        </tr>
			
			    </tbody>
			</table>
			</p>
			<h3>Inspiration</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">+1 RM</font></td>
			            <td><font size="2">+2 RM</font></td>
			
			            <td><font size="2">+3 RM</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">+2 RM</font></td>
			            <td><font size="2">+3 RM</font></td>
			            <td><font size="2">+4 RM</font></td>
			
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3>Sacrifice</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">+2 RM</font></td>
			            <td><font size="2">+3 RM</font></td>
			
			            <td><font size="2">+4 RM</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">+3 RM</font></td>
			            <td><font size="2">+4 RM</font></td>
			            <td><font size="2">+5 RM</font></td>
			
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3>Ralentissement</h3>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td><font size="2">1</font></td>
			
			            <td><font size="2">2</font></td>
			            <td><font size="2">3</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Actuel</font></td>
			            <td><font size="2">Augmente les PA pour attaquer de 1</font></td>
			            <td><font size="2">Augmente les PA pour attaquer de 1</font></td>
			
			            <td><font size="2">Augmente les PA pour attaquer de 1</font></td>
			        </tr>
			        <tr>
			            <td><font size="2">Modifi&eacute;</font></td>
			            <td><font size="2">Augmente les PA pour attaquer de 1</font></td>
			            <td><font size="2">Augmente les PA pour attaquer de 2</font></td>
			            <td><font size="2">Augmente les PA pour attaquer de 3</font></td>
			
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3>Soins (tous) :</h3>
			<p>Tous les effets * 2</p>
			<h2>Comp&eacute;tences de combat</h2>
			<h3><span style="font-weight: bold;">Berzeker</span></h3> dure 10 rounds (5 avant)</p>
			
			<h3><span style="font-weight: bold;">Postures (toutes)</span></h3> dure 20 rounds, 10 avant</p>
			<h2>Comp&eacute;tences hors combat</h2>
			<h3><span style="font-weight: bold;">Cri de victoire 3</span></h3> augmente les d&eacute;gats de 3 (4 avant)</p>
		</div>
<?php
	include('menu_d.php');
?>
</div>
<?php
	include('bas.php');
}
?>