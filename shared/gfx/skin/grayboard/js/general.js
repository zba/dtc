jQuery.preloadImages = function()
{
	for(var i = 0; i<arguments.length; i++)
	jQuery("<img>").attr("src", arguments[i]);
}
jQuery.preloadImages("../images/g_admin.png", "../images/client.png", "../images/domain.png", "../images/g_admino.png", "../images/cliento.png", "../images/domaino.png");

jQuery(document).ready(function(){
	
	$("#iconbar li a").hover(
		function(){
			var iconName = $(this).children("img").attr("src");
			var origen = iconName.split(".png")[0];
			$(this).children("img").attr({src: "" + origen + "o.png"});
			$(this).css("cursor", "pointer");
			$(this).animate({ width: "220px" }, {queue:false, duration:"normal"} );
			$(this).children("span").animate({opacity: "show"}, "fast");
		}, 
		function(){
			var iconName = $(this).children("img").attr("src");
			var origen = iconName.split("o.")[0];
			$(this).children("img").attr({src: "" + origen + ".png"});			
			$(this).animate({ width: "24px" }, {queue:false, duration:"normal"} );
			$(this).children("span").animate({opacity: "hide"}, "fast");
		});
});

$(function(){
	$("[title]").mbTooltip({ // also $([domElement]).mbTooltip  >>  in this case only children element are involved
		opacity : .55,       //opacity
		wait:250,           //before show
		cssClass:"default",  // default = default
		timePerWord:70,      //time to show in milliseconds per word
		hasArrow:false,			// if you whant a little arrow on the corner
		hasShadow:true,
		imgPath:"../images/",
		ancor:"parent", //"parent"  you can ancor the tooltip to the mouse position or at the bottom of the element
		shadowColor:"white", //the color of the shadow
		mb_fade:200 //the time to fade-in
	});
});
