<?php
/**
 * Classe permettant de vérifier les permissions sur les objets "messagerie_message"
 */
class voter_message extends voter
{
	// Les $attribute viables pour ce "voter"
	const SUPPR = 'suppr';
	
	public function supports($attribute, $subject)
	{
		// if the attribute isn't one we support, return false
		if (!in_array($attribute, array(self::SUPPR))) {
			return false;
		}
		
		// only vote on "messagerie_message" objects inside this voter
		if (!$subject instanceof messagerie_message) {
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
		
		// $subject is a "messagerie_message" object
		$message = $subject;
		
		switch ($attribute) {
			case self::SUPPR:
				return $this->canDelete($message, $perso);
				break;
		}
		
		throw new \LogicException('This code should not be reached!');
	}
	
	private function canDelete(messagerie_message $message, perso $perso)
	{
		return ( $message->get_id_auteur() == $perso->get_id() );
	}
}
