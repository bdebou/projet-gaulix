function GetId(id){	return document.getElementById(id);}

var chkBulleVisible=false; // La variable i nous dit si la bulle est visible ou non
			
function move(e) {
	if(chkBulleVisible){  // Si la bulle est visible, on calcul en temps reel sa position ideale
		if(navigator.appName!="Microsoft Internet Explorer"){ // Si on est pas sous IE
			GetId("curseur").style.left=e.pageX + 5+"px";
			GetId("curseur").style.top=e.pageY + 10+"px";
		}else{ // Modif proposé par TeDeum, merci à lui
			if(document.documentElement.clientWidth>0) {
				GetId("curseur").style.left=20+event.x+document.documentElement.scrollLeft+"px";
				GetId("curseur").style.top=10+event.y+document.documentElement.scrollTop+"px";
			}else{
				GetId("curseur").style.left=20+event.x+document.body.scrollLeft+"px";
				GetId("curseur").style.top=10+event.y+document.body.scrollTop+"px";
			}
		}
	}
}
function montre(text) {
	if(chkBulleVisible==false) {
		GetId("curseur").style.visibility="visible"; // Si il est cacher (la verif n'est qu'une securité) on le rend visible.
		GetId("curseur").innerHTML = text; // Cette fonction est a améliorer, il parait qu'elle n'est pas valide (mais elle marche)
		chkBulleVisible=true;
	}
}
function cache() {
	if(chkBulleVisible==true) {
		GetId("curseur").style.visibility="hidden"; // Si la bulle etais visible on la cache
		chkBulleVisible=false;
	}
}
document.onmousemove=move; // des que la souris bouge, on appelle la fonction move pour mettre a jour la position de la bulle.