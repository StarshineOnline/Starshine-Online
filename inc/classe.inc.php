<?php
if (file_exists('../root.php'))
  include_once('../root.php');

$Tmaxcomp['melee'] = 100;
$Tmaxcomp['esquive'] = 100;
$Tmaxcomp['incantation'] = 100;
$Tmaxcomp['sort_vie'] = 100;
$Tmaxcomp['sort_mort'] = 100;
$Tmaxcomp['sort_element'] = 100;
$Tmaxcomp['distance'] = 100;
$Tmaxcomp['blocage'] = 100;
$Tmaxcomp['maitrise_critique'] = 100;
$Tmaxcomp['maitrise_arc'] = 100;
$Tmaxcomp['maitrise_epee'] = 100;
$Tmaxcomp['maitrise_dague'] = 100;
$Tmaxcomp['maitrise_hache'] = 100;
$Tmaxcomp['identification'] = 500;
$Tmaxcomp['forge'] = 500;
$Tmaxcomp['alchimie'] = 500;
$Tmaxcomp['architecture'] = 500;
$Tmaxcomp['craft'] = 500;
$Tmaxcomp['survie'] = 500;
$Tmaxcomp['dressage'] = 100;

## Combattants
# Guerrier
$Tclasse['combattant']['type'] = 'guerrier';		#1
$Tclasse['guerrier']['type'] = 'guerrier';			#2
$Tclasse['champion']['type'] = 'champion';			#3
$Tclasse['titan']['type'] = 'champion';					#4

# Paladin
$Tclasse['paladin']['type'] = 'champion';				#3
$Tclasse['templier']['type'] = 'champion';			#4

# Voleur
$Tclasse['voleur']['type'] = 'voleur';					#2
$Tclasse['assassin']['type'] = 'voleur';				#3
$Tclasse['ombre']['type'] = 'voleur';						#4

## Danseur élémentaire
$Tclasse['danseur élémentaire']['type'] = 'voleur';#3
$Tclasse['derviche']['type'] = 'voleur';				#4

# Archer
$Tclasse['archer']['type'] = 'archer';					#2
$Tclasse['archer d élite']['type'] = 'archer';	#3
$Tclasse['archer d\'élite']['type'] = 'archer';	#3
$Tclasse['sniper']['type'] = 'archer';					#4

# Archer noir
$Tclasse['archer noir']['type'] = 'archer';			#3
$Tclasse['prédateur']['type'] = 'archer';				#4

# Rodeur
$Tclasse['rodeur']['type'] = 'rodeur';					#2
$Tclasse['pisteur']['type'] = 'rodeur';					#3
$Tclasse['dresseur']['type'] = 'rodeur';				#4

## Mages
# Sorcier
$Tclasse['magicien']['type'] = 'mage';					#1
$Tclasse['sorcier']['type'] = 'mage';						#2
$Tclasse['grand sorcier']['type'] = 'archimage';#3
$Tclasse['elémentaliste']['type'] = 'archimage';#4

# Clerc
$Tclasse['clerc']['type'] = 'mage';							#2
$Tclasse['prètre']['type'] = 'archimage';				#3
$Tclasse['prêtre']['type'] = 'archimage';				#3 // accent circonflexe
$Tclasse['sage']['type'] = 'archimage';					#4

# Necro
$Tclasse['nécromancien']['type'] = 'mage';			#2
$Tclasse['grand nécromancien']['type'] = 'archimage';#3
$Tclasse['pestimancien']['type'] = 'archimage';	#4

# Arcaniste
$Tclasse['arcaniste']['type'] = 'druide2';		#3
$Tclasse['démoniste']['type'] = 'druide2';		#4

# Druide
$Tclasse['druide oblaire']['type'] = 'druide';	#2
$Tclasse['druide anruth']['type'] = 'druide2';	#3
$Tclasse['druide ollamh']['type'] = 'druide2';	#4

# Dresseur de l´ombre
$Tclasse['dresseur de lombre']['type'] = 'druide';#2
$Tclasse['dresseur de l\'ombre']['type'] = 'druide';#2

# Invocateur
$Tclasse['invocateur']['type'] = 'druide';			#2
$Tclasse['grand invocateur']['type'] = 'druide2';#3
$Tclasse['conjurateur']['type'] = 'druide2';		#4


?>