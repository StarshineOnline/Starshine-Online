// Polling arenes 

var t;

function do_poll()
{
	$.ajax({
		type: "GET",
		ifModified: true,
		success: function(msg){
			//alert( "Reload: " + msg );
			do_xsl(msg);
		}
	});

	t = setTimeout("do_poll()", 3000);
				
}

function do_xsl(msg)
{
	alert('here: ' + msg);
	var xsltProcessor = new XSLTProcessor();
	var resultDocument = xsltProcessor.transformToDocument(msg);
}