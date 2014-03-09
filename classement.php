<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
?>
<ul>
	<li><a href="classement_groupe.php" onclick="return envoiInfo(this.href, 'popup_content');">Classement des groupes</a></li>
	<li><a href="classement_royaume.php" onclick="return envoiInfo(this.href, 'popup_content');">Classement des Royaumes</a></li>
</ul>
<ul>
	<li><a href="javascript:adresse('honneur', '')">Honneur</a> | <a href="javascript:adresse('reputation', '')">Réputation</a> | <a href="javascript:adresse('exp', '')">Expérience</a> | <a href="javascript:adresse('star', '')">Stars</a> | <a href="javascript:adresse('frag', '')">PvP</a> | <a href="javascript:adresse('mort', '')">Suicide</a> | <a href="javascript:adresse('crime', '')">Crime</a> | <a href="javascript:adresse('craft', '')">Artisanat</a> | <a href="javascript:adresse('achiev', '')">Achievements</a></li>
	<li><a href="javascript:adresse('melee', '')">Mélée</a> | <a href="javascript:adresse('esquive', '')">Esquive</a> | <a href="javascript:adresse('blocage', '')">Blocage</a> | <a href="javascript:adresse('distance', '')">Distance</a> | <a href="javascript:adresse('incantation', '')">Incantation</a> <span class="xsmall">( <a href="javascript:adresse('sort_element', '')">Magie élémentaire</a> | <a href="javascript:adresse('sort_mort', '')">Nécromancie</a> | <a href="javascript:adresse('sort_vie', '')">Magie de la vie</a> )</span> | <a href="javascript:adresse('survie', '')">Survie</a> | <a href="javascript:adresse('dressage', '')">Dressage</a></li>
</ul>
<ul>
	<li><a href="javascript:adresse('', 'tous')">Tous</a><?php if(array_key_exists('ID', $_SESSION)): ?> | <a href="javascript:adresse('', 'race')">Votre race</a> | <a id="classement_table_mon_perso">Moi</a><?php endif ?></li>
</ul>
<?php
include_once(root.'classement_ajax.php');
?>
<script type="text/javascript">
$('#classement_table_mon_perso').click(function(event){
	// The default action of the event will not be triggered.
	event.preventDefault();
	
	var isDisabledPagingAction = $(this).data('isDisabledPagingAction');
	if( !isDisabledPagingAction )
	{
		// On récupère la pagingAction à effectuer
		var pagingAction = $(this).data('pagingAction');
		possiblePagingActions = ["first", "previous", "next", "last"];
		// pagingAction peut aussi être un integer, le numéro de la page à laquelle on veut se rendre
		if( possiblePagingActions.indexOf(pagingAction) == -1 ){
			pagingAction = parseInt( pagingAction );
		}
		// The jQuery DataTables table takes the pagingAction 
		oTable.fnPageChange( pagingAction );
	}
});
</script>