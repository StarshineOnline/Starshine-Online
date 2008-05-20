<!-- drag&drop d'objets

function selObj(e){ // sélection de l'objet qui a généré l'évènement
el=ie?event.srcElement:e.target;
if(!el.tagName)el=el.parentNode // pour NS6+
}

function drag(e){
selObj(e);
if (el.id.substr(0,2)=="dd" && ob==D){  // si l'objet est déplaçable
  ob=el;el=D; // le capture
  }
return false 
}

function bouge(e){ // suit la souris
sx=gk?pageXOffset:db.scrollLeft;     //scroll h
sy=gk?pageYOffset:db.scrollTop;      //scroll v
px=gk?e.pageX:event.clientX+sx;      //curseur x
py=gk?e.pageY:event.clientY+sy;      //curseur y
if(ob != D){ // décroche l'objet et l'accroche à la souris
  with(ob.style){position="absolute";display="inline";
    left=px+"px";top=py+3+"px";cursor="move"
    }
  }
return false 
}

function drop(e){ // dépose l'objet
selObj(e);//
verif(); 
if(el.parentNode.id.substr(0,3) == "rcp"){  // si c'est bien une case réponse
  tag = el.parentNode.id.substr(3,6); // tag des réponses
  if(el.tagName == tag){
    with(ob.style){ left=px-15+"px";top=py-10+"px";// repositionne la réponse
      }    
    }
  }
    ob=D; // annule la sélection
}

function verif(){ // vérifie l'emplacement des objets et affiche le score
bien_place[ob.id.substr(2,1)]=(ob.id.substr(1,2) == el.id)?1:0;
score=0
for(var i=0;i<bien_place.length;i++){if(bien_place[i])score += bien_place[i]}
s=(score>1)?'s':''; //singulier ou pluriel
D.getElementById('resultat').innerHTML = score +' bonne'+s+' réponse'+s;
}

function initdrag(){
db=!D.documentElement.clientWidth?D.body:D.documentElement //quirk IE6
addEvent(D,"mousemove",bouge);//addEvent(D,"mousedown",drag); addEvent(D,"mouseup",drop);
D.onmousedown=drag;D.onmouseup=drop;
bien_place=[]//Tableau des objets bien placés
score=0;
}

function addEvent(obj,evType,fn,capt){ // ajoute un événement sans écraser l'éxistant.
if(obj.addEventListener){obj.addEventListener(evType,fn,capt);return true;} // NS6+
else if(obj.attachEvent)obj.attachEvent("on"+evType,fn) // IE 5+
else {return false;}
} 

D=document;ob=D;gk=window.Event?1:0/*Gecko*/;
addEvent(window,"load",initdrag);

//-->