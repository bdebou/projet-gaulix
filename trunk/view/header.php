<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<link href="https://plus.google.com/u/0/b/116898576928846446900/116898576928846446900/posts" rel="publisher" />
	<link href="http://fonts.googleapis.com/css?family=MedievalSharp&key=AIzaSyCvuVdTFkYGeKuHA5xljD_fGU5khD6lejM" rel="stylesheet" type="text/css">
	
	<meta name="keywords" content="gaulix" />
	<meta name="description" content="Gaulix est un Web Game totalement gratuit. Vous incarnez un personnage gaulois. Il doit construire sa maison et peut construire aussi d\'autre batiment. Mais pour cela il lui faut des ressources que vous devrez collecter, apprendre des compétences pour fabriquer des armes, ... Bonne Chance!!!" />
	
	<title>Gaulix</title>
	
	<script type="text/javascript" src="./fct/js_main.js"></script>
	<script type="text/javascript" src="./fct/js_infobulle.js"></script>
	
	<link rel="icon" href="./img/icone.png" type="image/x-icon" />
	
	<link rel="shortcut icon" href="./img/icone.png" type="image/x-icon" />
	
	<link rel="stylesheet" href="./css/styles.css" type="text/css" />
	
	<meta property="og:title" content="Gaulix" />
	<meta property="og:type" content="game" />
	<meta property="og:url" content="http://www.gaulix.be" />
	<meta property="og:image" content="http://www.gaulix.be/img/logo.png" />
	<meta property="og:site_name" content="Gaulix" />
	<meta property="fb:admins" content="100002431126216" />
<?php 
if (!empty($_GET['page']) && $_GET['page'] == 'competences'){
?>
	<script type="text/javascript">
		//<!--
		var arOnglets = new Array;
		function change_onglet(NewName, NewNiveau){
			for (var i=0; i<arOnglets.length; i++){
				if(arOnglets[i][0] == NewName){
					for(var j=1; j<arOnglets[i].length; j++){
						document.getElementById('onglet_'+NewName+'_'+arOnglets[i][j]).className = 'onglet_0 onglet';
						document.getElementById('contenu_onglet_'+NewName+'_'+arOnglets[i][j]).style.display = 'none';
					}
				}
			}
			document.getElementById('onglet_'+NewName+'_'+NewNiveau).className = 'onglet_1 onglet';
			document.getElementById('contenu_onglet_'+NewName+'_'+NewNiveau).style.display = 'block';
		}
		//-->
	</script>
<?php }?>
<?php 
if (!empty($_GET['page']) && $_GET['page'] == 'bricolage'){
?>
	<script type="text/javascript">
		//<!--
		function change_onglet(NewName){
			document.getElementById('onglet_'+OldName).className = 'onglet_0 onglet';
			document.getElementById('onglet_'+NewName).className = 'onglet_1 onglet';
			document.getElementById('contenu_onglet_'+OldName).style.display = 'none';
			document.getElementById('contenu_onglet_'+NewName).style.display = 'block';
			
			OldName = NewName;
		}
		//-->
	</script>
<?php }?>
</head>
<body>
	<div id="curseur" class="infobulle"></div>
	<div id="fb-root"></div>
	<div class="container">