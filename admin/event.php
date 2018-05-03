<?php
/**
 * @file event.php
 * Interface d'adiministration des events
 */

if (file_exists('../root.php'))
  include_once('../root.php');

// affichage d'un morceau de l'interface ou de la page intégrale ?
if( array_key_exists('event', $_GET) )
{ // morceau de l'interface pour un event particulier
  include_once(root.'inc/fp.php');
  
  $event = event::factory($_GET['event']);
  if( !$event or $event->get_statut() >= event::fini )
  {
    echo "Cet event n'est plus disponible. Peut-être est-il fini ou annulé ?";
    exit(0);
  }
}
elseif( array_key_exists('creer', $_GET) )
{ // morceau de l'interface pour la création d'un nouvel event
  include_once(root.'inc/fp.php');
  
  $event = event::nouveau($_GET['creer']);
}
else
{ // Page en entier

  $textures = false;
  $admin = true;

  include_once(root.'admin/admin_haut.php');
  // include_once(root.'haut_site.php');
  if ($G_maintenance)
  {
  	echo 'Starshine-online est actuellement en cours de mise à jour.<br />
  	le forum est toujours disponible <a href="punbb/">ici - Forum</a>';
    exit(0);
  }
  include_once(root.'admin/menu_admin.php');
?>
  <script type='text/javascript' src='../javascript/jquery/jquery-ui-timepicker-addon.js'></script>
  <div id="contenu">
<?php

  // On regarde s'il y a des evènements non finis
  $events = event::create('','', 'statut DESC', false, 'statut < '.event::fini);
  if( count($events) == 0 )
  {
    // On propose d'en créer un
?>
  <form id="creer_event" method="get" action="event.php">
    Créer un event de type
    <select name='creer'>
<?php
  $types = event::get_types();
  foreach($types as $type)
  {
      echo '<option>'.$type.'</option>';
  }
?>
    </select>
    <input value="Créer" type="submit" onclick="return envoiFormulaire('creer_event', 'contenu');"/>
  </form>
  </div>
<?php
    exit(0);
  }
  else // il y a des évent non fini (ni annulé), on choisi le premier
    $event = $events[0];
}

// On délègue l'affichage à l'objet gérant l'évènement.
if( $event )
  $event->interface_admin();
?>
</div>
</body></html>