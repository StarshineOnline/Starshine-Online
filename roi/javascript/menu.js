function addEvent(obj, type, fn) {
	if( obj.attachEvent ) {
			obj["e"+type+fn] = fn;
			obj[type+fn] = function(){obj["e"+type+fn]( window.event );};
			obj.attachEvent( "on"+type, obj[type+fn] );
	} else {
			obj.addEventListener( type, fn, true );
	};
}

function initMenus() {
	/*Activate/deactivate menus*/
	$$('.smenu').each(function(s,index){
		addEvent(s,'click',function(){
			if($('smenu'+s.id.replace('a','')).visible()){
				//do nothing
			} else {
				$$('.smenu').each(function(s,index){
					if($('smenu'+s.id.replace('a','')).visible()) {
						//$('smenu'+s.id).hide(); //Prototype Method
						Effect.BlindUp($('smenu'+s.id.replace('a','')), {duration:0.5}); //ScriptAculoUs Method
					}
				});
				//$('smenu'+s.id).show(); //Prototype Method
				Effect.BlindDown($('smenu'+s.id.replace('a','')), {duration:0.5}); //ScriptAculoUs Method
			}
		});
	});
	
	/*All menu hiden by default*/
	$$('.smenu').each(function(s,index){
		if($('smenu'+s.id.replace('a','')).visible()) {
			$('smenu'+s.id.replace('a','')).hide();
		}
	});
	
	/*effects on menu*/
	$$('dl#menu dt').each(function(s,index){
		addEvent(s,'mouseout',function(){
			new Effect.Highlight(s, {duration:0.5, startcolor:'#EFF3FF', endcolor:'#72ACDC', restorecolor
:'#72ACDC'});
		});
	});
}

addEvent(window, 'load', initMenus);
