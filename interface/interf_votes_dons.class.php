<?php
/**
 * @file interf_votes_dons.class.php
 * Affichage des votes & dons
 */
 
/**
 * classe gérant l'affichage des votes & dons
 */
class interf_votes_dons extends interf_onglets
{
	function __construct($categorie)
	{
		global $G_url;
		parent::__construct('ongl_votes_dons', 'votes_dons');
		$url = $G_url->copie('ajax', 2);
		$this->add_onglet('Votes', $url->get('categorie', 'votes'), 'ongl_votes', 'invent', $categorie=='votes');
		$this->add_onglet('Dons', $url->get('categorie', 'dons'), 'ongl_dons', 'invent', $categorie=='dons');
		
		$G_url->add('categorie', $categorie);
		switch($categorie)
		{
		case 'votes':
			$this->get_onglet('ongl_'.$categorie)->add( new interf_votes() );
			break;
		case 'dons':
			$this->get_onglet('ongl_'.$categorie)->add( new interf_dons() );
			break;
		}
	}
}

class interf_votes extends interf_cont
{
	function __construct()
	{
		$this->add( new interf_bal_smpl('p', 'Voter pour Starshine-Online permet d\attirer de nouveau jouer et ainsi de rendre le jeu plus vivant et agréable. Merci de voter aussi souvent que possible.') );
		$liste = $this->add( new interf_bal_cont('ul') );
		$li1 = $liste->add( new interf_bal_cont('li') );
		$lien1 = $li1->add( new interf_bal_cont('a') );
		$lien1->set_attribut('href', 'http://www.jeux-alternatifs.com/StarShine-Online-jeu548_hit-parade_1_1.html');
		$img1 = $lien1->add( new interf_img('http://www.jeux-alternatifs.com/im/bandeau/468x60.gif', 'Jeux alternatifs') );
		$lien1->add( new interf_bal_smpl('p', 'http://www.jeux-alternatifs.com/StarShine-Online-jeu548_hit-parade_1_1.html') );
	}
}

class interf_dons extends interf_cont
{
	function __construct()
	{
		global $db;
		
		$requete = 'SELECT valeur FROM variable WHERE nom LIKE "don_necessaire"';
		$req = $db->query($requete);
		if( $row = $db->read_assoc($req) )
		{
		  $necessaire = $row['valeur'];
		  $requete = 'SELECT valeur FROM variable WHERE nom IN ("don_paypal", "don_sms", "don_pub")';
		  $req = $db->query($requete);
		  $dons = 0;
		  while( $row = $db->read_assoc($req) )
		  {
		    $dons += $row['valeur'];
		  }
		  if( $necessaire != 0 )
		    $ratio_don = floor(100 * ($dons / $necessaire));
		  else
		    $ratio_don = 10;
		  if($ratio_don > 100)
		    $ratio_don = 100;
		  if($ratio_don < 0)
		    $ratio_don = 0;
		  $barre_don = './image/barre/pa'.$ratio_don.'.png';
		  $p = $this->add( new interf_bal_cont('p') );
		  $p->add( new interf_txt('Avancement pour le paiement du serveur : ') );
		  $m = $p->add( new interf_bal_smpl('meter', $ratio_don.'%', array('value'=>$dons, 'min'=>'0', 'max'=>$necessaire)) );
		  $m->set_tooltip($dons.'€ / '.$necessaire.' €');
			$this->add( new interf_bal_smpl('p', 'Vous pouvez aider à financer le serveur en fasiant un don. Merci d\'avance.') );
		}
		  
	  //Paypal
		$paypal_url = 'https://www.paypal.com/fr/cgi-bin/webscr';
		// $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$div_pp = $this->add( new interf_bal_cont('div') );
		$div_pp->add( new interf_bal_smpl('h4', 'Faire un don via paypal') );
		$div_pp->add( new interf_bal_smpl('p', 'Paypal permet de faire un don par l\'intermédiaire d\'un compte Paypal ou par carte banquaire (même sans compte Paypal).') );
		$form_pp = $div_pp->add( new interf_form($paypal_url, 'don_paypal') );
		$form_pp->add( new interf_chp_form('hidden', 'cmd', false, '_s-xclick') );
		$form_pp->add( new interf_chp_form('hidden', 'hosted_button_id', false, 'CVXP4LZ8DWHV8') );
		$sub_pp = $form_pp->add( new interf_chp_form('image', 'submit') );
		$sub_pp->set_attribut('src', 'https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donate_SM.gif');
		$sub_pp->set_attribut('border', '0');
		$sub_pp->set_attribut('alt', 'PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !');
		$img_pp = $form_pp->add( new interf_img('https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif') );
		$img_pp->set_attribut('border', '0');
		$img_pp->set_attribut('width', '1');
		$img_pp->set_attribut('height', '1');
		
		// Rentabilliweb
		$div_rw = $this->add( new interf_bal_cont('div') );
		$div_pp->add( new interf_bal_smpl('h4', 'Faire un don pas SMS / offres "offerpass"') );
		$div_pp->add( new interf_bal_smpl('p', 'Sur chaque SMS envoyé depuis la France métropolitaine nous récupérons 20cts qui serviront à payer le serveur et le nom de domaine.') );
		$div_pp->add( new interf_bal_smpl('p', 'Vérifiez tout de même que le surcout du SMS est bien compris décompté de votre fortait (et qu\'il vous reste assez dessus), le but est d\'utiliser l\'argent que vous avez déjà payé.') );
		$div_pp->add( new interf_bal_smpl('p', 'Vous pouvez aussi utiliser "offerpass" en souscrivant à une offre partenaire. Si l\'une des offres vous intéressent celà permet de participer au financement sans rien dépenser.') );
		$tbl1 = new interf_tableau(array('border'=>'0', 'cellpadding'=>'0', 'cellspacing'=>'0', 'style'=>'border:5px solid #E5E5E5; margin: 5px auto;'), false, false, false, false);
		$div_pp->add($tbl1);
		$tbl2 = new interf_tableau(array('cellpadding'=>'0', 'cellspacing'=>'0', 'style'=>'width: 436px;  border: solid 1px #AAAAAA;'), false, false, false, false);
		$tbl1->nouv_cell($tbl2);
		$tbl3 = new interf_tableau(array('width'=>'100%', 'border'=>'0', 'cellpadding'=>'0', 'cellspacing'=>'0', 'style'=>'background-color: #FFFFFF;'), false, false, false, false);
		$tbl2->nouv_cell($tbl3, array('colspan'=>2) );
		$tbl3->nouv_cell( new interf_img('http://payment.rentabiliweb.com/data/i/component/logo-form.gif', 'Paiement sécurisé par Rentabiliweb', array('width'=>173, 'height'=>20, 'style'=>'padding: 1px 0 0 5px')) );
		$div1 = new interf_bal_cont('div', array('style'=>'text-align: right; padding: 2px; font-family: Arial, Helvetica, sans-serif; min-height:30px;'));
		$tbl3->nouv_cell($div1);
		$div1->add( new interf_bal_smpl('span', 'Solutions de paiements sécurisés', array('style'=>'color: #3b5998; font-weight:bold; font-size: 12px;')) );
		$div1->add( new interf_bal_smpl('br') );
		$div1->add( new interf_bal_smpl('span', 'Secure payment solution', array('style'=>'color: #5c5c5c; font-size: 11px; font-style: italic;')) );
		$tbl2->nouv_ligne();
		$div2 = new interf_bal_cont('div', array('style'=>'text-align: center; padding: 2px; font-family: Arial, Helvetica, sans-serif; min-height:30px;'));
		$tbl2->nouv_cell($div2, array('style'=>'border-top: 1px solid #AAAAAA; border-bottom: 1px solid #AAAAAA;background-color: #F7F7F7;', 'colspan'=>2) );
		$div2->add( new interf_bal_smpl('span', 'Choisissez votre pays et votre moyen de paiement pour obtenir votre code', array('style'=>'color: #3b5998; font-weight:bold; font-size: 12px;')) );
		$div2->add( new interf_bal_smpl('br') );
		$div2->add( new interf_bal_smpl('span', 'Please choose your country and your kind of payment to obtain a code', array('style'=>'color: #5c5c5c; font-size: 11px; font-style: italic;')) );
		$tbl2->nouv_ligne( array('height'=>250) );
		$tbl2->nouv_cell( new interf_bal_smpl('iframe', false, array('name'=>'rweb_display_frame', 'width'=>280, 'height'=>250, 'frameborder'=>'0', 'marginheight'=>'0', 'marginwidth'=>'0', 'scrolling'=>'no', 'src'=>'http://payment.rentabiliweb.com/form/acte/frame_display.php?docId=118738&siteId=399811&cnIso=geoip&lang=fr&skin=default')), array('style'=>'background-color: #FFFFFF;', 'width'=>280) );
		$tbl2->nouv_cell( new interf_bal_smpl('iframe', false, array('name'=>'rweb_flags_frame', 'width'=>156, 'height'=>250, 'frameborder'=>'0', 'marginheight'=>'0', 'marginwidth'=>'0', 'scrolling'=>'no', 'src'=>'http://payment.rentabiliweb.com/form/acte/frame_flags.php?docId=118738&siteId=399811&lang=fr&skin=default')), array('style'=>'border-left: 1px solid #AAAAAA; background-color: #FFFFFF;', 'width'=>156) );
		$tbl2->nouv_ligne();
		$form1 = new interf_form('http://payment.rentabiliweb.com/access.php', array('id'=>'rweb_tickets_118738', 'style'=>'margin: 0px; padding: 0px;'));
		$td1 = $tbl2->nouv_cell($form1, array('style'=>'border-top: 1px solid #AAAAAA; background-color: #F7F7F7;', 'colspan'=>2));
		$tbl4 = $form1->add( new interf_tableau(array('width'=>400, 'cellpadding'=>'0', 'cellspacing'=>'0', 'style'=>'margin: 2px auto;'), false, false, false, false) );
		$label1 = new interf_bal_cont('label', array('for'=>'code_0', 'style'=>'font-family:Arial, Helvetica, sans-serif;font-size: 12px; font-weight:bold; color:#3b5998; padding: 2px; margin: 0px;') );
		$label1->add( new interf_txt('Saisissez votre code d\'accès et validez :') );
		$label1->add( new interf_bal_smpl('br') );
		$label1->add( new interf_bal_smpl('span', 'Please enter your access code :', array('style'=>'font-size: 11px; font-style: italic;color:#5c5c5c;')) );
		$tbl4->nouv_cell($label1, array('style'=>'text-align: center'));
		$tbl4->nouv_ligne();
		$td2 = $tbl4->nouv_cell(null, array('style'=>'text-align: center'));
		$td2->add( new interf_chp_form('text', 'code[0]', false, false, array('id'=>'code_0', 'style'=>'border: solid 1px #3b5998; padding: 2px; font-weight: bold; color:#3b5998; text-align: center;')) );
		$td2->add( new interf_chp_form('hidden', 'docId', false, '118738') );
		$td2->add( new interf_chp_form('button', false, false, false, array('id'=>'rweb_sub_118738', 'style'=>'width: 40px; height:20px; vertical-align:middle; margin-left: 5px; border: none; background:url(http://payment.rentabiliweb.com/data/i/component/button_okdefault.gif);', 'alt'=>'Ok', 'onclick'=>'getElementById(\'rweb_sub_118738\').disabled=true;document.getElementById(\'rweb_tickets_118738\').submit();')) );
		$div3 = $td1->add( new interf_bal_cont('div', array('style'=>'text-align: center; padding: 2px; font-family: Arial, Helvetica, sans-serif; clear: both;')) );
		$div3->add( new interf_bal_smpl('span', 'Votre navigateur doit accepter les cookies', array('style'=>'font-weight:bold; font-size: 10px; color: #3b5998;')) );
		$div3->add( new interf_bal_smpl('br') );
		$div3->add( new interf_bal_smpl('span', 'Please check that your browser accept the cookies', array('style'=>'font-style: italic; font-size: 10px; color: #5c5c5c;')) );
		$div4 = $td1->add( new interf_bal_cont('div', array('style'=>'text-align: center; padding: 2px; font-family: Arial, Helvetica, sans-serif;')) );
		$div4->add( new interf_bal_smpl('a', 'Support technique', array('href'=>'javascript:;', 'onclick'=>'javascript:window.open(\'http://payment.rentabiliweb.com/support/?docId=118738&siteId=399811&lang=fr\',\'rentabiliweb_help\',\'toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=1,copyhistory=0,menuBar=0,width=995,height=630\');', 'style'=>'color: #3b5998; font-weight:bold; font-size: 12px; text-decoration: none;')) );
		$div4->add( new interf_bal_smpl('span', ' / ', array('style'=>'color: #AAAAAA;')) );
		$div4->add( new interf_bal_smpl('a', 'Technical support', array('href'=>'javascript:;', 'onclick'=>'javascript:window.open(\'http://payment.rentabiliweb.com/support/?docId=118738&siteId=399811&lang=en\',\'rentabiliweb_help\',\'toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=1,copyhistory=0,menuBar=0,width=995,height=630\');', 'style'=>'color: #5c5c5c; font-weight:normal; font-size: 12px; text-decoration: none;')) );
	}
}

?>