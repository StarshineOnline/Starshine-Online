// Polling arenes 

var t;
var xsl;
var xsltProcessor;
var time = 2000;

function begin_poll()
{
	if (window.XSLTProcessor) {					
		// Mozilla, Opera, etc.
		$.ajax({
			type: "GET",
			url: "arene.xsl",
			dataType: "xml",
			success: function(msg) {
				try {
					xsltProcessor = new XSLTProcessor();			
				} catch (e) {
					alert('Pas de refresh auto possible: creation XSLTProcessor: ' + e);
					return;
				}
				try {
					xsltProcessor.importStylesheet(msg);
					t = setTimeout("do_poll()", time);
				} catch (e) {
					alert('Pas de refresh auto possible: import XSL: ' + e + ' - msg: ' + msg);
				}
			}
		});
	}
	else {
		t = setTimeout("do_poll()", time);
	}
}

function do_poll()
{
	$.ajax({
		type: "GET",
		ifModified: true,
		success: function(msg) {
			do_xsl(msg);
		}
	});
	t = setTimeout("do_poll()", time);
}

function do_xsl(msg)
{
	if (window.XSLTProcessor) {
		try {
			var resultDocument;
			resultDocument = xsltProcessor.transformToDocument(msg);
			var contents = resultDocument.getElementById("div_map").innerHTML;
			$("div.div_map").html(contents);
		} catch (e) {
			alert('Pas de refresh auto possible: processing XSL: ' + e);
			time = time * 10;
		}
	} else {
		ie_refresh();	
	}
}

var nbr = 0;

function ie_refresh() {
	/* Ca c'est du refresh tout pourri, mais ca suffira pour IE */
	if (nbr == 1) {
		document.location = document.location;
		nbr = 0;
	}
	else { nbr = 1; }
}