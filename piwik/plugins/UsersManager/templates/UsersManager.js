function changeSite()
{
	var action = $('form#accessSites').attr('action');
	var idsite = getIdSites();

	action = action + '&idsite=' + idsite;

	window.location = action;
	return false;
}

function getUpdateUserAJAX( row )
{
	var ajaxRequest = piwikHelper.getStandardAjaxConf('ajaxLoadingUsersManagement', 'ajaxErrorUsersManagement');
	
	var parameters = {};
	parameters.module = 'API';
	parameters.format = 'json';
 	parameters.method =  'UsersManager.updateUser';
 	parameters.userLogin = $(row).children('#userLogin').html();
 	var password =  $(row).find('input#password').val();
 	if(password != '-') parameters.password = password;
 	parameters.email = $(row).find('input#email').val();
 	parameters.alias = $(row).find('input#alias').val();
 	parameters.token_auth = piwik.token_auth;
	
	ajaxRequest.data = parameters;
	
	return ajaxRequest;
}

function getDeleteUserAJAX( login )
{
	var ajaxRequest = piwikHelper.getStandardAjaxConf('ajaxLoadingUsersManagement', 'ajaxErrorUsersManagement');
		
	var parameters = {};
	parameters.module = 'API';
	parameters.format = 'json';
 	parameters.method =  'UsersManager.deleteUser';
 	parameters.userLogin = login;
 	parameters.token_auth = piwik.token_auth;
	
	ajaxRequest.data = parameters;
	
	return ajaxRequest;
}

function getAddUserAJAX( row )
{
	var ajaxRequest = piwikHelper.getStandardAjaxConf('ajaxLoadingUsersManagement', 'ajaxErrorUsersManagement');
	
	var parameters = {};
	parameters.module = 'API';
	parameters.format = 'json';
 	parameters.method =  'UsersManager.addUser';
 	parameters.userLogin = $(row).find('input#useradd_login').val();
 	parameters.password =  $(row).find('input#useradd_password').val();
 	parameters.email = $(row).find('input#useradd_email').val();
 	parameters.alias = $(row).find('input#useradd_alias').val();
 	parameters.token_auth = piwik.token_auth;
 	
	ajaxRequest.data = parameters;
 	
	return ajaxRequest;
}

function getIdSites()
{
	return $('#selectIdsite option:selected').val();
}

function getUpdateUserAccess(login, access, successCallback)
{
	var ajaxRequest = piwikHelper.getStandardAjaxConf();
	
	ajaxRequest.success = successCallback;
	ajaxRequest.async = false;
	
	var parameters = {};
	parameters.module = 'API';
	parameters.format = 'json';
 	parameters.method =  'UsersManager.setUserAccess';
 	parameters.userLogin = login;
 	parameters.access = access;
 	parameters.idSites = getIdSites();
 	parameters.token_auth = piwik.token_auth;

	ajaxRequest.data = parameters;
 	
	return ajaxRequest;
}

function submitOnEnter(e)
{
	var key=e.keyCode || e.which;
	if (key==13)
	{
		$(this).find('.adduser').click();
		$(this).find('.updateuser').click();
	}
}

function launchAjaxRequest(self, successCallback)
{
	$.ajax( getUpdateUserAccess( 
					$(self).parent().parent().find('#login').html(),//if changed change also the modal
					$(self).parent().attr('id'),
					successCallback
				) 
			);	
}

function bindUpdateAccess()
{
	$('#accessUpdated').hide();
	var self = this;
	
	// callback called when the ajax request Update the user permissions is successful
	function successCallback (response)
	{
		piwikHelper.hideAjaxLoading();
		// if the permission couldn't be granted
		if(response.result == "error") 
		{
			piwikHelper.showAjaxError(response.message);
		}
		// if the permission change was successful
		else
		{
			piwikHelper.hideAjaxError();
			
			$(self).parent().parent().find('.accessGranted')
				.attr("src","plugins/UsersManager/images/no-access.png" )
				.attr("class","updateAccess" )
				.click(bindUpdateAccess)
				;
			$(self)
				.attr('src',"plugins/UsersManager/images/ok.png" )
				.attr('class',"accessGranted" )
				;
			$('#accessUpdated').show();
			$('#accessUpdated').fadeOut(1500);
		}
	}
	
	var idSite = getIdSites();
	if(idSite == 'all')
	{
		var target = this;       
		
		//ask confirmation
		var userLogin = $(this).parent().parent().find('#login').html();
		$('.dialog#confirm #login').text( userLogin ); // if changed here change also the launchAjaxRequest
		var question = $('.dialog#confirm');
		$('#yes', question).click(function()
		{
			launchAjaxRequest(target, successCallback);	
			$.unblockUI();
		});
		
		$('#no', question).click($.unblockUI);
		$.blockUI({message: question, css: { width: '300px' }});
	}
	else
	{
		launchAjaxRequest(this, successCallback);
	}
}

$(document).ready( function() {
	var alreadyEdited = new Array;
	// when click on edituser, the cells become editable
	$('.edituser')
		.click( function() {
			piwikHelper.hideAjaxError();
			var idRow = $(this).attr('id');
			if(alreadyEdited[idRow]==1) return;
			alreadyEdited[idRow] = 1;
			$('tr#'+idRow+' .editable').each(
				// make the fields editable
				// change the EDIT button to VALID button
				function (i,n) {
					var contentBefore = $(n).html();
					var idName = $(n).attr('id');
					if(idName != 'userLogin')
					{
						var contentAfter = '<input id="'+idName+'" value="'+contentBefore+'" size="25" />';
						$(n).html(contentAfter);
					}
				}
			);
					
			$(this)
				.toggle()
				.parent()
				.prepend( $('<img src="plugins/UsersManager/images/ok.png" class="updateuser" />')
				.click( function(){ $.ajax( getUpdateUserAJAX( $('tr#'+idRow) ) ); } ) 
			);
		});
		
	$('.editable').keypress( submitOnEnter );
	
	$('td.editable')
	 	.click( function(){ $(this).parent().find('.edituser').click(); } );
	
	// when click on deleteuser, the we ask for confirmation and then delete the user
	$('.deleteuser')
		.click( function() {
			piwikHelper.hideAjaxError();
			var idRow = $(this).attr('id');
			var loginToDelete = $(this).parent().parent().find('#userLogin').html();
			if( confirm(sprintf(_pk_translate('UsersManager_DeleteConfirm_js'),'"'+loginToDelete+'"')) )
			{
				$.ajax( getDeleteUserAJAX( loginToDelete ) );
			}
		}
	);
	
	$('.addrow').click( function() {
		piwikHelper.hideAjaxError();
		$(this).toggle();
		
		var numberOfRows = $('table#users')[0].rows.length;
		var newRowId = numberOfRows + 1;
		var newRowId = 'row' + newRowId;
	
		$(' <tr id="'+newRowId+'">\
				<td><input id="useradd_login" value="login?" size="10" /></td>\
				<td><input id="useradd_password" value="password" size="10" /></td>\
				<td><input id="useradd_email" value="email@domain.com" size="15" /></td>\
				<td><input id="useradd_alias" value="alias" size="15" /></td>\
				<td>-</td>\
				<td><img src="plugins/UsersManager/images/ok.png" class="adduser" /></td>\
	  			<td><img src="plugins/UsersManager/images/remove.png" class="cancel" /></td>\
	 		</tr>')
	  			.appendTo('#users')
		;
		$('#'+newRowId).keypress( submitOnEnter );
		$('.adduser').click( function(){ $.ajax( getAddUserAJAX($('tr#'+newRowId)) ); } );
		$('.cancel').click(function() { piwikHelper.hideAjaxError(); $(this).parents('tr').remove();  $('.addrow').toggle(); });
	});

	$('.updateAccess')
		.click( bindUpdateAccess );
	$('#accessUpdated').hide();
});	
