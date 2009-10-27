<?php

define('BASE', '/srv/starshine-data/starshine/PidginPortable/');

if (file_exists('root.php'))
		 include_once('root.php');
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

function replace_accents($string)
{
  return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
}

function replace_all($string)
{
  $string = str_replace(' ', '_', $string);
  return replace_accents($string);
  //return strtolower(replace_accents($string));
}

$login = replace_all($_SESSION['nom']);

$temp_file = tempnam(sys_get_temp_dir(), 'pidgin');
$temp_dir = $temp_file.'.dir';
$work_dir = $temp_dir.'/pidgin/';
$zip_file = 'pidgin.zip';
mkdir($work_dir.'Data/settings/.purple', 0777, true);

$targets = array('App', 'PidginPortable.exe', 'Other',
								 'Data/settings/PidginPortableSettings.ini',
								 'Data/settings/.purple/blist.xml',
								 'Data/settings/.purple/prefs.xml',
								 'Data/settings/.purple/status.xml',
								 'Data/settings/.purple/xmpp-caps.xml',
								 'Data/settings/.purple/certificates',
								 'Data/settings/.purple/smileys');
foreach ($targets as $target) {
	symlink(BASE.$target, $work_dir.$target);
}

$transforms = array('Data/settings/.purple/accounts.xml', 
										'Data/settings/.purple/blist.xml');
foreach ($transforms as $transform) {
	system("sed s/__LOGIN__/$login/ > ${work_dir}${transform} < ".BASE.$transform);
}
chdir($temp_dir);
system("zip -X -q -r $zip_file pidgin");

header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename='.basename($zip_file));
header('Content-Transfer-Encoding: binary');
header('Pragma: public');
header('Content-Length: ' . filesize($zip_file));

readfile("$temp_dir/$zip_file");

chdir('/tmp');
system("rm -Rf $temp_dir");
unlink($temp_file);

?>