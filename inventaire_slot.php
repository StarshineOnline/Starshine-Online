<?php // -*- tab-width:	 2 -*-
if (file_exists('root.php'))
  include_once('root.php');

	if (isset($_GET['javascript']))
	{
		include_once(root.'inc/fp.php');
		$joueur = new perso($_SESSION['ID']);
		$W_requete = 'SELECT * FROM map WHERE x ='.$joueur->get_x()
			.' and y = '.$joueur->get_y();
		$W_req = $db->query($W_requete);
		$W_row = $db->read_array($W_req);
		$R = get_royaume_info($joueur->get_race(), $W_row['royaume']);
	}
//Filtre
if(array_key_exists('filtre', $_GET)) $filtre_url = '&amp;filtre='.$_GET['filtre'];
else $filtre_url = '';
$i = 0;
//echo '<ul>';
if($joueur->get_inventaire_slot() != '')
{
$arme_de_siege = 0;
	$joueur->restack_objet();
	foreach($joueur->get_inventaire_slot_partie() as $invent)
	{
		$image='image/interface/inventaire/Unknown.png';
		if($invent !== 0 AND $invent != '')
		{
			$objet_d = decompose_objet($invent);
			//echo '<!-- '; var_dump($objet_d); echo '-->';
			if($objet_d['identifier'])
			{
				switch ($objet_d['categorie'])
				{
					//Si c'est une arme
					case 'a' :
						$requete = "SELECT * FROM arme WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$image = 'image/arme/arme'.$row['id'].'.png';
						$mains = explode(';', $row['mains']);
						$partie = $mains[0];
					break;
					//Si c'est un objet de pet
					case 'd' :
						$requete = "SELECT * FROM objet_pet WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
					break;
					//Si c'est une protection
					case 'p' :
						$requete = "SELECT * FROM armure WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$image = 'image/armure/'.$partie.'/'.$partie.$row['id'].'.png';
					break;
					case 'o' :
						$requete = "SELECT * FROM objet WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
					break;
					case 'g' :
						$requete = "SELECT * FROM gemme WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$row['prix'] = pow(10, $row['niveau']) * 10;
					break;
					case 'r' :
						$requete = "SELECT * FROM objet_royaume WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$row['utilisable'] = 'y';
						
						if($row['type'] == "arme_de_siege")
							$arme_de_siege++;
					break;
					case 'm' :
						$requete = "SELECT * FROM accessoire WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = 'accessoire';
						$image = 'image/accessoire/accessoire'.$row['id'].'.png';
						$row['utilisable'] = 'n';
					break;
					case 'l' :
						$requete = "SELECT * FROM grimoire WHERE ID = ".$objet_d['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
						$row['utilisable'] = 'y';
					break;
				}
			}
			else
			{
				$row['nom'] = 'Objet non-identifié';
			}
			//Filtrage
			if(array_key_exists('filtre', $_GET)) $filtre = $_GET['filtre']; else $filtre = 'utile';
			$check = false;
			$liste_categorie = array('o', 'a', 'p', 'l');
			if((($objet_d['categorie'] == 'o' AND $filtre == 'utile')
					OR ($objet_d['categorie'] == 'l' AND $filtre == 'utile')
					OR ($objet_d['categorie'] == 'a' AND $filtre == 'arme')
					OR ($objet_d['categorie'] == 'p' AND $filtre == 'armure'))
				 AND $objet_d['identifier'])
			{
				$check = true;
			}
			elseif(!in_array($objet_d['categorie'], $liste_categorie) AND $filtre == 'autre') $check = true;
			if($check)
			{
			  if ($objet_d['identifier'])
				  	$echo = description_objet($invent);
			  else
					$echo = 'Objet non indentifié';
			?>
			<script>
			
			
			function dragndrop(source, cible) {
				
				// on rend les objets de l'inventaire draggable
				$( source ).draggable({ helper: "original", tolerance: "touch", revert: "invalid" });
				// les drop zones n'acceptent que les drag du meme type
				$( cible ).droppable({
										accept: source,
										activeClass: "ui-state-hover",
										hoverClass: "ui-state-active",
										drop: function( event, ui ) {
																		$( this ).addClass( "ui-state-highlight" );
																		// il peut y avoir plusieurs objets pour la meme cible
																		$( source ).each(function(index, objet){
																													var classe=$(this).attr("class");
																													var testclass=source.replace(".","")+" ui-draggable ui-draggable-dragging"
																													//alert(classe+" VS "+testclass);
																													if(classe==testclass)
																													{
																														//alert($(this).attr("id"));
																														var target = $(this).find(".equiper").attr("value");
																														//alert(target);
																														var cibleajax="inventaire.php";
																														if("<? echo substr($partie, -4); ?>"=="_pet")
																														{
																															cibleajax="inventaire_pet.php";
																														}
																														// on envoie une requete ajax pour faire equiper l'objet
																														$.ajax({
																															url : cibleajax,
																															type : "GET",
																															data : target,
																															// en cas de reussite, forcer le refresh
																															success : function(reponse) {
																																//alert(reponse)
																																$("#information").empty();
																																//$("#information").append(reponse);
																																$("#information").load(cibleajax);
																																$(".overlib").hide();
																															}
																														});
																													}
																												});
							
										}
				});
				$( "#hdv" ).droppable({
										accept: source,
										activeClass: "ui-state-hover",
										hoverClass: "ui-state-highlight",
										drop: function( event, ui ) {
																		$( this ).addClass( "ui-state-highlight" );
																		// il peut y avoir plusieurs objets pour la meme cible
																		$( source ).each(function(index, objet){
																													var classe=$(this).attr("class");
																													var testclass=source.replace(".","")+" ui-draggable ui-draggable-dragging"
																													//alert(classe+" VS "+testclass);
																													if(classe==testclass)
																													{
																														//alert($(this).attr("id"));
																														var target = $(this).children(".hdv").attr("value");
																														//alert(target);
																														// on envoie une requete ajax pour mettre l'objet a l'HDV
																														$.ajax({
																															url : "inventaire.php",
																															type : "GET",
																															data : target,
																															// en cas de reussite, forcer le refresh
																															success : function(reponse) {
																																//alert(reponse)
																																$("#information").empty();
																																$("#information").append(reponse);
																																//$("#information").load("inventaire.php");
																																$(".overlib").hide();
																															}
																														});
																													}
																												});
																	}																								
									});
				$( "#marchand" ).droppable({
												accept: source,
												activeClass: "ui-state-hover",
												hoverClass: "ui-state-highlight",
												drop: function( event, ui ) {
																				$( this ).addClass( "ui-state-highlight" );
																				// il peut y avoir plusieurs objets pour la meme cible
																				$( source ).each(function(index, objet){
																															var classe=$(this).attr("class");
																															var testclass=source.replace(".","")+" ui-draggable ui-draggable-dragging"
																															//alert(classe+" VS "+testclass);
																															if(classe==testclass)
																															{
																																//alert($(this).attr("id"));
																																var target = $(this).children(".marchand").attr("value");
																																//alert(target);
																																// on envoie une requete ajax pour vendre l'objet
																																$.ajax({
																																	url : "inventaire.php",
																																	type : "GET",
																																	data : target,
																																	// en cas de reussite, forcer le refresh
																																	success : function(reponse) {
																																		//alert(reponse)
																																		$("#information").empty();
																																		//$("#information").append(reponse);
																																		$("#information").load("inventaire.php");
																																		$(".overlib").hide();
																																	}
																																});
																															}
																														});
																			}
											});
			};
			dragndrop(".drag_<?php echo $partie; ?>", "#drop_<?php echo $partie; ?>");  
			</script>
			<p style='width:400px;'>
			<span name="overlib" onmouseover="return <?php echo make_overlib($echo); ?>" onmouseout="return nd();">
			<?php 
			echo "<span class='drag_".$partie."' id='".$objet_d['id_objet']."'><img src='".$image."' />"; 

			$modif_prix = 1;
			if($objet_d['stack'] > 1) echo ' X '.$objet_d['stack'];
			if($objet_d['slot'] > 0)
			{
				echo '<span class="xsmall">Slot niveau '.$objet_d['slot'].'</span>';
				$modif_prix = 1 + ($objet_d['slot'] / 5);
			}
			if($objet_d['slot'] == '0')
			{
				echo '<span class="xsmall">Slot impossible</span>';
				$modif_prix = 0.9;
			}
			if($objet_d['enchantement'] > '0')
			{
				$requete = "SELECT * FROM gemme WHERE id = ".$objet_d['enchantement'];
				$req = $db->query($requete);
				$row_e = $db->read_assoc($req);
				$modif_prix = 1 + ($row_e['niveau'] / 2);
				echo '<span class="xsmall">Enchantement de '.$row_e['enchantement_nom'].'</span>';
			}
			
			//else echo ' X 1';
			//print_r($objet_d);
			if($objet_d['identifier'])
			{
				if($objet_d['categorie'] == 'g')
				{
					//echo '<span class="inventaire_span" style="width:60px;"><a href="inventaire.php?action=enchasse&amp;key_slot='.$i.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Enchasser</a><span class="xsmall">(20 PA)</span></span>';
					echo '<span class="equiper" value="action=enchasse&amp;key_slot='.$i.$filtre_url.'" />';
				}
				elseif($objet_d['categorie'] == 'a' OR $objet_d['categorie'] == 'p' OR $objet_d['categorie'] == 'm')
				{
					echo '<span class="equiper" value="action=equip&amp;id_objet='.$objet_d['id_objet'].'&amp;partie='.$partie.'&amp;key_slot='.$i.'&amp;categorie='.$objet_d['categorie'].$filtre_url.'" />';
				}
				elseif($objet_d['categorie'] == 'd')
				{
					echo '<span class="equiper" value="action=equip&amp;id_objet='.$objet_d['id_objet'].'&amp;partie='.$partie.'&amp;key_slot='.$i.'&amp;categorie='.$objet_d['categorie'].$filtre_url.'" />';
				}
				elseif($objet_d['categorie'] == 'o' OR $objet_d['categorie'] == 'r')
				{
					if($row['utilisable'] == 'y') echo '<span class="inventaire_span" style="width:60px;"><a href="inventaire.php?action=utilise&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Utiliser</a></span>';
					if($W_row['type'] == 1 AND $objet_d['categorie'] == 'r') echo '<a href="inventaire.php?action=depot&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Déposer au dépot</a></span>';
					//echo '<span class="inventaire_span"><a href="inventaire.php?action=depot&amp;id_objet='.$objet_d['id_objet'].'&amp;type='.$row['type'].'&amp;key_slot='.$i.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Déposer au dépot</a></span>';
				}
				elseif($objet_d['categorie'] == 'l')
				{
					// echo '<span class="inventaire_span" style="width:50px;"><a href="inventaire.php?action=utilise&amp;id_objet='.$objet_d['id_objet'].'&amp;type=grimoire&amp;key_slot='.$i.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Lire</a></span>';
					echo '<a href="inventaire.php?action=utilise&amp;id_objet='.$objet_d['id_objet'].'&amp;type=grimoire&amp;key_slot='.$i.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Lire</a></span>';
				}
				if ($W_row['type'] == 1 AND $objet_d['categorie'] != 'r' AND $objet_d['categorie'] != 'h')
				{
					$prix = floor($row['prix'] * $modif_prix / $G_taux_vente);
					//echo '<span class="inventaire_span" style="width:100px;"><a href="inventaire.php?action=vente&amp;id_objet='.$objet_d['id'].'&amp;key_slot='.$i.$filtre_url.'" onclick="if(confirm(\'Voulez vous vendre cet objet ?\')) return envoiInfo(this.href, \'information\'); else return false;">Vendre '.$prix.' Stars</a> / <a href="inventaire.php?action=ventehotel&amp;id_objet='.$objet_d['categorie'].$objet_d['id_objet'].'&amp;key_slot='.$i.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Hotel des ventes</a></span>';
					echo '<span class="marchand" value="action=vente&amp;id_objet='.$objet_d['id'].'&amp;key_slot='.$i.$filtre_url.'" /> Vendre('.$prix.' stars)<span class="hdv" value="action=ventehotel&amp;id_objet='.$objet_d['categorie'].$objet_d['id_objet'].'&amp;key_slot='.$i.$filtre_url.'" />';
				}
				if(($objet_d['categorie'] == 'a' OR $objet_d['categorie'] == 'p' OR $objet_d['categorie'] == 'm') AND $objet_d['slot'] == '' AND $objet_d['enchantement'] == '')
				{
					//echo '<span class="inventaire_span" style="width:120px;"><a href="inventaire.php?action=slot&amp;key_slot='.$i.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Mettre un slot</a> <span class="xsmall">(10 PA)</span></span>';
					echo ' / <a href="inventaire.php?action=slot&amp;key_slot='.$i.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Mettre un slot</a> <span class="xsmall">(10 PA)</span></span>';
				}
			}
			echo "</span>";
			}
			$i++;
		}
	}
}
//echo "</ul>";
?>
