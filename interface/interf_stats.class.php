<?php
/**
 * @file interf_stats.class.php
 * Affichage des statistiques
 
/**
 * classe gérant l'affichage des statistiques
 */
class interf_stats extends interf_onglets
{
	function __construct($stat)
	{
		global $G_url;
		parent::__construct('ongl_stats', 'statistiques');
		$G_url->add('ajax', 2);
		$this->add_onglet('Niveaux', $G_url->get('stat', 'stat_lvl'), 'ongl_stat_lvl', 'invent', $stat=='stat_lvl');
		$this->add_onglet('Niveau moyen', $G_url->get('stat', 'stat_niveau_moyen'), 'ongl_stat_niveau_moyen', 'invent', $stat=='stat_niveau_moyen');
		$this->add_onglet('Joueurs', $G_url->get('stat', 'stat_joueur'), 'ongl_stat_joueur', 'invent', $stat=='stat_joueur');
		$this->add_onglet('Rang 1', $G_url->get('stat', 'stat_classe1'), 'ongl_stat_classe1', 'invent', $stat=='stat_classe1', 'Rang');
		$this->add_onglet('Rang 2', $G_url->get('stat', 'stat_classe2'), 'ongl_stat_classe2', 'invent', $stat=='stat_classe2', 'Rang');
		$this->add_onglet('Rang 3', $G_url->get('stat', 'stat_classe3'), 'ongl_stat_classe3', 'invent', $stat=='stat_classe3', 'Rang');
		$this->add_onglet('Rang 4', $G_url->get('stat', 'stat_classe4'), 'ongl_stat_classe4', 'invent', $stat=='stat_classe4', 'Rang');
		$this->add_onglet('Races', $G_url->get('stat', 'stat_race'), 'ongl_stat_race', 'invent', $stat=='stat_race');
		$this->add_onglet('Stars 1', $G_url->get('stat', 'stat_star1'), 'ongl_stat_star1', 'invent', $stat=='stat_star1', 'Stars');
		$this->add_onglet('Stars 2', $G_url->get('stat', 'stat_star2'), 'ongl_stat_star2', 'invent', $stat=='stat_star2', 'Stars');
		$this->add_onglet('Stars 3', $G_url->get('stat', 'stat_star3'), 'ongl_stat_star3', 'invent', $stat=='stat_star3', 'Stars');
		$this->add_onglet('Monstres', $G_url->get('stat', 'stat_monstre'), 'ongl_', 'invent', $stat=='stat_monstre');
		
		$this->get_onglet('ongl_'.$stat)->add( new interf_img('image/'.$stat.'.png') );
		/// @todo remettre l'historique
	}
}

?>