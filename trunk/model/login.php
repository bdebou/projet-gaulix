<?php
include('fct/config.php');

function CheckIfLoginMPCorrect(DBManage &$db, $Login, $Password){

	if(!preg_match('/^[[:alnum:]]+$/', $Login) or !preg_match('/^[[:alnum:]]+$/', $Password)){
		return 'login_bad';
	}else{
		$sql = "SELECT * FROM table_joueurs WHERE login='".mysql_escape_string($Login)."'";
			//On vérifie si ce login existe
		//$requete_1 = mysql_query($sql) or die ( mysql_error() );
		$requete_1 = $db->Select('table_joueurs', NULL, array("login='".mysql_escape_string($Login)."'"));
		if(mysql_num_rows($requete_1) == 0){
			return 'login_inexisting';
		}else{
				// On vérifie si le login et le mot de passe correspondent au compte utilisateur
			//$requete_2 = mysql_query($sql." AND password='".sha1($Password)."'") or die ( mysql_error() );
			$requete_2 = $db->Select('table_joueurs', NULL, array("login='".mysql_escape_string($Login)."'", "password='".sha1($Password)."'"));
			if(mysql_num_rows($requete_2) == 0){
					// On va récupérer les rÃ©sultats
				$result = mysql_fetch_array($requete_1, MYSQL_ASSOC);
					// On va récupérer la date de la dernière connexion
				$intDate = strtotime($result['dates']);
					// On va récupérer le nombre de tentative et l'affecter
				global $MAX_essai;
				if( date('d', strtotime($result['dates'])) == date('d') AND $MAX_essai <= $result['nbr_connect']){
					return 'login_quota';
				}else{
					$result['nbr_connect']++;
					$update = "UPDATE table_joueurs SET nbr_connect='".$result['nbr_connect']."', dates=NOW() WHERE id='".$result["id"]."'";
					//mysql_query($update) or die ( mysql_error() );
					$db->Query($update);
					return 'login_bad_password';
				}
			}else{
					// On va récupérer les résultats
				$result = mysql_fetch_array($requete_2, MYSQL_ASSOC);
				$nbr_essai = 0;
				$update = "UPDATE table_joueurs SET nbr_connect='".$nbr_essai."', dates=NOW() WHERE id='".$result["id"]."'";
				//mysql_query($update) or die ( mysql_error() );
				$db->Query($update);
				$_SESSION['joueur'] = $result['login'];
				return null;
			}
		}
	}
}
?>