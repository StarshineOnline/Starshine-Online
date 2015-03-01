
function pose_drapeau(x, y)
{
  // @todo poser une question ?
	//alert("pose_drapeau: " + x + "/" + y);
	var url = 'drapeaux.php?action=pose&x=' + x + '&y=' + y + '&mag_factor=' + mag;
	if (mtop != 0) { url = url + '&mtop=' + mtop; }
	if (mleft != 0) { url = url + '&mleft=' + mleft; }
	return charger(url);
}

function move_r()
{
	//var x = $("#mapim").css("left").substring(0, -2);
	var l = $("#mapim").css("left").split('p');
	var x = new Number(l[0]);
	mleft = x - 50;
	$("#mapim").css("left", mleft + 'px');
	return false;
}

function move_l()
{
	var l = $("#mapim").css("left").split('p');
	var x = new Number(l[0]);
	mleft = x + 50;
	$("#mapim").css("left", mleft + 'px');
	return false;
}

function move_u()
{
	var l = $("#mapim").css("top").split('p');
	var x = new Number(l[0]);
	mtop = x + 50;
	$("#mapim").css("top", mtop + 'px');
	return false;
}

function move_b()
{
	var l = $("#mapim").css("top").split('p');
	var x = new Number(l[0]);
	mtop = x - 50;
	$("#mapim").css("top", mtop + 'px');
	return false;
}

function zoom_m()
{
	mag++;
	aff_img();
	return false;
}

function zoom_l()
{
	if (mag > 1)
	{
		mag--;
		aff_img();
	}
	return false;
}

function aff_img()
{
	var url = 'drapeaux.php?mag_factor=' + mag;
	if (mtop != 0) { url = url + '&mtop=' + mtop; }
	if (mleft != 0) { url = url + '&mleft=' + mleft; }
	return charger(url);
}

function tout_poser()
{
	var url = 'drapeaux.php?action=tout';
	if (mag > 1) { url = url + '&mag_factor=' + mag; }
	if (mtop != 0) { url = url + '&mtop=' + mtop; }
	if (mleft != 0) { url = url + '&mleft=' + mleft; }
	return charger(url);
}