<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><h1>Gestion des batailles</h1>
<a href="gestion_bataille.php" onclick="return envoiInfo(this.href, 'conteneur');">Index batailles</a> | <a href="gestion_bataille.php?new" onclick="return envoiInfo(this.href, 'conteneur');">Nouvelle bataille</a>