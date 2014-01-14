page = "perso";
slot = "utile";

function get_url_invent(options)
{
  return "inventaire.php?"+options+"&page="+page+"&slot="+slot;
}

function ajout_filtre_form(id)
{
  //alert("coucou");
  var elt = document.getElementById(id);
  var chp_page = document.createElement("input");
  chp_page.type = 'hidden';
  chp_page.name = 'page';
  chp_page.value = page;
  elt.appendChild(chp_page);
  var chp_slot = document.createElement("input");
  chp_slot.type = 'hidden';
  chp_slot.name = 'slot';
  chp_slot.value = slot;
  elt.appendChild(chp_slot);
}

function drop_func( event, ui )
{
  var drag = ui.draggable;
  var objet = drag[0].id.substr(11);
  var drop = this.id == 'slots' ? 'desequip' : this.id.substr(5);
  switch( drop )
  {
  case 'hotel_vente':
    show_modal( get_url_invent("action=hotel_vente&objet="+objet) );
    drag.animate(drag.data("ui-draggable").originalPosition,"slow");
    break;
  case 'enchasser':
    show_modal( get_url_invent("action=gemme&objet="+objet) );
    drag.animate(drag.data("ui-draggable").originalPosition,"slow");
    break;
  case 'desequip':
    $("#information").load( get_url_invent("action=desequip&zone="+drag[0].id.substr(5)) );
    break;
  default:
    $("#information").load( get_url_invent("action="+drop+"&objet="+objet) );
  }
}

function show_modal(url)
{
  var modal = document.getElementById("modal");
  if( !modal )
  {
    var cont =  document.getElementById("conteneur");
    modal = document.createElement("div");
    modal.id = "modal";
    modal.className = "modal fade";
    modal.setAttribute("role", "dialog");
    modal.tabIndex = "-1";
    modal.setAttribute("aria-labelledby", "modalLabel");
    cont.appendChild(modal);
  }
  modal.innerHTML = getWait();
  $("#modal").modal('show');
  $("#modal").load(url);
}

/*function dragndrop(source, cible, page)
{
	// on rend les objets de l'inventaire draggable
	$( source ).draggable({ helper: "original", tolerance: "touch", revert: "invalid" });
	// les drop zones n'acceptent que les drag du meme type
	$( cible ).droppable({accept: source, activeClass: "invent_cible", hoverClass: "invent_hover",
	   drop: function( event, ui )
     {
				$( this ).addClass( "ui-state-highlight" );
				// il peut y avoir plusieurs objets pour la meme cible
				$( source ).each(function(index, objet)
        {
				  var id = $(this).attr("id");
					//var testclass=source.replace(".","")+" ui-draggable ui-draggable-dragging"
					//alert(id+" VS "+source);
					if(source == "#" + id)
					{
            $("#information").load(page+"?action=equip&key_slot="+id.substr(11));
				  }
			 });

    }
	});
};*/