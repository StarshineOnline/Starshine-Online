 // JavaScript Document
 <!-- Script écrit par Altan. Visitez son site! -->
 <!-- http://www.altan.hr/snow -->
  
 <!-- Script utiliser à titre d'exemple -->
 <!-- http://www.espace-webmaster.com -->
  
 <!-- Script modifié pour Firefox 2 et IE 7 -->

 <!-- Begin
 var no = 10; // nombre de flocon
 var speed = 40; // Vitesse ou tombe les flocons
 var snowflake1 = "http://monsite/images/flocon1.gif"; // Nom de votre image
 var snowflake2 = "http://monsite/images/flocon2.gif"; // Nom de votre image
 var snowflake3 = "http://monsite/images/flocon3.gif"; // Nom de votre image
 var nb=3;
  
 var ns4up = (document.layers) ? 1 : 0; // Version de netscape
 var ie4up = (document.all) ? 1 : 0; // Version d' Internet Exploreur
 var dx, xp, yp; // Coordonnées de positionnement
 var am, stx, sty; // amplitude and step variables
 var i, doc_width = 1024, doc_height = 768;
 if (ns4up)
   {
   doc_width = self.innerWidth;
   doc_height = self.innerHeight;
   }
 else if (ie4up)
   {
   doc_width = document.body.clientWidth;
   doc_height = document.body.clientHeight;
   }
   
 dx = new Array();
 xp = new Array();
 yp = new Array();
 am = new Array();
 stx = new Array();
 sty = new Array();
 var i=0;
  
 for (i = 0; i < no; ++ i)
   {
   dx[i] = Math.floor(Math.random() * 1100)+20; // définition du coordonné
   if(dx[i]>doc_width-30) dx[i] = dx[i]-50;
   xp[i] = 5; // définition de la position
   am[i] = Math.random()*1;
   yp[i] = Math.random()*200-200;
   stx[i] = 0.02 + Math.random()/10; // set step variables
   sty[i] = 0.7 + Math.random(); // set step variables
  
   if (i == 0)
     {
     document.write("<div id=\"dot"+ i +"\" style=\"POSITION: ");
 document.write("absolute;opacity:1;filter:alpha(opacity=100); Z-INDEX: 50; VISIBILITY: ");
 document.write("visible; TOP: 5px; LEFT: 15px;\"><img style='width:54px;' src=\"");
 var typefloc = Math.floor(Math.random() * nb)+1;
 if(typefloc==1) document.write(snowflake1 + "\" border=\"0\"></div>");
     if(typefloc==2) document.write(snowflake2 + "\" border=\"0\"></div>");
 if(typefloc==3) document.write(snowflake3 + "\" border=\"0\"></div>");
     //if(typefloc==4) document.write(snowflake4 + "\" border=\"0\"></div>");
 }
 else
 {
 document.write("<div id=\"dot"+ i +"\" style=\"POSITION: ");
     document.write("absolute;opacity:1;filter:alpha(opacity=100); Z-INDEX: 50; VISIBILITY: ");
     document.write("visible; TOP: 5px; LEFT: 15px;\"><img style='width:24px;' src=\"");
     var typefloc = Math.floor(Math.random() * nb)+1;
     if(typefloc==1) document.write(snowflake1 + "\" border=\"0\"></div>");
 if(typefloc==2) document.write(snowflake2 + "\" border=\"0\"></div>");
     if(typefloc==3) document.write(snowflake3 + "\" border=\"0\"></div>");
 //if(typefloc==4) document.write(snowflake4 + "\" border=\"0\"></div>");
     }
   }
  
 function snowIE()
   { // Définition de l'animation pour Internet Exploreur
   doc_width = document.body.clientWidth;
   doc_height = document.body.clientHeight;
   for (i = 0; i < no; ++ i)
     {
     //deplacement vertical
     sty[i] = 0.2 + Math.random()*3;
     yp[i] += sty[i];
    
     //deplacement horizontal
     stx[i] = 0.08 + Math.random()/10;
     dx[i] += stx[i] + am[i]*Math.sin(dx[i]);
     
     test=Math.floor(Math.random()*1000);
     
     //tant que dans la page
  
     if (yp[i] < 600 && test>1)
       {
       document.getElementById("dot"+i).style.top = Math.floor(yp[i])+"px";
       if (dx[i] < doc_width-5) document.getElementById("dot"+i).style.left = dx[i]+"px";
       }
     else //sinon on le remet en haut
       {
       yp[i]=0;
       dx[i] = 50 + Math.floor(Math.random() * 800)+5;
       }
     
     }
   setTimeout("snowIE()", speed);
   }
   
 snowIE();
