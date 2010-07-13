/* -*- mode: java -*- */
function updatePNJ(id) {
		var data = $('#pnj_' + id).serialize();
		//alert(data);
		$.post('edit_pnj.php?mode=update', data, function(res) { $("#upd_res").html(res); });
    return false;
}

function newPNJ() {
		$("#upd_res").load('edit_pnj.php?mode=new');
}

function doNewPnj() {
		var data = $('#newpnj').serialize();
		$.post('edit_pnj.php?mode=donew', data, function(res) { $("#upd_res").html(res); });
		return false;
}
