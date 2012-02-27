/*
+---------------------------------------------------------------------------------------+
|							Asynchronous JavaScript Loading								|
+---------------------------------------------------------------------------------------+
*/
window.___gcfg = {lang: 'fr'};
(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://apis.google.com/js/plusone.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();

/*
+---------------------------------------------------------------------------------------+
|				La fonction pour afficher le compteur de déplacement.					|
+---------------------------------------------------------------------------------------+
*/
var check = false;
function CountDown(time){
	if(time>0){
		if(time>=1){
			document.getElementById("TimeToWait").innerHTML = ArrangeDate(time);
			document.title = "Gaulix - " + ArrangeDate(time);
			btime = time-1;
			if (btime==0){check=true;}
		}
		setTimeout("CountDown(btime)", 1000);
	}else if(check){window.location="index.php?page=common&action=deplacement";}
}
/*
+---------------------------------------------------------------------------------------+
|				La fonction pour afficher le compteur pour la ressource.				|
+---------------------------------------------------------------------------------------+
*/
var checkRessource = false;
function CountDownRessource(timeRessource){
	if(timeRessource>0){
		if(timeRessource>=1){
			document.getElementById("TimeToWaitRessource").innerHTML = ArrangeDate(timeRessource);
			btimeRessource = timeRessource-1;
			if (btimeRessource==0){checkRessource=true;}
		}
		setTimeout("CountDownRessource(btimeRessource)", 1000);
	}else if(checkRessource){window.location="index.php?page=common&action=ressource";}
}
/*
+---------------------------------------------------------------------------------------+
|				Fonction pour afficher un temp en jour-heure-minutes-secondes.			|
+---------------------------------------------------------------------------------------+
ArrangeDate("un temp en seconde") exemple 3600 =  1 heure
*/
function ArrangeDate(heure){
	if(heure>=0 && heure<=59){
		// Seconds
		shifttime = heure+" seconds";
	}else if(heure>=60 && heure<=3599) {
		// Minutes + Seconds
		pmin = heure / 60;
		premin = Math.floor(pmin);
		presec = pmin-premin;
		sec = presec*60;
		shifttime = premin+" min "+Math.round(sec)+" sec";
	}else if(heure>=3600 && heure<=86399) {
		// Hours + Minutes 4253
		phour = heure / 3600;
		prehour = Math.floor(phour);
		premin = (phour-prehour)*60;
		min = Math.floor(premin);
		presec = premin-min;
		sec = presec*60;
		shifttime = prehour+" hrs "+min+" min "+Math.round(sec)+" sec";
	}else if(heure>=86400) {
		// Days + Hours + Minutes
		pday = heure / 86400;
		preday = Math.floor(pday);
		phour = (pday-preday)*24;
		prehour = Math.floor(phour);
		premin = (phour-prehour)*60;
		min = Math.floor(premin);
		presec = premin-min;
		sec = presec*60;
		shifttime = preday+" days "+prehour+" hrs "+min+" min "+Math.round(sec)+" sec";
	}
	return (shifttime);
}
/*
+---------------------------------------------------------------------------------------+
|						Fonction pour afficher le module FaceBook						|
+---------------------------------------------------------------------------------------+
*/
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
/*
+---------------------------------------------------------------------------------------+
|						Fonction pour Google Analytic						|
+---------------------------------------------------------------------------------------+
*/
var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-25828929-1']);
	_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
