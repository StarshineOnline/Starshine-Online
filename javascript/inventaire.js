page = "perso";
slot = "utile";

function get_url_invent(options)
{
  return "inventaire.php?"+options+"&page="+page+"&slot="+slot;
}

function ajout_filtre_form(id)
{
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
    //$("#information").load( get_url_invent("action=desequip&zone="+drag[0].id.substr(5)) );
    charger( get_url_invent("action=desequip&zone="+drag[0].id.substr(5)) );
    break;
  case 'vendre_marchand':
    vente(drag[0].id);
    $(drag[0]).hide();
    break;
  default:
    //$("#information").load( get_url_invent("action="+drop+"&objet="+objet) );
    charger( get_url_invent("action="+drop+"&objet="+objet) );
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

objets_vente = new Array();

function vente(id)
{
  var tbody = document.getElementById("vente_table");
  if( !tbody )
  {
    var pere = document.getElementById("contenu_droit");
    var vente = creer_element("div", "vente", "panel panel-default", pere, null, pere.firstChild);
    varentete = creer_element("div", null, "panel-heading", vente, "Vente");
    var corps = creer_element("div", null, "panel-body", vente);
    var pied = creer_element("div", null, "panel-footer", vente);
    pied.style = "text-align:right;";
    var btns = creer_element("div", null, "btn-group btn-group-xs", pied);
    creer_bouton("Annuler", btns, /*'$("#information").load( get_url_invent() );'*/'annuler_vente();', "default");
    creer_bouton("Vendre", btns, "vendre();", "primary");
    var table = creer_element("table", null, "table", corps);
    var thead = creer_element("thead", null, null, table);
    var tr = creer_element("tr", null, null, thead);
    creer_element("th", null, null, tr, "Nom");
    creer_element("th", null, null, tr, "Prix");
    creer_element("th", null, null, tr, "Nombre");
    creer_element("th", null, null, tr, "Montant");
    creer_element("th", null, null, tr, "&nbsp;");
    tbody = creer_element("tbody", "vente_table", null, table);
    var tfoot = creer_element("tfoot", null, null, table);
    tr = creer_element("tr", null, null, tfoot);
    var total = creer_element("th", null, null, tr, "Total");
    total.style = "text-align:right; padding-right: 30px;";
    total.colSpan = 3;
    var tot = creer_element("th", "total", null, tr, "0");
    creer_element("th", null, null, tr, "&nbsp;");
  }
  else
    var tot = document.getElementById("total");
  var i;
  var objet = document.getElementById(id);
  var nom = objet.getElementsByClassName("nom_obj");
  var prix = Number( objet.getAttribute("data-prix") );
  var nbr = Number( objet.getAttribute("data-nombre") );
  var index = objets_vente.length;
  objets_vente[index] = new Object();
  objets_vente[index].slot = id.substr(11);
  objets_vente[index].prix = prix;
  objets_vente[index].nombre = 1;
  var lgn = creer_element("tr", "vente_"+index, null, tbody);
  creer_element("td", null, null, lgn, nom[0].innerHTML);
  creer_element("td", null, null, lgn, prix);
  if( nbr > 1 )
  {
    var td = creer_element("td", null, null, lgn);
    var input = creer_element("input", null, null, td);
    input.value = 1;
    $(input).spinner({min:1, max:nbr, spin: function (event, ui)
    {
      lgn.childNodes[3].innerHTML = prix * ui.value;
      objets_vente[index].nombre = ui.value;
      calcul_total();
    } });
    input.style = "width: 30px; height:14px;";
    input.setAttribute("onkeyup", "javascript:calcul_prix("+index+");");
  }
  else
    creer_element("td", null, null, lgn, "1");
  creer_element("td", null, "prix", lgn, prix);
  var td = creer_element("td", null, null, lgn);
  creer_bouton("×", td, "suppr_obj_vente("+index+");").className = "close";
  calcul_total();
}

function calcul_prix(index)
{
  var elt = document.getElementById("vente_"+index);
  var input = elt.childNodes[2].getElementsByTagName("input");
  var nbr = $(input[0]).spinner("value");
  elt.childNodes[3].innerHTML = nbr * objets_vente[index].prix;
  objets_vente[index].nombre = nbr;
  calcul_total();
}

function calcul_total()
{
  var tot = 0;
  for(var i=0; i<objets_vente.length; i++)
  {
    tot += objets_vente[i].prix * objets_vente[i].nombre;
  }
  document.getElementById("total").innerHTML = tot;
}

function suppr_obj_vente(index)
{
  var obj = $("#invent_slot"+objets_vente[index].slot);
  obj.animate(obj.data("ui-draggable").originalPosition,"slow");
  obj.show();
  objets_vente[index].nombre = 0;
  var elt = document.getElementById("vente_"+index);
  elt.parentNode.removeChild( elt );
  calcul_total();
}

function annuler_vente()
{
  for(var i=0; i<objets_vente.length; i++)
  {
    if( objets_vente[i].nombre )
    {
      var obj = $("#invent_slot"+objets_vente[i].slot);
      obj.animate(obj.data("ui-draggable").originalPosition,"slow");
      obj.show();
    }
  }
  var elt = document.getElementById("vente");
  elt.parentNode.removeChild( elt );
  objets_vente = new Array();
}

function vendre()
{
  var objs = "";
  for(var i=0; i<objets_vente.length; i++)
  {
    if( objets_vente[i].nombre )
    {
      if( objs.length )
        objs += "-";
      objs += objets_vente[i].slot + "x" + objets_vente[i].nombre;
    }
  }
  $("#information").load( get_url_invent("action=vente&objets="+objs) );
  alert(get_url_invent("action=vente&objets="+objs));
}

function start_drag(event, ui)
{
  if( ui.helper.data('bs.popover') )
    ui.helper.data('bs.popover').options.trigger = 'click';
  else
  {
    var attr = ui.helper.attr("onclick");
    ui.helper.attr("onclick", "");
    ui.helper.attr("data-click", attr);
  }
  var vente = document.getElementById("drop_vendre_marchand");
  if( vente && ui.helper.attr("class").indexOf("vendre_marchand") > -1 )
  {
    var elt = vente.getElementsByClassName("infos");
    elt[0].innerHTML = "prix : " + ui.helper.attr("data-prix") + " stars";
  }
  var utiliser = document.getElementById("drop_utiliser");
  if( utiliser && ui.helper.attr("class").indexOf("utiliser") > -1 )
  {
    var elt = utiliser.getElementsByClassName("infos");
    var infos = ui.helper.children(".infos");
    //alert(infos[0]);
    elt[0].innerHTML = infos[0].innerHTML;
  }
}

function stop_drag(event, ui)
{
  if( ui.helper.data('bs.popover') )
    ui.helper.data('bs.popover').options.trigger = 'click';
  else
  {
    var attr = ui.helper.attr("data-click");
    ui.helper.attr("onclick", attr);
  }
  var vente = document.getElementById("drop_vendre_marchand");
  if( vente )
  {
    var elt = vente.getElementsByClassName("infos");
    elt[0].innerHTML = "";
  }
  var utiliser = document.getElementById("drop_utiliser");
  if( utiliser )
  {
    var elt = utiliser.getElementsByClassName("infos");
    elt[0].innerHTML = "";
  }
}