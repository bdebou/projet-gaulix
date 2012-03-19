<?php
// On prolonge la session
function chargerClasse($classname){
	require './fct/'.$classname.'.class.php';
}
spl_autoload_register('chargerClasse');

session_start();

include('fct/config.php');

include('model/common.php');

include('view/header.php');

if(!isset($_SESSION['joueur'])){

	if (isset($_GET['page']) && in_array($_GET['page'], array('inscription', 'mp_oublie', 'unconnect'))){
		include('control/'.$_GET['page'].'.php');
	}else{

		if(isset($_POST['login']) and !empty($_POST['login']) and isset($_POST['motdepasse']) and !empty($_POST['motdepasse'])){
			include('model/login.php');
			$ResultCode = CheckIfLoginMPCorrect($_POST['login'], $_POST['motdepasse']);
			
			if (!is_null($ResultCode)){
				if(is_file('view/error/'.$ResultCode.'.php')){
					include('view/error/'.$ResultCode.'.php');
				}else{
					include('view/error/inconnu.php');
				}
			}else{
				//header('Location: ./');
				echo '<script language="javascript">window.location="./";</script>';
			}
		}else{
			include('view/forms/login.php');
		}
		include('model/regles.php');
		
		include('view/regles/statistiques.php');
			
		include('view/regles.php');
	}
}else{
	
	//$_SESSION['main']['uri']			= $codeUri;
	$_SESSION['main']['deplacement']	= 'new';
	
	if(!empty($_GET['page']) && is_file('control/'.$_GET['page'].'.php')){
		include('control/'.$_GET['page'].'.php');
	}else{
		include('control/main.php');
	}
		//on affiche la barre de status
	include('control/loginstatus.php');
		
		//on affiche la barre de menu
	include('control/menu.php');
	
	if (!empty($_GET['page']) && is_file('view/'.$_GET['page'].'.php')){
		include('view/'.$_GET['page'].'.php');
	}else{
		include('view/main.php');
	}
	
}

include('view/footer.php');
?>