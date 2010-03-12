<?php
mysql_connect('localhost', 'starshine', 'YjyaELN4cUYtGD7b');
$base = 'starshine_raz';
$table = mysql_list_tables($base);
//on prépare la requête
$sql = "OPTIMIZE TABLE ";
//on recherche toutes les données des tables
$req = mysql_query('SHOW TABLE STATUS');
while($data = mysql_fetch_assoc($req))
{
    //on regarde seulement les tables qui affichent des pertes
    if($data['Data_free'] > 0)
    {
        //et on l'inclut si elle comporte des pertes
        $sql .= '`'.$data['Name'].'`, ';
    }
}
//on enlève le ', ' de trop
$sql = substr($sql, 0, (strlen($sql)-2));
//et on optimise
mysql_query($sql);
mysql_close();
?>