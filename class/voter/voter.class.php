<?php
/**
 * Classe abstraite pour vérifier les permissions sur un objet quelconque
 */
abstract class voter
{
	abstract public function supports($attribute, $subject);
	abstract public function voteOnAttribute($attribute, $subject);
}