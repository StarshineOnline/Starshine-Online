<?php
/**
 * Classe permettant de vérifier les permissions sur les objets "messagerie_thread"
 */
class voter_thread extends voter
{
	// Les $attribute viables pour ce "voter"
	const SUPPR = 'suppr';
	
	public function supports($attribute, $subject)
	{
		// if the attribute isn't one we support, return false
		if (!in_array($attribute, array(self::SUPPR))) {
			return false;
		}
		
		// only vote on "messagerie_thread" objects inside this voter
		if (!$subject instanceof messagerie_thread) {
			return false;
		}
		
		return true;
	}
	
	public function voteOnAttribute($attribute, $subject)
	{
		$perso = joueur::get_perso();
		
		// the user must be logged in; if not, deny access
		if (!$perso instanceof perso) {
			return false;
		}
		
		// $subject is a "messagerie_thread" object
		$thread = $subject;
		
		switch ($attribute) {
			case self::SUPPR:
				return $this->canDelete($thread, $perso);
				break;
		}
		
		throw new \LogicException('This code should not be reached!');
	}
	
	private function canDelete(messagerie_thread $thread, perso $perso)
	{
		$messagerie = new messagerie($perso);
		$nbr_msg = $thread->get_message_total($perso->get_id(), $messagerie->get_condition());
		
		return ( $thread->get_categorie() == 'groupe' && $perso->get_groupe()->get_id_leader() == $perso->get_id() ) || ( $thread->get_id_auteur() == $perso->get_id() && $nbr_msg <= 1 );
	}
}
