/*$.ajax({
   type: "GET",
   url: "inventaire_slot.php",
   data: "javascript=ok&filtre=utile",
   success: function(data) {
	    $('#inventaire_slot').append(data);
   }
 });
$.ajax({
   type: "GET",
   url: "inventaire_slot.php",
   data: "javascript=ok&filtre=arme",
   success: function(data) {
	    $('#inventaire_slot').append(data);
   }
 });
$.ajax({
   type: "GET",
   url: "inventaire_slot.php",
   data: "javascript=ok&filtre=armure",
   success: function(data) {
	    $('#inventaire_slot').append(data);
   }
 });
$.ajax({
   type: "GET",
   url: "inventaire_slot.php",
   data: "javascript=ok&filtre=autre",
   success: function(data) {
	    $('#inventaire_slot').append(data);
   }
 });*/

/*function set_drag()
{
}*/

filtre = "";

function drop_func( event, ui )
{
  var objet = ui.draggable[0].id.substr(11);
  var drop = this.id.substr(5);
  $("#information").load(page+"?action=drop"+filtre+"&objet="+objet+"&drop="+drop);
}

function dragndrop(source, cible, page)
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
	/*$( "#hdv" ).droppable({ accept: source, activeClass: "ui-state-hover", hoverClass: "ui-state-highlight",
	   drop: function( event, ui )
     {
				$( this ).addClass( "ui-state-highlight" );
				// il peut y avoir plusieurs objets pour la meme cible
				$( source ).each(function(index, objet)
        {
					var classe=$(this).attr("class");
					var testclass=source.replace(".","")+" ui-draggable ui-draggable-dragging"
					//alert(classe+" VS "+testclass);
					if(classe==testclass)
					{
						//alert($(this).attr("id"));
						var target = $(this).children(".hdv").attr("value");
						//alert(target);
						// on envoie une requete ajax pour mettre l'objet a l'HDV
						$.ajax({
							url : "inventaire.php",
							type : "GET",
							data : target,
							// en cas de reussite, forcer le refresh
							success : function(reponse) {
								//alert(reponse)
								$("#information").empty();
								$("#information").append(reponse);
								//$("#information").load("inventaire.php");
								$(".overlib").hide();
							}
						});
					}
				});
			 }
			});
	$( "#marchand" ).droppable({ accept: source, activeClass: "ui-state-hover", hoverClass: "ui-state-highlight",
	 drop: function( event, ui )
   {
		  $( this ).addClass( "ui-state-highlight" );
		  // il peut y avoir plusieurs objets pour la meme cible
		  $( source ).each(function(index, objet)
      {
				var classe=$(this).attr("class");
				var testclass=source.replace(".","")+" ui-draggable ui-draggable-dragging"
				//alert(classe+" VS "+testclass);
				if(classe==testclass)
				{
					//alert($(this).attr("id"));
					var target = $(this).children(".marchand").attr("value");
					//alert(target);
					// on envoie une requete ajax pour vendre l'objet
					$.ajax({
						url : "inventaire.php",
						type : "GET",
						data : target,
						// en cas de reussite, forcer le refresh
						success : function(reponse) {
							//alert(reponse)
							$("#information").empty();
							//$("#information").append(reponse);
							$("#information").load("inventaire.php");
							$(".overlib").hide();
						}
					});
				}
			});
		}
	});*/
};