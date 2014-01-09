filtre = "";

function drop_func( event, ui )
{
  var objet = ui.draggable[0].id.substr(11);
  var drop = this.id.substr(5);
  switch( drop )
  {
  case 'hotel_vente':
    show_modal("inventaire.php?action=hotel_vente&objet="+objet);
    break
  default:
    $("#information").load("inventaire.php?action=drop"+filtre+"&objet="+objet+"&drop="+drop);
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