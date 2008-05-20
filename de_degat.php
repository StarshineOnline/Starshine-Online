<?php
function de_degat($force, $degat_arme)
{
	$tab_de = array();
	$tab_de[0][0] = 2;
	$tab_de[0][1] = 3;
	$tab_de[0][2] = 4;
	$tab_de[0][3] = 5;
	$tab_de[0][4] = 6;
	$tab_de[0][5] = 7;
	$tab_de[0][6] = 11;
	$tab_de[1][0] = 2;
	$tab_de[1][1] = 4;
	$tab_de[1][2] = 6;
	$tab_de[1][3] = 8;
	$tab_de[1][4] = 10;
	$tab_de[1][5] = 12;
	$tab_de[1][6] = 20;
	$potentiel = ceil($force / 3) + $degat_arme;
	$de_degat = array();
	while($potentiel > 1)
	{		
		if (($potentiel > 7) AND ($potentiel < 15))
		{
			$des = array_search(ceil($potentiel / 2), $tab_de[0]);
			$potentiel = $potentiel - $tab_de[0][$des];
			$de[] = $tab_de[1][$des];
		}
		else
		{
			$z = 6;
			$check = true;
			while($z >= 0 && $check)
			{
				if ($potentiel >= $tab_de[0][$z])
				{
					$potentiel = $potentiel - $tab_de[0][$z];
					$de[] = $tab_de[1][$z];
					$check = false;
				}
				$z--;
			}
		}
	}
	return $de;
}

function de_soin($force, $degat_arme)
{
	$tab_de = array();
	$tab_de[0][0] = 1;
	$tab_de[0][1] = 2;
	$tab_de[0][2] = 3;
	$tab_de[0][3] = 4;
	$tab_de[0][4] = 5;
	$tab_de[1][0] = 1;
	$tab_de[1][1] = 2;
	$tab_de[1][2] = 4;
	$tab_de[1][3] = 6;
	$tab_de[1][4] = 8;
	$potentiel = ceil($force / 3) + $degat_arme;
	$de_degat = array();
	while($potentiel > 0)
	{		
		if (($potentiel > 7) AND ($potentiel < 15))
		{
			$des = array_search(ceil($potentiel / 2), $tab_de[0]);
			$potentiel = $potentiel - $tab_de[0][$des];
			$de[] = $tab_de[1][$des];
		}
		else
		{
			$z = 4;
			$check = true;
			while($z >= 0 && $check)
			{
				if ($potentiel >= $tab_de[0][$z])
				{
					$potentiel = $potentiel - $tab_de[0][$z];
					$de[] = $tab_de[1][$z];
					$check = false;
				}
				$z--;
			}
		}
	}
	return $de;
}

for($g = 1; $g < 100; $g++)
{
	$de_degat_sort = de_soin(0, $g);
	$des = '';
	$de_degat_sort2 = array();
	$i = 0;
	while($i < count($de_degat_sort))
	{
		$de_degat_sort2[$de_degat_sort[$i]] += 1;
		$i++;
	}
	$i = 0;
	$keys = array_keys($de_degat_sort2);
	while($i < count($de_degat_sort2))
	{
		if ($i > 0) $des .= ' + ';
		$des .= $de_degat_sort2[$keys[$i]].'D'.$keys[$i];
		$i++;
	}
	echo $g.' - '.$des.'<br />';
}

?>