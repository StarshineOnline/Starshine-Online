<?php
include('inc/fp.php');

$requete = "SELECT * FROM journal WHERE time < '2007-10-01' limit 0,100000";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$id_journal = $row['id'];
	$requete2 = 'DELETE FROM journal WHERE id = "'.$id_journal.'"'; 	
	$req2 = $db->query($requete2);	
	echo 'Suppression reussi de '.$id_journal.'<br />';	
}

?>

