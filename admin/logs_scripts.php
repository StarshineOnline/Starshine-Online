<?php
if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;
$textures = false;

include_once(root.'admin/admin_haut.php');

setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');
include_once(root.'admin/menu_admin.php');

$script = array_key_exists('script', $_GET) ? $_GET['script'] : 'journalier';
$date =  array_key_exists('date', $_GET) ? $_GET['date'] : date('d/m/Y');

$elts = explode('/', $date);


if( joueur::factory()->get_droits() & joueur::droit_admin )
{
?>
  <script type='text/javascript' src='../javascript/jquery/jquery-ui-timepicker-addon.js'></script>
	<div id="contenu">
		<div id="centre3">
      <div>
        <form id="form_date" method="get" action="logs_scripts.php">
          Date de début :
          <input type="hidden" name="script" value="<?php echo $script; ?>" />
          <input name="date" type="text" id="date" value="<?php echo $date; ?>" />
          <input type="submit" value="Voir" />
        </form>
      </div>
      <script type="text/javascript">
        $( "#date" ).datepicker();
        $( "#date" ).datepicker("option", "dateFormat", "dd/mm/yy");
				$( "#date" ).datepicker("setDate",  new Date(<?php echo $elts[2].','.$elts[1].','.$elts[0] ?>) );
      </script>
      <div style="border: solid 1px gray;">
<?php
			$addr = $G_logs.$elts[2].'/'.$elts[1].'/'.$elts[0].'/'.$script.'.log';
			$fich = fopen($addr, 'r');
			if( $fich )
			{
				while( $lgn = fgets($fich) )
				{
					echo $lgn.'<br/>';
				}
				fclose($fich);
			}
			echo 'erreur fichier : '.$addr;
?>
			</div>
		</div>
	</div>
<?php
}
include_once(root.'bas.php');
?>