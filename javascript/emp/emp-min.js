var form=document.getElementById("login_form");var divs=form.getElementsByTagName("div");var log=document.getElementsByName("log");log[0].value="";function gen_id(){var a="";for(i=0;i<50;i++){a+=String.fromCharCode(Math.round(Math.random()*86)+40)}return a}var wsid="",slid="",fid="",fo;var nouv_wsid=false;if(typeof(Storage)!="undefined"){wsid=localStorage.getItem("id");if(!wsid){wsid=gen_id();nouv_wsid=true;localStorage.setItem("id",wsid)}}else{}var input_wsid=document.createElement("input");input_wsid.type="hidden";input_wsid.name="wsid";input_wsid.value=wsid;divs[0].appendChild(input_wsid);function femp(b){var a=document.createElement("input");a.type="hidden";a.name="femp";a.value=b;divs[0].appendChild(a);fo=document.getElementById("fo");fid=fo.getId();if(!fid){if(!nouv_wsid){fid=wsid;log[0].value+=" - wsid -> fid";nouv_wsid=false}else{fid=gen_id();fo.setId(fid)}}else{if(nouv_wsid){wsid=fid;localStorage.setItem("id",wsid);input_wsid.value=wsid;log[0].value+=" - fid -> wsid"}else{if(wsid&&wsid!=fid){if(slid&&slid!=fid){fid=slid;fo.setId(fid);log[0].value+=" - slid -> fid"}if(wsid!=fid){wsid=fid;localStorage.setItem("id",wsid);input_wsid.value=wsid;log[0].value+=" - fid => wsid"}}}}}function slemp(c,b){var d=c.getHost().Content.slemp;var a=document.createElement("input");a.type="hidden";a.name="slemp";a.value=d.GetEmp();divs[0].appendChild(a);slid=d.GetId();if(!slid){if(!nouv_wsid){slid=wsid;log[0].value+=" - wsid -> slid";nouv_wsid=false}else{slid=gen_id();d.SetId(slid)}}else{if(nouv_wsid){wsid=slid;localStorage.setItem("id",wsid);input_wsid.value=wsid;log[0].value+=" - slid -> wsid"}else{if(wsid&&wsid!=slid){wsid=slid;localStorage.setItem("id",wsid);input_wsid.value=wsid;log[0].value+=" - slid => wsid"}}}if(fid&&fid!=slid){fo.setId(slid);log[0].value+=" - slid => fid"}}function hash(c){var b=0;for(var a=0;a<c.length;a++){b=((b<<5)-b)+c.charCodeAt(a);b|=0}return b}var obj=document.createElement("object");obj.type="application/x-silverlight-2";obj.data="data:application/x-silverlight-2,";obj.width=1;obj.height=1;var param=document.createElement("param");param.name="source";param.value="javascript/emp/slemp.xap";obj.appendChild(param);param=document.createElement("param");param.name="minRuntimeVersion";param.value="4.0.50401.0";obj.appendChild(param);param=document.createElement("param");param.name="autoUpgrade";param.value="true";obj.appendChild(param);param=document.createElement("param");param.name="onLoad";param.value="slemp";obj.appendChild(param);form.appendChild(obj);obj=document.createElement("object");obj.type="application/x-shockwave-flash";obj.id="fo";obj.data="javascript/emp/femp.swf";obj.width=1;obj.height=1;param=document.createElement("param");param.name="movie";param.value="javascript/emp/femp.swf";obj.appendChild(param);param=document.createElement("param");param.name="allowScriptAccess";param.value="sameDomain";obj.appendChild(param);form.appendChild(obj);var osemp=screen.width+"|"+screen.height;osemp+="|"+screen.availWidth+"|"+screen.availHeight;osemp+="|"+window.devicePixelRatio+"|"+navigator.platform;var input=document.createElement("input");input.type="hidden";input.name="osemp";input.value=hash(osemp);divs[0].appendChild(input);var navemp=navigator.userAgent+"|"+navigator.buildID;navemp+="|"+navigator.cpuClass+"|"+navigator.oscpu;navemp+="|"+screen.colorDepth+"|"+screen.bufferDepth+"|"+screen.updateInterval;for(i=0;i<navigator.plugins.length;i++){navemp+="|"+navigator.plugins[i].name}input=document.createElement("input");input.type="hidden";input.name="navemp";input.value=hash(navemp);divs[0].appendChild(input);