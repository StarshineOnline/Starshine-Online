	
$(function()
{
	$("#actions").sortable(
	{
		stop : function(event, ui)
		{
			var tab = $(this).sortable('toArray');
			var id_elt = ui.item[0].id;
			for(i=0;i<tab.length;i++)
			{
				if( tab[i] == id_elt )
					break;
			}
			var url = $('<textarea />').html(url_page).text();
			charger(url+"&action=change_ordre&ligne="+id_elt.substr(7)+"&pos="+i);
			return;
    }   		          	          
  });
  alert('ok');
});

function change_script_nombre(elt, url)
{
	return charger(url + "&valeur=" + elt.value);
}
