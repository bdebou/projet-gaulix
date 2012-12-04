<?php
header ("Content-type: image/png");

/*
Erreur01:
	Il n'y a aucun GET value
Erreur02:
	Aucun $_GET['type'] correspondant
Erreur03:
	1 des 2 variables de STATUS n'est pas transmises
*/


$arCouleurs			= array('Attaque'		=> 'FF0000',					// Red 
							'Defense'		=> '32CD32',					// LimeGreen
							'Nourriture'	=> '808000',					// Olive
							'Pierre'		=> '708090',					// SlateGray
							'Bois'			=> '8B4513',					// SaddleBrown
							'Hydromel'		=> 'F0E68C',					// Khaki
							'Vie'			=> 'FFA07A',					// LightSalmon
							'Experience'	=> '6495ED',					// CornflowerBlue
							'Or'			=> 'FFD700');					// Gold
						
if(!isset($_GET['type'])){ImageErreur('Erreur01');}
else{
	switch($_GET['type']){
		case 'statusetat':
			if(isset($_GET['max']) AND isset($_GET['value'])){ImageStatus($_GET['max'], $_GET['value'], 202, 22);}
			else{ImageErreur('Erreur03');}
			break;
		case 'etatcarte':
			if(isset($_GET['max']) AND isset($_GET['value'])){ImageStatus($_GET['max'], $_GET['value'], 102, 12);}
			else{ImageErreur('Erreur03');}
			break;
		case 'vie':
		case 'experience':
			if(isset($_GET['max']) AND isset($_GET['value']) AND isset($_GET['type'])){
				$tailleX = 20;
				$tailleY = 120;
				if(isset($_GET['taille'])){
					$arTemp = explode('x', $_GET['taille']);
					$tailleX = $arTemp[0];
					$tailleY = $arTemp[1];
				}
				ImageProgression($_GET['type'], $_GET['max'], $_GET['value'], $tailleX, $tailleY);
			}else{
				ImageErreur('Erreur03');
			}
			break;
		case 'VieCarte':
			if(isset($_GET['max']) AND isset($_GET['value'])){
				ImageProgression('vie', $_GET['max'], $_GET['value'], 70, 14);
			}else{
				ImageErreur('Erreur03');
			}
			break;
		default:
			ImageErreur('Erreur02');
	}
}

//
//---------------------------------------------------	FUNCTIONS	-----------------------------
//
function ImageProgression($type, $max, $value, $SizeX, $SizeY){
	global $arCouleurs;
	$image		= imagecreate($SizeX, $SizeY);
	
	$icone		= imagecreatefrompng("../img/icones/ic_".strtolower($type).".png");
	$HauteurMini    = $SizeY - 4;
	$LargeurMini    = intval((ImagesX($icone) / ImagesY($icone)) * $HauteurMini);
	$icone_mini	= imagecreate($LargeurMini, $HauteurMini);
	//fond
	$blanc = imagecolorallocate($image, 255, 255, 255);
	$blanc = imagecolorallocate($icone_mini, 255, 255, 255);
	//on remplace le blanc de icone_mini par du transparent
	imagecolortransparent($icone_mini, $blanc);
	
	//création de la couleur
	$arExplodeCouleur = str_split($arCouleurs[ucfirst(strtolower($type))], 2);
	foreach($arExplodeCouleur as $col){$colorRVB[] = base_convert($col, 16, 10);}
	
	$couleur	= imagecolorallocate($image, $colorRVB['0'], $colorRVB['1'], $colorRVB['2']);
	$noir		= imagecolorallocate($image, 0, 0, 0);
	
	//on dessine le pourtour
	ImageRectangle ($image, 0, 0, $SizeX-1, $SizeY-1, $noir);
	
	//on rempli
	ImageFilledRectangle ($image, 1, 1, intval(($value / $max) * ($SizeX - 2)), ($SizeY - 2), $couleur);
	
	// On reduit l'icone
	imagecopyresampled($icone_mini, $icone, 0, 0, 0, 0, ImagesX($icone_mini), ImagesY($icone_mini), ImagesX($icone), ImagesY($icone));

	// On met le logo (source) dans l'image de destination (la photo)
	imagecopymerge($image, $icone_mini, 4, 2, 0, 0, ImagesX($icone_mini), ImagesY($icone_mini), 100);
	//Ecrire du texte
	imagestring($image, ($SizeY >= 20?4:1), ($LargeurMini + ($SizeY >= 20?14:7)), intval((ImagesY($image) - ($SizeY >= 20?16:8)) / 2), $value.'/'.$max, $noir);
	return imagepng($image);
}
function ImageStatus($max, $value, $SizeX, $SizeY){
	$image = imagecreate($SizeX, $SizeY);

	//fond
	$blanc = imagecolorallocate($image, 255, 255, 255);

	//création des couleurs
	$orange	= imagecolorallocate($image, 255, 128, 0);
	$vert	= imagecolorallocate($image, 0, 255, 0);
	$noir	= imagecolorallocate($image, 0, 0, 0);
	$rouge	= imagecolorallocate($image, 255, 0, 0);

	//on dessine le pourtour
	ImageRectangle ($image, 0, 0, $SizeX-1, $SizeY-1, $noir);

	//on sélectionne la couleur de remplissage
	$pourcentage = intval(($value / $max) * 100);
	if($pourcentage <= 25){$couleur = $rouge;}
	elseif($pourcentage <= 60){$couleur = $orange;}
	else{$couleur = $vert;}

	//on rempli
	ImageFilledRectangle ($image, 1, 1, intval(($value / $max) * ($SizeX - 2)), ($SizeY - 2), $couleur);

	//Ecrire du texte
	imagestring($image, ($SizeY >= 20?3:1), ($SizeX / 4), ($SizeY >= 20?4:2), $value.'/'.$max, $noir);
	return imagepng($image);
}

function ImageErreur($text){
	$image = imagecreate(100, 50);
	
	//fond
	$rouge = imagecolorallocate($image, 255, 0, 0);
	
	//Couleurs
	$jaune = imagecolorallocate($image, 255, 255, 0);
	
	ImageSetThickness ($image, 3);
	
	imagestring($image, 4, 15, 15, $text, $jaune);
	
	return imagepng($image);
}
?>