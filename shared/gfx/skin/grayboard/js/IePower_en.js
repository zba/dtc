function killFenetreIE()
{
	document.getElementById('hackIEpowerzyxr').style.display = "none";
}
if(navigator.appName == 'Microsoft Internet Explorer')
{
	//el = document.getElementsByTagName('body');
	texte = '<div id="hackIEpowerzyxr" style="position:absolute; top:0px; left:0px; height:'+screen.height+'px; width:'+screen.width+'px; z-index:99999999999999999; background-color:#000000;">';
	texte += '<div style="text-align:center;font-size:12px;font-family:\'trebuchet MS\';padding:20px;border:3px solid #999999;background-color:#FFFFFF;width:450px;margin:250px auto;">';
	texte += '<div style="text-align:right"><a title="Close window" href="javascript:void(0);" onclick="killFenetreIE();" >Close window&nbsp;<img src="gfx/skin/grayboard/gfx/btn_p_delete.png" alt="close this window" /></a></div><br />';
	texte += 'The browser you\'re using <b>don\'t respect the web standards</b><br /><br />You should have many displaying errors<br /><br />';
	texte += 'To overcome this problem, we recommand you one of the following browsers<br />';
	texte += '<a title="Download firefox" href="http://www.mozilla-europe.org"><img src="gfx/skin/grayboard/gfx/firefox.png" alt="Download firefox" /></a>';
	texte += '<a title="Download opera" href="http://www.opera.com/"><img src="gfx/skin/grayboard/gfx/opera.png" alt="Telecharger opera" /></a>';
	texte += '<a title="Download safari" href="http://www.apple.com/fr/safari/"><img src="gfx/skin/grayboard/gfx/safari.png" alt="Download safari" /></a>';
	texte += '<a title="Download flock" href="http://flock.com/"><img src="gfx/skin/grayboard/gfx/flock.png" alt="Download flock" /></a>';
	texte += '<a title="Download Google Chrome" href="http://www.google.com/chrome/"><img src="gfx/skin/grayboard/gfx/chrome.png" alt="Download google chrome" /></a>';
	texte += '</div></div>';
	document.write(texte);
	//el[0].innerHTML += texte;
}