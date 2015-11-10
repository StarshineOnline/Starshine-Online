<?php
// ???
if (file_exists('root.php'))
  include_once('root.php');
?><html>
<head>
<script type="text/javascript">
var infos = "getinfos.php?monsters=1";
var parser = new DOMParser();
var xpe = new XPathEvaluator();
var nsResolver = null;
var lastxml = null;
var showkingdoms = false;
var kingdomstyle = [];
var prkstyle = '<div class="boite_royaume">' + 
'<div class="marque_royaume" style="border: 1px solid rgb';
var pokstyle = ';">';
kingdomstyle[0] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[1] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[2] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[3] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[4] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[5] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[6] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[7] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[8] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[9] =  prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[10] = prkstyle + '(92, 30, 0)' + pokstyle;
kingdomstyle[11] = prkstyle + '(92, 30, 0)' + pokstyle;

function XPathNode(aNode, aExpr) {
  if (nsResolver == null)
    nsResolver = xpe.createNSResolver(aNode.ownerDocument == null ?
				      aNode.documentElement :
				      aNode.ownerDocument.documentElement);
  return xpe.evaluate(aExpr, aNode, nsResolver, 0, null);
}

function evaluateXPath(aNode, aExpr) {
  if (nsResolver == null)
    nsResolver = xpe.createNSResolver(aNode.ownerDocument == null ?
				      aNode.documentElement :
				      aNode.ownerDocument.documentElement);
  var result = xpe.evaluate(aExpr, aNode, nsResolver, 0, null);
  var found = [];
  var res;
  while ((res = result.iterateNext()))
    found.push(res);
  return found;
}

var MAX_DUMP_DEPTH = 2;
function dumpObj(obj, name, indent, depth) {
  if (depth > MAX_DUMP_DEPTH) {
    return indent + name + ": <Maximum Depth Reached>\n";
  }
  if (typeof obj == "object") {
    var child = null;
    var output = indent + name + "\n";
    indent += "\t";
    for (var item in obj)
      {
	try {
	  child = obj[item];
	} catch (e) {
	  child = "<Unable to Evaluate>";
	}
	if (typeof child == "object") {
	  //output += dumpObj(child, item, indent, depth + 1);
	} else {
	  output += indent + item + ": " + child + "\n";
	}
      }
    return output;
  } else {
    return obj;
  }
}

function updatemap(xml)
{
  var squares = evaluateXPath(xml, "/infos/square/@x");
  var xmin = 1000;
  var xmax = -1;
  for (var i in squares) {
    if (squares[i].value < xmin) xmin = squares[i].value;
    if (squares[i].value > xmax) xmax = squares[i].value;
  }
  var squares = evaluateXPath(xml, "/infos/square/@y");
  var ymin = 1000;
  var ymax = -1;
  for (var i in squares) {
    if (squares[i].value < ymin) ymin = squares[i].value;
    if (squares[i].value > ymax) ymax = squares[i].value;
  }
  var res = '<table cellpadding="0" cellspacing="0" style="margin: 0 auto;">'
    + '<tr><td class="bord_carte_haut_gauche"><a '
    + 'title="Afficher / Masquer les royaumes" onclick="switch_map();" '
    + 'style="cursor : pointer;"><img src="image/icone/royaume_icone.png" '
    + 'alt="Royaume" title="Royaume" style="vertical-align : middle;" />'
    + '</a></td>';
  for (var x = xmin; x <= xmax; x++) {
    res += '<td class="bord_carte_haut" style="color : white;">' + x + '</td>';
  }
  res += '</tr>';
  for (var y = ymin; y <= ymax; y++) {
    res += '<tr><td style="color : white;" class="bord_carte_gauche">' + y
      + '</td>';
    for (var x = xmin; x <= xmax; x++) {
      var square = XPathNode(xml, "/infos/square[@x="+x+" and @y="+y+"]");
      square = square.iterateNext();
      res += '<td class="decor tex' + square.getAttribute('decor') + 
	'" onClick="show_square_info(' + x + ',' + y + ')">';
      if (showkingdoms) {
	res += kingdomstyle[square.getAttribute('royaume')];
      }
      if (square.hasChildNodes()) {
	var top = square.firstChild;
	var istyle = ' style="vertical-align : middle; margin : 0px;padding' +
	' : 0px;" onmousemove="show_bubble(' + x + ', ' + y + ', event, \'block\')"' +
	' onmouseout="show_bubble(' + x + ', ' + y + ', event, \'none\')" ';
	if (top.nodeName == 'npc') {
	  res += '<img src="image/pnj/' + top.getAttribute('image') +
	  '.png" alt="' + top.getAttribute('nom') + '"' + istyle + '/>';
	}
	else if (top.nodeName == 'pc') {
	  res += '<img src="image/personnage/' + top.getAttribute('race') +
	  '/' + top.getAttribute('image') + '.png" alt="' + 
	  top.getAttribute('race') + '"' + istyle + '/>';
	}
	else if (top.nodeName == 'building') {
	  res += '<img src="image/batiment/' + top.getAttribute('image') +
	  '.png" alt="' + top.getAttribute('nom') + '"' + istyle + '/>';
	}
	else if (top.nodeName == 'flag') {
	  if (top.getAttribute('type') == 'drapeau') {
	    res += '<img src="image/drapeaux/' + top.getAttribute('image') +
	    '.png" alt="' + top.getAttribute('nom') + '"' + istyle + '/>';
	  }
	  else {
	    res += '<img src="image/batiment/' + top.getAttribute('image') +
	    '.png" alt="' + top.getAttribute('nom') + '"' + istyle + '/>';
	  }

	}
	else if (top.nodeName == 'monster') {
	  res += '<img src="image/monstre/' + top.getAttribute('lib') +
	  '.png" alt="' + top.getAttribute('nom') + '"' + istyle + '/>';
	}
      }
      //else res += x + "," + y; // Temporaire
      if (showkingdoms) {
	res += '</div></div>';
      }
      res += '</td>';
      //res += "<td><pre>"+x+","+y+": "+dumpObj(square)+"</pre></td>";
    }
    res += "</tr>\n";
  }
  res = res + "</table>";
  document.getElementById("map").innerHTML = res;
}

function refresh() {
  var req = new XMLHttpRequest();
  req.open('GET', infos, true); 
  req.onreadystatechange = function (aEvt) {
    if (req.readyState == 4) {
      if(req.status == 200) {
	document.getElementById("xml").innerHTML = "Got: " + req.responseText;
	lastxml = parser.parseFromString(req.responseText, "text/xml");
	updatemap(lastxml);
      }
      else {
	alert("Erreur pendant le chargement de la page.\n");
      }
    }
  };
  req.send(null);
}

function show_bubble(x, y, evt, display) {
  // TODO
  var bx = evt.pageX;
  var by = evt.pageY;
  var dwidth = document.width;
  var dheight = document.height;
  var element = document.getElementById("bubble");
  var square = XPathNode(lastxml, "/infos/square[@x="+x+" and @y="+y+"]");
  square = square.iterateNext();
  //element.innerHTML = dumpObj(square);
  var content = '';
  if (square.hasChildNodes()) {
    var current = square.firstChild;
    var nummonsters = 1;
    while (current != null) {
      if (current.nodeName == 'monster') {
	if (current.nextSibling && current.nextSibling.nodeName == 'monster' && 
	    current.nextSibling.getAttribute('nom') == current.getAttribute('nom'))
	  nummonsters++;
	else {
	  content += '<span class="info_monstre">Monstre</span> - ' + current.getAttribute('nom');
	  if (nummonsters > 1) content += ' x' + nummonsters;
	  content += '<br/>';
	  nummonsters = 1;
	}
      }
      else if (current.nodeName == 'building') {
	content += '<strong>Batiment</strong> - ' +
	  current.getAttribute('nom') + '<br/>';
      }
      else if (current.nodeName == 'npc') {
	content += '<strong>PNJ</strong> - ' +
	  current.getAttribute('nom') + '<br/>';
      }
      else if (current.nodeName == 'pc') {
	content += '<span class="info_joueur">' + current.getAttribute('nom')
	  + '</span> - ' + current.getAttribute('race');
	if (current.hasAttribute('level')) {
	  content += ' - Niv ' + current.getAttribute('level');
	}
	content += '<br />';
      }
      else if (current.nodeName == 'flag') {
        content += '<strong>' + current.getAttribute('nom') + '</strong><br/>';
      }
      current = current.nextSibling;
    }
    element.innerHTML = content;
  }
  //var ehg = parseInt(element.style.top);
  var ewidth = parseInt(element.style.width);
  var eheight = parseInt(element.clientHeight);
  if ((bx + ewidth) > dwidth)
    {
      bx = bx - ewidth - 15;
    }
  else bx += 10;
  if ((by + eheight) > dheight)
    {
      by = by - eheight - 15;
    }
  else by += 10;
  element.style.top = by + "px";
  element.style.left = bx + "px";
  element.style.display = display;
}

function show_square_info(x, y) {
  var square = XPathNode(lastxml, "/infos/square[@x="+x+" and @y="+y+"]");
  // TODO ...
}

function switch_map() {
  showkingdoms = !showkingdoms;
  updatemap(lastxml);
}
</script>
<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="css/interface.css" />
<link rel="stylesheet" type="text/css" media="screen,projection" title="Normal" href="css/texture.css" />
<title>Test map 3</title>
</head>
<body>
<input type="button" value="test" onclick="javascript:refresh();" />
<div id="map" style="border: 1px solid black">
...
</div>
<div id="xml" style="border: 1px solid black">
...
</div>
<div id="bubble" class="jsmap">...</div>
</body>
