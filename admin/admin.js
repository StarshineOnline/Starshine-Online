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

function setUniqueLoot(o) {
  $('#hd-loot-id').val(o);
  $('#item-txt').val(o);
}

function setLootSelector(t) {
  if (t != '' && uLoot[t]) {
    var loot = uLoot[t];
    html = '';
    for (var k in loot) {
      html += '<option value="' + t + k + '">' + loot[k] + '</option>';
    }
    $('#loot-opt').html(html);
  }
  else {
    $('#loot-opt').html('');
  }
}