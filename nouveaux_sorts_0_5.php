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
			<h2>Nouveaux sorts et compétences 0.5</h2>
			<?php
			require_once('menu_0_5.php');
			?>
			<h2>Comp&eacute;tences en Combat</h2>
			
			<p>12 nouvelles comp&eacute;tences</p>
			<h3><strong>Bouclier Protecteur </strong>- Bouclier - 3 RM</h3>
			<p>Augmente votre protection magique de (d&eacute;gat du bouclier) * X pour 10 rounds</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>10</td>
			
			            <td>20</td>
			            <td>30</td>
			        </tr>
			        <tr>
			            <td>Blocage</td>
			            <td>100</td>
			            <td>200</td>
			
			            <td>300</td>
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Feinte</strong> - Dague / Ep&eacute;e - 2 RM</h3>
			<p>Augmente vos chances de toucher de +X% et vos chances de critique de +Y%</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			
			        <tr>
			            <td>X</td>
			            <td>10</td>
			            <td>15</td>
			            <td>20</td>
			        </tr>
			        <tr>
			
			            <td>Y</td>
			            <td>5</td>
			            <td>7</td>
			            <td>10</td>
			        </tr>
			        <tr>
			            <td>M&eacute;l&eacute;e</td>
			
			            <td>150</td>
			            <td>250</td>
			            <td>325</td>
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Attaque de c&ocirc;t&eacute;</strong> - Dague / Ep&eacute;e / Hache - 1 RM</h3>
			
			<p>Augmente vos chances de toucher de +X%, Augmente les chances de vous faire bloquer de +Y%, R&eacute;duit vos chances de critique de +Z%</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>15</td>
			            <td>20</td>
			            <td>25</td>
			
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>15</td>
			            <td>12</td>
			            <td>10</td>
			        </tr>
			
			        <tr>
			            <td>Z</td>
			            <td>15</td>
			            <td>12</td>
			            <td>10</td>
			        </tr>
			        <tr>
			
			            <td>M&eacute;l&eacute;e</td>
			            <td>120</td>
			            <td>200</td>
			            <td>300</td>
			        </tr>
			    </tbody>
			
			</table>
			<h3><strong>Attaque Brutale</strong> - Ep&eacute;e / Hache - 2 RM</h3>
			<p>Augmente vos d&eacute;gats de +X, augmente les chances de vous faire bloquer de +Y%, r&eacute;duits vos chances de critique de +Z%</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>1</td>
			
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>5</td>
			            <td>10</td>
			
			            <td>15</td>
			        </tr>
			        <tr>
			            <td>Z</td>
			            <td>10</td>
			            <td>20</td>
			            <td>30</td>
			
			        </tr>
			        <tr>
			            <td>M&eacute;l&eacute;e</td>
			            <td>140</td>
			            <td>220</td>
			            <td>310</td>
			
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Attaque Rapide</strong> - Dague / Ep&eacute;e - 2 RM</h3>
			<p>Augmente vos chances de toucher de +X%, r&eacute;duits vos chances de critique de +Y%</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			
			            <td>X</td>
			            <td>15</td>
			            <td>25</td>
			            <td>35</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			
			            <td>5</td>
			            <td>7</td>
			            <td>10</td>
			        </tr>
			        <tr>
			            <td>M&eacute;l&eacute;e</td>
			
			            <td>110</td>
			            <td>210</td>
			            <td>320</td>
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Frappe de la derni&egrave;re chance</strong> - Dague / Ep&eacute;e / Hache - 3 RM</h3>
			
			<p>Augmente vos d&eacute;gats de +X, r&eacute;duits votre esquive et protection magique de Y% jusqu'&agrave; la fin du combat</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>3</td>
			            <td>4</td>
			
			            <td>5</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>50</td>
			            <td>45</td>
			            <td>40</td>
			
			        </tr>
			        <tr>
			            <td>M&eacute;l&eacute;e</td>
			            <td>150</td>
			            <td>250</td>
			            <td>400</td>
			
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Fl&egrave;che magn&eacute;tique</strong> - Arc - 5 RM</h3>
			<p>D&eacute;gats normaux, a X% de chance d'enlever de 1 &agrave; Y buffs</p>
			<p>
			
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			
			        <tr>
			            <td>X</td>
			            <td>10</td>
			            <td>20</td>
			            <td>30</td>
			        </tr>
			        <tr>
			
			            <td>Y</td>
			            <td>1</td>
			            <td>2</td>
			            <td>2</td>
			        </tr>
			        <tr>
			            <td>Tir &agrave; distance</td>
			
			            <td>150</td>
			            <td>250</td>
			            <td>325</td>
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Fl&egrave;che de sable</strong> - Arc - 2 RM</h3>
			
			<p>D&eacute;gats - X, pendant Y tours les chances de toucher et de lancer des sorts de l'adversaire sont r&eacute;duits de Z%</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>2</td>
			            <td>1</td>
			            <td>1</td>
			
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>4</td>
			            <td>4</td>
			            <td>5</td>
			        </tr>
			
			        <tr>
			            <td>Z</td>
			            <td>20</td>
			            <td>25</td>
			            <td>30</td>
			        </tr>
			        <tr>
			
			            <td>Tir a distance</td>
			            <td>130</td>
			            <td>240</td>
			            <td>300</td>
			        </tr>
			    </tbody>
			</table>
			
			<h3><strong>Fl&egrave;che empoisonn&eacute;e</strong> - Arc - 6 RM</h3>
			<p>D&eacute;gats + X, empoisonne la cible avec un poison d'effet Y</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>1</td>
			
			            <td>3</td>
			            <td>4</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>3</td>
			            <td>3</td>
			
			            <td>4</td>
			        </tr>
			        <tr>
			            <td>
			            <p>Tir &agrave; distance</p>
			            </td>
			            <td>140</td>
			
			            <td>240</td>
			            <td>310</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Fl&egrave;che rapide</strong> - Arc - 3 RM</h3>
			
			<p>D&eacute;gats normaux. Ignore le blocage, et a X% de chances d'ignorer l'armure</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>10</td>
			            <td>20</td>
			            <td>30</td>
			
			        </tr>
			        <tr>
			            <td>Tir &agrave; distance</td>
			            <td>110</td>
			            <td>220</td>
			            <td>300</td>
			
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Fl&egrave;che sanglante </strong>- Arc - 5 RM</h3>
			<p>Augmente vos chances de faire un coup critique de X%</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			
			            <td>35</td>
			            <td>45</td>
			            <td>60</td>
			        </tr>
			        <tr>
			            <td>Tir &agrave; distance</td>
			
			            <td>130</td>
			            <td>240</td>
			            <td>300</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Fl&egrave;che d&eacute;bilitante</strong> - Arc - 5 RM</h3>
			
			<p>D&eacute;gats normaux. Les chances de lancer des sorts de l'ennemi sont r&eacute;duits de X% pendant 3 rounds</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>25</td>
			            <td>30</td>
			            <td>35</td>
			
			        </tr>
			        <tr>
			            <td>Tir &agrave; distance</td>
			            <td>120</td>
			            <td>230</td>
			            <td>310</td>
			
			        </tr>
			    </tbody>
			</table>
			</p>
			<h2>Comp&eacute;tences Hors combat</h2>
			<p>7 nouvelles comp&eacute;tences</p>
			<h3><strong>Renouveau &eacute;nergique</strong> - Arc - X MP 5 PA</h3>
			
			<p>A chaque fois que vous fa&icirc;tes un critique physique, vous gagnez Y RM</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>&nbsp;30</td>
			            <td>&nbsp;40</td>
			            <td>&nbsp;60</td>
			
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>1</td>
			            <td>3</td>
			            <td>4</td>
			        </tr>
			
			        <tr>
			            <td>Tir &agrave; distance</td>
			            <td>130</td>
			            <td>240</td>
			            <td>320</td>
			        </tr>
			
			    </tbody>
			</table>
			</p>
			<h3><strong>Fl&egrave;ches tranchantes </strong>- Arc - X MP 5 PA</h3>
			<p>Pendant 30 minutes, vos fl&egrave;ches font + Y d&eacute;gats.</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			
			            <td>60</td>
			            <td>45</td>
			            <td>50</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>1</td>
			
			            <td>1</td>
			            <td>2</td>
			        </tr>
			        <tr>
			            <td>Tir &agrave; distance</td>
			            <td>200</td>
			
			            <td>300</td>
			            <td>400</td>
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Oeil du chasseur </strong>- Arc - X MP 5 PA</h3>
			<p>Pendant 24 heures, augmente vos d&eacute;gats de Y contre les betes.
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			
			        <tr>
			            <td>X</td>
			            <td>20</td>
			            <td>30</td>
			            <td>40</td>
			        </tr>
			        <tr>
			
			            <td>Y</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>Tir &agrave; distance</td>
			
			            <td>80</td>
			            <td>150</td>
			            <td>210</td>
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Fouille du gibier</strong> - Arc - X MP 4 PA</h3>
			
			<p>[Groupe] Pendant 24 heures, augmente de Y% les chances de trouver un objet sur un monstre.</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>40</td>
			            <td>50</td>
			            <td>60</td>
			        </tr>
			
			        <tr>
			            <td>Y</td>
			            <td>20</td>
			            <td>30</td>
			            <td>40</td>
			        </tr>
			        <tr>
			
			            <td>Tir &agrave; distance</td>
			            <td>X</td>
			            <td>200</td>
			            <td>350</td>
			        </tr>
			    </tbody>
			
			</table>
			<h3><strong>Pr&eacute;paration de camp</strong> - Arc - X MP 5 PA</h3>
			<p>[Groupe] Pendant 1 semaine, augmente de Y% la regen HP et MP.</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>50</td>
			            <td>55</td>
			
			            <td>60</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>10</td>
			            <td>15</td>
			            <td>20</td>
			
			        </tr>
			        <tr>
			            <td>Tir &agrave; distance</td>
			            <td>X</td>
			            <td>250</td>
			            <td>350</td>
			
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Longue Port&eacute;e</strong> - Arc - X MP 1 PA</h3>
			<p>Votre prochaine attaque b&eacute;n&eacute;ficiera d'une port&eacute;e augmet&eacute;e de 1</p>
			
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>40</td>
			            <td>30</td>
			            <td>20</td>
			        </tr>
			
			        <tr>
			            <td>Tir &agrave; distance</td>
			            <td>X</td>
			            <td>200</td>
			            <td>320</td>
			        </tr>
			
			    </tbody>
			</table>
			</p>
			<h3><strong>Recherche du pr&eacute;cieux</strong> - Dague - X MP 5 PA</h3>
			<p>[Groupe] Pendant 24 heures, augmente l'or r&eacute;cup&eacute;r&eacute; sur les monstre de +Y %</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			
			        <tr>
			            <td>X</td>
			            <td>40</td>
			            <td>50</td>
			            <td>60</td>
			        </tr>
			        <tr>
			
			            <td>Y</td>
			            <td>5</td>
			            <td>10</td>
			            <td>15</td>
			        </tr>
			        <tr>
			            <td>Tir &agrave; distance</td>
			
			            <td>X</td>
			            <td>200</td>
			            <td>350</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h2>Sorts en combat</h2>
			
			<p>11 nouveaux sorts</p>
			<h3><strong>Vortex de vie</strong> - N&eacute;cromancie - 7 RM</h3>
			<p>Vous infligez +X d&eacute;gats, et gagnez 40% en point de vie</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			            <td>8</td>
			
			            <td>9</td>
			            <td>11</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>50</td>
			            <td>140</td>
			
			            <td>230</td>
			            <td>360</td>
			            <td>450</td>
			            <td>550</td>
			        </tr>
			    </tbody>
			</table>
			
			<h3><strong>Vortez de mana</strong> - N&eacute;cromancie - 7 RM</h3>
			<p>Vous infligez +X d&eacute;gats, et gagnez 20% en RM</p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			
			            <td>2</td>
			            <td>3</td>
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			        </tr>
			
			        <tr>
			            <td>X</td>
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			            <td>8</td>
			
			            <td>9</td>
			            <td>11</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>60</td>
			            <td>150</td>
			
			            <td>240</td>
			            <td>370</td>
			            <td>460</td>
			            <td>560</td>
			        </tr>
			    </tbody>
			</table>
			
			<h3><strong>Putr&eacute;faction </strong>- N&eacute;cromancie - 6 RM</h3>
			<p>Vous infligez +X d&eacute;gats. Si l'&eacute;nnemi est empoisonn&eacute; alors les d&eacute;gats d&ucirc;t au poison pour ce tour sont doubl&eacute;s.</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			            <td>4</td>
			            <td>5</td>
			
			            <td>6</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>3</td>
			            <td>4</td>
			            <td>5</td>
			
			            <td>7</td>
			            <td>8</td>
			            <td>10</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>70</td>
			
			            <td>160</td>
			            <td>230</td>
			            <td>350</td>
			            <td>420</td>
			            <td>500</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Brisement d'os </strong>- N&eacute;cromancie - 3 RM</h3>
			
			<p>Vous infligez +X d&eacute;gats. Si l'&eacute;nnemi est paralys&eacute; alors les d&eacute;gats sont doubl&eacute;s.</p>
			<p>&nbsp;</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>0</td>
			            <td>1</td>
			            <td>3</td>
			            <td>4</td>
			
			            <td>6</td>
			            <td>8</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>30</td>
			            <td>100</td>
			
			            <td>200</td>
			            <td>290</td>
			            <td>350</td>
			            <td>400</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Embrasement</strong> - Magie &eacute;l&eacute;mentaire - 6 RM</h3>
			
			<p>Vous infligez +X d&eacute;gats, l'adversaire s'embrase pendant 5 rounds faisant 1 de d&eacute;gats par round</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			
			            <td>3</td>
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			        </tr>
			        <tr>
			            <td>X</td>
			
			            <td>5</td>
			            <td>7</td>
			            <td>9</td>
			            <td>12</td>
			            <td>14</td>
			            <td>17</td>
			
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>120</td>
			            <td>190</td>
			            <td>240</td>
			            <td>290</td>
			            <td>330</td>
			
			            <td>400</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Sph&egrave;re de glace</strong> - Magie &eacute;l&eacute;mentaire - 6 RM</h3>
			<p>Vous infligez +X d&eacute;gats, l'adversaire a -10% de chance d'anticiper vos actions pendant 3 tours.</p>
			
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>7</td>
			
			            <td>9</td>
			            <td>11</td>
			            <td>14</td>
			            <td>16</td>
			            <td>19</td>
			        </tr>
			
			        <tr>
			            <td>Incantation</td>
			            <td>150</td>
			            <td>220</td>
			            <td>270;</td>
			            <td>310</td>
			            <td>380</td>
			            <td>440</td>
			
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Globe de foudre</strong> - Magie &eacute;l&eacute;mentaire - 6 RM</h3>
			<p>Vous infligez +X d&eacute;gats, Soit vous supprimez un buff (15% de chances), soit le sort inflige +1 d&eacute;gat.</p>
			<p>
			
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			            <td>4</td>
			
			            <td>5</td>
			            <td>6</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>5</td>
			            <td>7</td>
			
			            <td>9</td>
			            <td>12</td>
			            <td>14</td>
			            <td>17</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			
			            <td>130</td>
			            <td>200</td>
			            <td>250</td>
			            <td>300</td>
			            <td>340</td>
			            <td>410</td>
			        </tr>
			    </tbody>
			</table>
			
			</p>
			<h3><strong>Lapidation</strong> - Magie &eacute;l&eacute;mentaire - 6 RM</h3>
			<p>Vous infligez +X d&eacute;gats, a une chance d'assomer.</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>5</td>
			            <td>7</td>
			            <td>9</td>
			            <td>12</td>
			
			            <td>14</td>
			            <td>17</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>110</td>
			            <td>180</td>
			
			            <td>230</td>
			            <td>280</td>
			            <td>320</td>
			            <td>390</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Fournaise</strong> - Magie &eacute;l&eacute;mentaire - 8 RM</h3>
			
			<p>Vous infligez +X d&eacute;gats</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>			
			            <td>19</td>
			            <td>22</td>
			            <td>26</td>
			
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>250</td>
			            <td>400</td>			
			            <td>500</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h2>Sorts hors combat</h2>
			<p>10 nouveaux sorts</p>
			<h3><strong>Engloutissement </strong>- Magie &eacute;l&eacute;mentaire - X MP / Y PA</h3>
			
			<p>[Sort de zone] R&eacute;duit la dext&eacute;rit&eacute; de Z pendant 6 heures</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>100</td>
			            <td>110</td>
			
			            <td>120</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>50</td>
			            <td>60</td>
			            <td>70</td>
			
			        </tr>
			        <tr>
			            <td>Z</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			
			        <tr>
			            <td>Incantation</td>
			            <td>270</td>
			            <td>410</td>
			            <td>520</td>
			        </tr>
			    </tbody>
			
			</table>
			<h3><strong>D&eacute;luge</strong> - Magie &eacute;l&eacute;mentaire - X MP / Y PA</h3>
			<p>[Sort de zone] R&eacute;duit la volont&eacute; de Z pendant 6 heures</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			
			            <td>X</td>
			            <td>
			            <p>100</p>
			            </td>
			            <td>110</td>
			            <td>120</td>
			        </tr>
			
			        <tr>
			            <td>Y</td>
			            <td>50</td>
			            <td>60</td>
			            <td>70</td>
			        </tr>
			        <tr>
			
			            <td>Z</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			
			            <td>250</td>
			            <td>370</td>
			            <td>470</td>
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Blizard</strong> - Magie &eacute;l&eacute;mentaire - X MP / Y PA</h3>
			
			<p>[Sort de zone] Si la cible se d&eacute;place, elle perd Z% de ses points de vie</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>100</td>
			            <td>110</td>
			            <td>120</td>
			
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>50</td>
			            <td>60</td>
			            <td>70</td>
			        </tr>
			
			        <tr>
			            <td>Z</td>
			            <td>2</td>
			            <td>3</td>
			            <td>4</td>
			        </tr>
			        <tr>
			
			            <td>Incantation</td>
			            <td>310</td>
			            <td>450</td>
			            <td>570</td>
			        </tr>
			    </tbody>
			</table>
			
			<h3><strong>Orage magn&eacute;tique</strong> - Magie &eacute;l&eacute;mentaire - X MP / Y PA</h3>
			<p>[Sort de zone] Supprime Z% des MP max des joueurs sur la case (non r&eacute;p&eacute;table)</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			
			            <td>100</td>
			            <td>110</td>
			            <td>120</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>50</td>
			
			            <td>60</td>
			            <td>70</td>
			        </tr>
			        <tr>
			            <td>Z</td>
			            <td>8</td>
			            <td>12</td>
			
			            <td>16</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>220</td>
			            <td>330</td>
			            <td>420</td>
			
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Bouclier de terre</strong> - Magie &eacute;l&eacute;mentaire - X MP 4 PA</h3>
			<p>Augmente l'absorption avec un bouclier de Y</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			
			            <td>X</td>
			            <td>30</td>
			            <td>35</td>
			            <td>50</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>incantation</td>
			            <td>100</td>
			
			            <td>300</td>
			            <td>400</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Bouclier d'eau</strong> - Magie &eacute;l&eacute;mentaire - X MP 4 PA</h3>
			
			<p>Lors d'un blocage, on a Y% de chance de glacer l'adversaire pendant Z round</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>40</td>
			            <td>47</td>
			            <td>55</td>
			        </tr>
			
			        <tr>
			            <td>Y</td>
			            <td>15</td>
			            <td>25</td>
			            <td>25</td>
			        </tr>
			        <tr>
			
			            <td>Z</td>
			            <td>1</td>
			            <td>1</td>
			            <td>2</td>
			        </tr>
			        <tr>
			            <td>incantation</td>
			
			            <td>80</td>
			            <td>260</td>
			            <td>350</td>
			        </tr>
			    </tbody>
			</table>
			</p>
			<h3><strong>Bouclier de feu</strong> - Magie &eacute;l&eacute;mentaire - X MP 4 PA</h3>
			
			<p>Lors d'un blocage, inflige Y d&eacute;gats</p>
			<p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			
			            <td>3</td>
			        </tr>
			        <tr>
			            <td>X</td>
			            <td>30</td>
			            <td>35</td>
			            <td>50</td>
			
			        </tr>
			        <tr>
			            <td>Y</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			
			        <tr>
			            <td>incantation</td>
			            <td>130</td>
			            <td>350</td>
			            <td>500</td>
			        </tr>
			    </tbody>
			
			</table>
			</p>
			<h3><strong>Repos du sage</strong> - Magie de la vie - 0 MP / 40 PA</h3>
			<p>Vous redonne X MP, mais vous ne pouvez pas attaquer pendant 10 heures</p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			
			            <td>2</td>
			            <td>3</td>
			            <td>4</td>
			            <td>5</td>
			            <td>6</td>
			        </tr>
			
			        <tr>
			            <td>X</td>
			            <td>30</td>
			            <td>34</td>
			            <td>38</td>
			            <td>42</td>
			
			            <td>46</td>
			            <td>50</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>130</td>
			            <td>240</td>
			
			            <td>360</td>
			            <td>460</td>
			            <td>540</td>
			            <td>600</td>
			        </tr>
			    </tbody>
			</table>
			
			<h3><strong>Lente agonie</strong> - N&eacute;cromancie - X MP / 10 PA</h3>
			<p>Inverse la r&eacute;g&eacute;n&eacute;ration naturelle. Le joueur perd des HP au lieu d'en gagner. (Y HP perdu pour 1 HP gagn&eacute; th&eacute;oriquement). Dur&eacute;e 3 jours.</p>
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			        <tr>
			
			            <td>X</td>
			            <td>70</td>
			            <td>75</td>
			            <td>80</td>
			        </tr>
			        <tr>
			            <td>Y</td>
			
			            <td>0.5</td>
			            <td>0.7</td>
			            <td>1</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			            <td>250</td>
			
			            <td>500</td>
			            <td>650</td>
			        </tr>
			    </tbody>
			</table>
			<h3><strong>Souffrance extenuante</strong> - N&eacute;cromancie - X MP / 4 PA</h3>
			<p>Mulitiplie par Y la dur&eacute;e des d&eacute;buffs que vous lancez.</p>
			
			<table cellspacing="1" border="1" cellpadding="1" width="200">
			    <tbody>
			        <tr>
			            <td>&nbsp;</td>
			            <td>1</td>
			            <td>2</td>
			            <td>3</td>
			        </tr>
			
			        <tr>
			            <td>X</td>
			            <td>40</td>
			            <td>45</td>
			            <td>50</td>
			        </tr>
			        <tr>
			
			            <td>Y</td>
			            <td>2</td>
			            <td>3</td>
			            <td>4</td>
			        </tr>
			        <tr>
			            <td>Incantation</td>
			
			            <td>120</td>
			            <td>240</td>
			            <td>340</td>
			        </tr>
			    </tbody>
			</table>
			<p>&nbsp;</p>
			<h2>Total</h2>
			
			<p>19 Nouvelles comp&eacute;tences et 21 nouveaux sorts</p>
		</div>
<?php
	include('menu_d.php');
?>
</div>
<?php
	include('bas.php');
}
?>