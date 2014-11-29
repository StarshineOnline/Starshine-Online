<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');


$interf_princ = $G_interf->creer_jeu();

$categorie = array_key_exists('categorie', $_GET) ? $_GET['categorie'] : 'votes';
$action = array_key_exists('action', $_GET) ? $_GET['action'] : null;

switch($action)
{
case 'ipn':
  $parsed_url = parse_url($paypal_url);
  $vars = array();;
  $vars_post = '';
  $mail = "Réception d'un paiment par paypal:\n";
  foreach($_POST as $var=>$val)
  {
    $vars_post .= $var.'='.$val.'&';
    $vars[$var] = $val;
  }
  $mail .= 'Variables : '.$vars_post."\n";
  $vars_post .='cmd=_notify-validate';
  $fp = fsockopen($parsed_url['host'],'80',$err_num,$err_str,30);
  if( $fp )
  {
    fputs($fp, "POST $parsed_url[path] HTTP/1.1\r\n");
    fputs($fp, "Host: $parsed_url[host]\r\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
    fputs($fp, "Content-length: ".strlen($vars_post)."\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    fputs($fp, $vars_post . "\r\n\r\n");
    $rep = '';
    while(!feof($fp))
    {
      $rep .= fgets($fp, 1024);
    }
    fclose($fp);
    if( stripos($rep, 'VERIFIED') )
    {
      $requete = 'SELECT valeur FROM variable WHERE nom LIKE "don_paypal"';
      $req = $db->query($requete);
      $row = $db->read_assoc($req);
      $dons = $row['valeur'] + $vars['mc_gross'] - $vars['mc_fee'];
      $requete = 'UPDATE variable SET valeur = "'.$dons.'"" WHERE nom LIKE "don_paypal"';
      $db->query($requete);
      $mail .= 'Paiment ok.';
    }
    else
      $mail .= 'Paiment non vérifié.';
  }
  else
  {
    $mail .= 'Erreur fsockopen #'.$err_num.' : '.$err_str."\n";
  }
  mail('elettar@starshine-online.com', 'SSO - don Paypal', $mail);
  exit;
  
case 'valide':
  // Identifiants de votre document
  $docId      = 118738;
  $siteId      = 399811;

  // PHP5 avec register_long_arrays désactivé?
  if (!isset($HTTP_GET_VARS)) {
      //$HTTP_SESSION_VARS    = $_SESSION;
      $HTTP_SERVER_VARS     = $_SERVER;
      $HTTP_GET_VARS        = $_GET;
  }

  // Construction de la requête pour vérifier le code

  $query      = 'http://payment.rentabiliweb.com/checkcode.php?';
  $query     .= 'docId='.$docId;
  $query     .= '&siteId='.$siteId;
  $query     .= '&code='.$HTTP_GET_VARS['code'];
  $query     .= "&REMOTE_ADDR=".$HTTP_SERVER_VARS['REMOTE_ADDR'];
  $result     = @file($query);

  if(trim($result[0]) === "OK")
  {
    echo 'Merci pour le don ! <br />';
    $requete = 'SELECT valeur FROM variable WHERE nom LIKE "don_sms"';
    $req = $db->query($requete);
    $row = $db->read_assoc($req);
    $dons = $row['valeur'] + .2;
    $requete = 'UPDATE variable SET valeur = "'.$dons.'" WHERE nom LIKE "don_sms"';
    $db->query($requete);
  }
  else
  {
    echo 'Le don n\'a pu être vérifié !<br />';
  }

  // Accès à votre page protégée
  
  break;

case 'erreur':
	log_admin::log('bug', 'erreur lors d\'un don.');
}

if( array_key_exists('ajax', $_GET) && $_GET['ajax'] == 2 )
{
	$G_url->add('categorie', $categorie);
	switch($categorie)
	{
	case 'votes':
		$interf_princ->add( $G_interf->creer_votes() );
		break;
	case 'dons':
		$interf_princ->add( $G_interf->creer_dons() );
		break;
	}
}
else
{
	$dlg = $interf_princ->set_dialogue( new interf_dialogBS('Votes & dons', true, 'dlg_votes_dons') );
	$dlg->add( $G_interf->creer_votes_dons($categorie) );
}

?>