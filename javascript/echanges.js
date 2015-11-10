
function ajout_objet_echg()
{
	var div = document.getElementById("liste_objets");
	var sel = document.getElementById("nouv_obj");
	var obj = sel.options[sel.selectedIndex];
	var nouv_div = creer_element("div", "obj_"+obj.value, "input-group", div);
	creer_element("span", null, "input-group-addon", nouv_div, obj.text);
	//creer_chp_form("hidden", "obj_"+obj.value, obj.value, nouv_div);
	creer_element("span", null, "input-group-addon", nouv_div, "X");
	var nbr = creer_chp_form("number", "nbr_"+obj.value, 1, nouv_div);
	var n = obj.getAttribute("data-nbr");
	/*if( n == 1 )
	{
		nbr.disabled = true;
	}
	else
	{*/
		nbr.min = 1;
		nbr.max = n;
		nbr.step = 1;
	//}
	var span_btn = creer_element("span", null, "input-group-btn", nouv_div);
	var btn = creer_element("button", null, "btn btn-default icone icone-croix", span_btn);
	btn.type = "button";
	btn.setAttribute("onclick", "return suppr_objet_echg("+obj.value+");");
	sel.remove(sel.selectedIndex);
	return false;
}

function suppr_objet_echg(ind)
{
	var div = document.getElementById("liste_objets");
	var elt = document.getElementById("obj_"+ind);
	var sel = document.getElementById("nouv_obj");
	var opt = document.createElement("option");
	opt.text = $(elt).find("span")[0].innerHTML;
	opt.value = ind;
	opt.setAttribute("data-nbr", $(elt).find("input").attr("max"));
	sel.add(opt);
	div.removeChild(elt);
	return false;
}