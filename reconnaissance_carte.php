<?php
$seuil = 500;
$nb = 0;

//on va chercher l'image
$fichier = 'image/carteajouer.jpg';
$image = imagecreatefromjpeg($fichier);
$taille = getimagesize($fichier);
$x_max = $taille[0];
$y_max = $taille[1];

//On créé une image vierge
$image_nb = imagecreate($x_max, $y_max);
$blanc = imagecolorallocate($image_nb, 255, 255, 255);
$noir = imagecolorallocate($image_nb, 0, 0, 0);

//On parcours l'image
for($x = 0; $x < $x_max; $x++)
{
	for($y = 0; $y < $y_max; $y++)
	{
		$rgb = imagecolorat($image, $x, $y);
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		//On défini les blancs
		if(($r + $g + $b) > $seuil)
		{
			$couleur = $blanc;
			$bit = false;
		}
		else
		{
			$couleur = $noir;
			$bit = true;
		}
		$tableau[$x][$y] = $bit;
		//echo $x.' - '.$y.' : '.$r.' '.$g.' '.$b.' => '.$couleur.' <br />';
		imagefilledrectangle($image_nb, $x, $y, $x, $y, $couleur);
	}
}

imagepng($image_nb, 'image/carte/nb.png');
?>
<img src="image/carte/nb.png" />
<?php
$nb++;

function detecte($x, $y)
{
	global $tableau, $forme, $nb_forme, $x_tab_forme, $y_tab_forme, $next_x, $next_y, $first_check;
	$first_check = true;
	for($yt = -1;$yt < 2;$yt++)
	{
		for($xt = -1;$xt < 2;$xt++)
		{
			$x_forme = $x + $xt;
			$y_forme = $y + $yt;
			$x_tableau = $x_tab_forme + $x + $xt;
			$y_tableau = $y_tab_forme + $y + $yt;
			//echo 'xt : '.$xt.' - yt : '.$y.' - x_forme : '.$x_forme.' - y_forme : '.$y_forme.' - x_tableau : '.$x_tableau.' - y_tableau : '.$y_tableau.'<br />';
			if($tableau[$x_tableau][$y_tableau] AND !$forme[$nb_forme][$x_forme][$y_forme]['couleur'])
			{
				$forme[$nb_forme][$x_forme][$y_forme]['couleur'] = true;
				$forme[$nb_forme][$x_forme][$y_forme]['check'] = false;
				$forme[$nb_forme][$x_forme][$y_forme]['xy'] = $x_tableau.' - '.$y_tableau;
				$tableau[$x_tableau][$y_tableau] = false;
				if(($xt != 0 OR $yt != 0) AND $first_check)
				{
					$next_x = $x_forme;
					$next_y = $y_forme;
					$first_check = false;
				}
			}
		}		
	}
	$forme[$nb_forme][$x][$y]['check'] = true;
}

function create_image_forme($forme, $numero)
{
	$keys_x = array_keys($forme);
	$x_max = max($keys_x) + 1;
	$y_max = 0;
	foreach($forme as $key)
	{
		$keys_y = array_keys($key);
		if(max($keys_y) + 1 > $max) $y_max = max($keys_y) + 1;
	}
	if($y_max > 5)
	{
		echo 'Taille en px : '.$x_max.' '.$y_max.'<br />';
		//On créé une image vierge
		$image_nb = imagecreate($x_max, $y_max);
		$blanc = imagecolorallocate($image_nb, 255, 255, 255);
		$noir = imagecolorallocate($image_nb, 0, 0, 0);
		imagepng($image_nb, 'image/carte/forme'.$numero.'.png');
		for($i = 0; $i < $x_max; $i++)
		{
			for($j = 0; $j < $y_max; $j++)
			{
				if($forme[$i][$j]['check']) $couleur = $noir;
				else $couleur = $blanc;
				imagefilledrectangle($image_nb, $keys_x[$i], $keys_y[$j], $keys_x[$i], $keys_y[$j], $couleur);
			}
		}
		echo '<img src="image/carte/forme'.$numero.'.png" />';
	}
}

echo '<pre>';
//Recherche de formes
$x = 0;
$y = 0;
$nb_forme = 0;
//On parcours jusqu'à la fin
while($y < $y_max)
{
	$x = 0;
	while($x < $x_max)
	{
		//On débute la formation
		if($tableau[$x][$y])
		{
			$x_tab_forme = $x;
			$y_tab_forme = $y;
			//test pour le premier pixel seul
			$forme[$nb_forme][0][0]['couleur'] = true;
			$forme[$nb_forme][0][0]['check'] = true;
			detecte(0, 0);
			while(!$first_check)
			{
				//echo $next_x.' '.$next_y.'<br />';
				detecte($next_x, $next_y);
			}
			if(count($forme[$nb_forme]) > 5)
			{
				echo 'FORME : '.$nb_forme.'<br />';
				create_image_forme($forme[$nb_forme], $nb_forme);
				echo '<br /><br />';
			}
			$nb_forme++;
		}
		$x++;
	}
	$y++;
}
?>