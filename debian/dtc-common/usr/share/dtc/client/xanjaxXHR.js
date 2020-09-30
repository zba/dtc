/*	XANJAX Copyright 2007,2008, and Trademark, of David Chapman, openPC Labs.

	XANJAX IS FREE SOFTWARE usually under GNU Affero General Public License Version 3.
	Read licence.txt distributed with XANJAX, or xanjax.org/license.html for details.
	IF YOU USE OR DISTRIBUTE OR CONVEY XANJAX YOU ARE LEGALLY BOUND BY ITS LICENCE.
	
	THESE are (modified) library-components of XANJAX released under the GNU Lesser
	General Public License (LGPL) version 2.1, by special arrangment with the copyright
	owner David Chapman.

	YOU MUST Comply with Copyright and Trademark Rights. DO NOT REMOVE THIS NOTICE */

var ajaxPath="logPushlet.php?";
var ajaxAuth="";
var abortXHR=new Array();
window.onbeforeunload=stopXHR; // FIXME check if this conflicts with your stuff
// but you need it to prevent script errors if user navigates away

function stopXHR(){
 for(var index in abortXHR){
	try {abortXHR[index]();}
	catch(e){};
 }
}

function objfyJSON(JSONtext){
 var jsonObj=new Function("return({'objSet':"+JSONtext+"});");
 return jsonObj().objSet;
}

function logPushlet(url,JSONtext,xml){
 var list=objfyJSON(JSONtext);
 if (list.callSID)
	xanGet(logPushlet,ajaxPath+ajaxAuth+list.callSID);
var data = list.lastlog.replace(/\n\n/g,"\n\r").replace(/&/g,"&amp;").replace(/>/g,"&gt;").replace(/</g,"&lt;");
 document.getElementById('reinstall_os_log').lastChild.data=data;
} // FIXME can remove "replace()" if no double linefeeds

function xanjaxHttpRequest(reqHandler){
 var xanjax=null; var aborted = false;
 var inner=this;
 this.abort=function(){
	aborted=true;
	xanjax.abort();
	xanjax=null;
 }
 this.init=initRequest();
 this.handler=reqHandler || function(){};
 this.request=function (mode, url, query, data){
	if (this.init){
	 xanjax.onreadystatechange=function(){
		switch(xanjax.readyState) {
		 case 1:break; case 2:break; case 3:break;
		 case 4:if(aborted)break;
		else inner.handler(url,xanjax.status,xanjax.responseText,xanjax.responseXML); 		
		}
	 };
	 xanjax.open(mode, url + query, true);
	 if (mode=="POST"){
	  xanjax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	  xanjax.setRequestHeader("Content-Length", data.length);
	 }
	xanjax.send(data);
	} else alert("Error: Cannot create Http Request Object!");
 };

	function initRequest(){
	 switch(true){
		case window.XMLHttpRequest && (xanjax=new XMLHttpRequest()) && true:
		 return 1;
		case window.ActiveXObject && true:
		 var objVer=new Array(".6.0",".3.0",".4.0",".5.0",null);
			for (var i=0;i<=objVer.length;i++)
			 try{
				xanjax=new ActiveXObject("MSXML2.XMLHTTP" + objVer[i]);
				if(i==objVer.length)xanjax = new ActiveXObject("Microsoft.XMLHTTP");
				if(xanjax)return(i + 2);
				}catch(e){if(i==objVer.length)return 0;};
	 }
	}
}

function xanGet(handler,url){
 var xanReq=new xanjaxHttpRequest();
 abortXHR.push(xanReq.abort);
 xanReq.handler=function(url,status,text,xml){
  if(status==200)
	 handler(url,text,xml);
  else alert("XMLHttpRequest error "+status+" getting "+url);
 };
 xanReq.request('GET',url,"","");
}

