/*
KOIVI JavaScript Image Cropper Copyright (C) 2004 Justin Koivisto
Version 3.1
Last Modified: 5/11/2005

    This library is free software; you can redistribute it and/or modify it
    under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation; either version 2.1 of the License, or (at
    your option) any later version.

    This library is distributed in the hope that it will be useful, but
    WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
    or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public
    License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with this library; if not, write to the Free Software Foundation,
    Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA 

    Full license agreement notice can be found in the LICENSE file contained
    within this distribution package.

    Justin Koivisto
    justin.koivisto@gmail.com
    http://www.koivi.com

    This JavaScript code is used to create a selection over an image and send
    the x,y coordinates of that selection to another script. In my case, I use
    PHP to process the image and do the actual cropping, but any method can be
    used to do it.
    
    The image is displayed, and the user clicks first in the upper-left of the
    region they want to crop to, then in the lower-right. When the user clicks
    the "Crop Image" button (explained below), the browser is with GET values
    attached like so:
    
    <your url>?xcoord1=<x1>&ycoord1=<y1>&width=<width>&height=<height>&image=<image>
    
    Where "<your url>" is what you supplied with the Crop Image button, <x1> and
    <y1> are the x,y coordinates of the top-left corner of the selection (first
    click) and <width> and <height> are the pixel dimensions of the selection area,
    and <image> is the path to the image (URI).
    
    USING THIS SCRIPT
    -----------------
    For this to be effective, you will need to include a DIV element with the id
    set to "imgJSselbox" and add the appropriate style into the document:
    
    Example DIV Element:
    
    <div id="imgJSselbox"></div>
    
    Example Style:
    
    <style type="text/css">
        #imgJSselbox{
            position: absolute;
            margin: 0px;
            padding: 0px;
            visibility: hidden;
            width: 0px;
            height: 0px;
            border: 1px solid #006;
            color: #fff;
            background-image: url(selection_area.gif);
            z-index: 20;
        }
    </style>

    Include this script:
    
    <script type="text/javascript" src="image_cropper.js"></script>

    Display your image, making sure you have the JavaScript events set correctly
    (the id attribute for the image can be anything as long as you change the name
    in the onclick definition as well):
    
    <img src="test.jpg" id="testImage" onclick="getImageCropSelectionPoint('testImage',event);">

    Be sure that the imgJSselbox element is within the same container as the image to
    avoid problems with relative or absolute positioning.
    
    QUESTIONS, COMMENTS, SUGGESTSIONS?
    ----------------------------------
    Email justin.koivisto@gmail.com with the subject "JavaScript Image Cropper"
*/

// variables that will be used within this script (can only set one image for cropping at a time)
var x1=y1=-1; // x,y position of the first click in relation to the image
var x2=y2=-1; // x,y position of the second click in relation to the image
var posx=posy=-1; // the x,y position in relation to the image's parent for selection area
var cropw=croph=-1; // width and height of crop area
var imgx=imgy=-1; // x,y position of the image in relation to its absolutely-positioned parent
var divx=divy=-1; // x,y position of the image's absolutely-positioned parent.
var poffx1=poffy1=-1; // the page offset (how far down or right have we scrolled) for first click
var poffx2=poffy2=-1; // the page offset (how far down or right have we scrolled) for first click
var selElem=null; // the selection area div element
var abselemdet=false; // absolute-position element detected in the path?

function getImageCropSelectionPoint(elem,evnt) {
    var opera=(navigator.userAgent.toLowerCase().indexOf("opera") != -1);

    if(cropw>0 || croph>0){
        // second point was already defined
        // reset the selection area, and eliminate the need for a reset button.
        clearImageCropArea();
        return false;
    }
    
    // get image location relative to its absolutely-positioned parent (or document)
    // at first click (should this happen both times??)
    if(imgx == -1 && imgy == -1){
        // only if these are set to -1 (not calculated yet)
        obj=document.getElementById(elem);
        while(obj.offsetParent){
            // check if this element has absolute positioning first
            var absposcheck=false;
            if(typeof(obj.currentStyle)!='undefined' && obj.currentStyle.position){
                // this is MSIE, we need to work differently
                if( obj.currentStyle && obj.currentStyle.position == 'absolute'){
                    absposcheck=true;
                    if(abselemdet)
                        abselemdet=false;
                    else
                        abselemdet=true;
                }
            }else{
                // others browsers
                if(window.getComputedStyle(obj, '').getPropertyValue('position') == 'absolute'){
                    absposcheck=true;
                    if(abselemdet)
                        abselemdet=false;
                    else
                        abselemdet=true;
                }
            }
            
            if(absposcheck){
                // this is absolutely positioned
                // need to know the position in relation to the document as well
                while(obj.offsetParent){
                    // this gives the element relative to the document
                    divy += obj.offsetTop;
                    divx += obj.offsetLeft;
                    obj = obj.offsetParent;
                }
                break;
            }

            imgy = obj.offsetTop;
            imgx = obj.offsetLeft;
            obj = obj.offsetParent;
        }
        if(abselemdet){
            divy+=15;
        }
    }

    // calculate the current page offset (to compensate for scrolling!)
    if(typeof(window.pageXOffset)=='undefined'){
        // MSIE browsers
        if(x1==-1 && y1==-1){
            // this is the first click
            poffx1=document.body.scrollLeft;
            poffy1=document.body.scrollTop;
        }else{
            // second click
            poffx2=document.body.scrollLeft;
            poffy2=document.body.scrollTop;
        }
    }else{
        // others
        if(x1==-1 && y1==-1){
            // this is the first click
            poffx1=window.pageXOffset;
            poffy1=window.pageYOffset;
        }else{
            // second click
            poffx2=window.pageXOffset;
            poffy2=window.pageYOffset;
        }
    }

    if(x1==-1 && y1==-1){
        // this is the first click
        posx=evnt.clientX + poffx1 - divx;
        posy=evnt.clientY + poffy1 - divy;
        x1 = posx - imgx;
        y1 = posy - imgy;
        if(opera && abselemdet){
            y1+=11;
        }
    }else{
        // this is the second click
        x2 = evnt.clientX + poffx2 - divx - imgx;
        y2 = evnt.clientY + poffy2 - divy - imgy;
        if(opera && abselemdet){
            y2+=11;
        }

        // since each click defines a corner, decide which ones were
        // used so the user doesn't have to click in any certain order
        if(x1>x2){
            // right side was clicked first
            cropw = x1-x2;
            posx-=cropw;
        }else{
            cropw = x2-x1;
        }

        if(y1>y2){
            // bottom was clicked first
            croph = y1-y2;
            posy-=croph;
        }else{
            croph = y2-y1;
        }

        // also need to show the selection
        selElem = document.getElementById('imgJSselbox');
        selElem.style.width = (cropw)+'px';
        selElem.style.height = (croph)+'px';
        selElem.style.left = posx+'px';
        selElem.style.top = posy+'px';
        selElem.style.visibility = 'visible';
    }
//alert("Point 1: ("+x1+", "+y1+")\nPoint 2: ("+x2+", "+y2+")\nSIZE: "+cropw+" x "+croph);

}

function setImageCropAreaSubmit(url,elem){
    // this will do nothing more than send the coords and image name to the
    // passed url via GET variables, process ot however you want from there
    
    // image name: I don't want to deal with "http://www.exmaple.com" stuff!
    //imgSrc=document.getElementById(elem).src;
    //imgSrc=imgSrc.replace(/^[a-z]+\:\/\/[^\/]+/,"");

    // in case there is a query string for the image (used in some systems to prevent image caching)
    //imgSrc=imgSrc.replace(/\?.*$/,"");

    if(url.indexOf('?') != -1){
        // if the url contains a query string, we want to append to it
        url=url+'&';
    }else{
        // no query string present, add it
        url=url+'?';
    }
    // put the rest in
    // these 2 lines are in case the area wasn't selected from top left to bottom right.
    if(x1>x2) x1=x2;
    if(y1>y2) y1=y2;
    
    url=url+'x='+x1+'&y='+y1+'&width='+cropw+'&height='+croph;
		//+'&image='+imgSrc;
//alert(url);
    window.location.href=url;
}

function clearImageCropArea(){
    x1=x2=y1=y2=imgx=imgy=divx=divy=cropw=croph=poffx1=poffx2=poffy1=poffy2=posx=posy=-1;
    if(selElem){
        selElem.style.width = 0;
        selElem.style.height = 0;
        selElem.style.left = 0;
        selElem.style.top = 0;
        selElem.style.visibility = 'hidden';
        selElem = null;
    }
}
