<?php
include('fct/config.php');

function CheckIfLoginMPCorrect($Login, $Password){

	if(!preg_match('/^[[:alnum:]]+$/', $Login) or !preg_match('/^[[:alnum:]]+$/', $Password)){
		return 'login_bad';
	}else{
		$sql = "SELECT * FROM table_joueurs WHERE login='".mysql_escape_string($Login)."'";
			//On v�rifie si ce login existe
		$requete_1 = mysql_query($sql) or die ( mysql_error() );
		if(mysql_num_rows($requete_1) == 0){
			return 'login_existing';
		}else{
				// On v�rifie si le login et le mot de passe correspondent au compte utilisateur
			$requete_2 = mysql_query($sql." AND password='".mysql_escape_string($Password)."'") or die ( mysql_error() );
			if(mysql_num_rows($requete_2) == 0){
					// On va r�cup�rer les r�sultats
				$result = mysql_fetch_array($requete_1, MYSQL_ASSOC);
					// On va r�cup�rer la date de la derni�re connexion
				$intDate = strtotime($result['dates']);
					// On va r�cup�rer le nombre de tentative et l'affecter
				global $MAX_essai;
				if( date('d', strtotime($result['dates'])) == date('d') AND $MAX_essai <= $result['nbr_connect']){
					return 'login_quota';
				}else{
					$result['nbr_connect']++;
					$update = "UPDATE table_joueurs SET nbr_connect='".$result['nbr_connect']."', dates=NOW() WHERE id='".$result["id"]."'";
					mysql_query($update) or die ( mysql_error() );
					return 'login_bad_password';
				}
			}else{
					// On va r�cup�rer les r�sultats
				$result = mysql_fetch_array($requete_2, MYSQL_ASSOC);
				$nbr_essai = 0;
				$update = "UPDATE table_joueurs SET nbr_connect='".$nbr_essai."', dates=NOW() WHERE id='".$result["id"]."'";
				mysql_query($update) or die ( mysql_error() );
				$_SESSION['joueur'] = $result['login'];
				return null;
			}
		}
	}
}
?>