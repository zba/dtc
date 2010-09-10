function killFenetreIE()
{
	document.getElementById('hackIEpowerzyxr').style.display = "none";
}
if(navigator.appName == 'Microsoft Internet Explorer')
{
	//el = document.getElementsByTagName('body');
	texte = '<div id="hackIEpowerzyxr" style="position:absolute;top:0px;left:0px;height:'+screen.height+'px;width:'+screen.width+'px;z-index:99999999999999999;	background-color:#000000;">';
	texte += '<div style="text-align:center;font-size:12px;font-family:\'trebuchet MS\';padding:20px;border:3px solid #999999;background-color:#FFFFFF;width:450px;margin:250px auto;">';
	texte += '<div style="text-align:right"><a title="fermer cette fenetre" href="javascript:void(0);" onclick="killFenetreIE();" >fermer cette fenetre&nbsp;<img src="gfx/skin/grayboard/gfx/btn_p_delete.png" alt="fermer cette fenetre" /></a></div><br />';
	texte += 'Le navigateur que vous utilisez actuellement<br /><b>ne respecte pas les standards</b> du web<br /><br />Vous risquez de rencontrer de nombreux problemes d\'affichage<br /><br />';
	texte += 'Pour palier &agrave; ce probl&egrave;me, nous vous recommandons<br />l\'un des navigateurs suivants:<br />';
	texte += '<a title="Telecharger firefox" href="http://www.mozilla-europe.org"><img src="gfx/skin/grayboard/gfx/firefox.png" alt="Telecharger firefox" /></a>';
	texte += '<a title="Telecharger opera" href="http://www.opera.com/"><img src="gfx/skin/grayboard/gfx/opera.png" alt="Telecharger opera" /></a>';
	texte += '<a title="Telecharger safari" href="http://www.apple.com/fr/safari/"><img src="gfx/skin/grayboard/gfx/safari.png" alt="Telecharger safari" /></a>';
	texte += '<a title="Telecharger flock" href="http://flock.com/"><img src="gfx/skin/grayboard/gfx/flock.png" alt="Telecharger flock" /></a>';
	texte += '<a title="Telecharger Google Chrome" href="http://www.google.com/chrome/"><img src="gfx/skin/grayboard/gfx/chrome.png" alt="Telecharger google chrome" /></a>';
	texte += '</div></div>';
	document.write(texte);
	//el[0].innerHTML += texte;
}