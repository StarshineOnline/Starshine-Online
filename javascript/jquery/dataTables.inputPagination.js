$.fn.dataTableExt.oPagination.input = {
    "fnInit": function ( oSettings, nPaging, fnCallbackDraw )
    {
		var oLang = oSettings.oLanguage.oPaginate;
		var oClasses = oSettings.oClasses;
		var fnClickHandler = function ( e ) {
			if ( oSettings.oApi._fnPageChange( oSettings, e.data.action ) )
			{
				fnCallbackDraw( oSettings );
			}
		};
		
		var nFirst = $('<a>').text(oLang.sFirst).attr({"tabindex":oSettings.iTabIndex, "class":oClasses.sPageButton+" "+oClasses.sPageFirst});
		var nPrevious = $('<a>').text(oLang.sPrevious).attr({"tabindex":oSettings.iTabIndex, "class":oClasses.sPageButton+" "+oClasses.sPagePrevious});
		var nNext = $('<a>').text(oLang.sNext).attr({"tabindex":oSettings.iTabIndex, "class":oClasses.sPageButton+" "+oClasses.sPageNext});
		var nLast = $('<a>').text(oLang.sLast).attr({"tabindex":oSettings.iTabIndex, "class":oClasses.sPageButton+" "+oClasses.sPageLast});
		var nPage = $('<span>').text('Page ').attr({"class":"paginate_page"});
		var nInput = $('<input>').attr({"type":"text", "class":"paginate_input"});
		var nOf = $('<span>').attr({"class":"paginate_of"});
		
		/* ID only the first instance of the paging controls.
		 * 'oSettings.aanFeatures.p' array is provided by DataTables to contain the paging controls
		 */
		if ( !oSettings.aanFeatures.p )
		{
			nPaging.id = oSettings.sTableId+'_paginate';
			nFirst.attr("id", oSettings.sTableId+'_first');
			nPrevious.attr("id", oSettings.sTableId+'_previous');
			nNext.attr("id", oSettings.sTableId+'_next');
			nLast.attr("id", oSettings.sTableId+'_last');
			nPage.attr("id", oSettings.sTableId+'_page');
			nInput.attr("id", oSettings.sTableId+'_input');
			nOf.attr("id", oSettings.sTableId+'_of');
		}
		
		$(nPaging).append(
			nFirst
			, nPrevious
			, nPage
			, nInput
			, nOf
			, nNext
			, nLast
		);
		
		oSettings.oApi._fnBindAction( nFirst, {action: "first"}, fnClickHandler );
		oSettings.oApi._fnBindAction( nPrevious, {action: "previous"}, fnClickHandler );
		oSettings.oApi._fnBindAction( nNext, {action: "next"}, fnClickHandler );
		oSettings.oApi._fnBindAction( nLast, {action: "last"}, fnClickHandler );
		$(nInput).keyup( function (e) {
			if ( this.value == "" || this.value.match(/[^0-9]/) )
			{
				/* Nothing entered or non-numeric character */
				return;
			}
			
			var minPage = 1;
			var maxPage = 1;
			if(oSettings._iDisplayLength > 0)
				maxPage = 1 + Math.floor( (oSettings.fnRecordsDisplay()-1) / oSettings._iDisplayLength );
			var newValue = this.value;
			
			if ( e.which == 38 || e.which == 39 )
			{
				newValue++;
				if( newValue > maxPage )
					return;
			}
			else if ( e.which == 37 || e.which == 40 )
			{
				newValue--;
				if( newValue < minPage )
					return;
			}
			
			newValue = Math.max(minPage, Math.min(newValue, maxPage));
			
			var newDisplayStart = oSettings._iDisplayLength * (newValue - 1);
			oSettings._iDisplayStart = newDisplayStart;
			fnCallbackDraw( oSettings );
		} );
		
		/* Take the brutal approach to cancelling text selection */
		$('span', nPaging).add('a', nPaging).bind( 'mousedown', function () { return false; } );
		$('span', nPaging).add('a', nPaging).bind( 'selectstart', function () { return false; } );
	},
	
	"fnUpdate": function ( oSettings, fnCallbackDraw )
	{
		if ( !oSettings.aanFeatures.p )
		{
			return;
		}
		var oClasses = oSettings.oClasses;
		var an = oSettings.aanFeatures.p;
		var nNode;
		
		var iPages = 1;
		if(oSettings._iDisplayLength > 0)
			iPages = Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength);
		var iCurrentPage = 1;
		if(oSettings._iDisplayLength > 0)
			iCurrentPage = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1;
		
		/* Loop over each instance of the pager */
		for ( var i=0, iLen=an.length ; i<iLen ; i++ )
		{
			nNode = an[i];
			if ( !nNode.hasChildNodes() )
			{
				continue;
			}
			
			/* First node and Previous node */
			$('a:lt(2)', nNode)
				.removeClass( oClasses.sPageButton+" "+oClasses.sPageButtonActive+" "+oClasses.sPageButtonStaticDisabled )
				.addClass((iCurrentPage==1) ? oClasses.sPageButtonStaticDisabled : oClasses.sPageButton);
			/* Input node */
			$('input:eq(0)', nNode).val(iCurrentPage);
			/* Of node */
			$('span:eq(1)', nNode).html(' sur '+iPages);
			/* Next node and Last node */
			$('a:gt(1)', nNode)
				.removeClass( oClasses.sPageButton+" "+oClasses.sPageButtonActive+" "+oClasses.sPageButtonStaticDisabled )
				.addClass((iPages===0 || iCurrentPage===iPages || oSettings._iDisplayLength<=0) ? oClasses.sPageButtonStaticDisabled : oClasses.sPageButton);
			
		}
	}
};