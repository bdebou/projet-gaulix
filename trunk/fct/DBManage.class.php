<?php
class DBManage{
	
	Private $Connection;
	
	Const DB_SERVEUR			= 'localhost'; 			// Nom du serveur
	Const DB_UTILISATEUR		= 'gaulix_be'; 			// Nom de l'utilisateur de la base
	Const DB_MOTDEPASSE			= 'A3hwDpwdVTPpC3B9'; 	// Mot de passe pour accèder à la base
	Const DB_BASE				= 'gaulix_be'; 			// Nom de la base
	
	Const ALL_FIELDS			= "*";
	
	public function __construct(){
		date_default_timezone_set('Europe/Brussels');
	
		// On se connecte au serveur MySQL
		if(!$this->Connection = mysql_connect(self::DB_SERVEUR, self::DB_UTILISATEUR, self::DB_MOTDEPASSE))
			return $this->PseudoHandlerError(mysql_errno(), mysql_error()); 
			
		// On se connecte à la BDD
		if(!mysql_select_db(self::DB_BASE, $this->Connection))
			return $this->PseudoHandlerError(mysql_errno(), mysql_error());
	
	}
	
	public static function Cast(DBManage &$object=NULL){
		return $object;
	}
	
	Public function Query($strQuery){
		if($returns = mysql_query($strQuery)) return $returns;
		
		return $this->PseudoHandlerError(mysql_errno(), mysql_error(), $strQuery);
	}
	Public function NbLigne($strQuery){
		if($returns = mysql_num_rows($this->Query($strQuery))) return $returns;
		
		return $this->PseudoHandlerError(mysql_errno(), mysql_error(), $strQuery);
	}
	private function PseudoHandlerError($errno, $error, $query=NULL){
		if(empty($query))
			die('Une erreur est survenue: <br /> [numéro '.$errno.'] '.$error);
		
		die('Une erreur est survenue, dans l\'exécution de la requête : <br />'.$query.'<br />
			<strong>Erreur détectée - numéro '.$errno.' - '.$error.'</strong>');
	}
	/**
	 * Insert une log dans TABLE_LOG pour le support ou le suivi.
	 * @param string $Type <p>Valeurs possibles: Info, Warning, Error</p>
	 * @param string $Login <p>Le login du joueur en question</p>
	 * @param string $Source <p>de quel source provient la log. Valeurs possibles: SQL, <page></p>
	 * @param integer $ErrNum <p>Le numéro de l'erreur si connu. Si inconnu = 0</p>
	 * @param string $Message <p>Le message de la log.</p>
	 */
	Public function InsertLog($Type, $Login, $Source, $ErrNum = 0, $Message){
		//on construit la requete SQL
		$sql = "INSERT INTO table_log (
					`id`, 
					`log_date`,  
					`log_type`, 
					`log_login`, 
					`log_source`, 
					`log_errnum`,
					`log_message`) 
				VALUES (
					NULL, 
					'".date('Y-m-d H:i:s')."', 
					'".$Type."', 
					'".$Login."', 
					'".$Source."', 
					'".$ErrNum."', 
					'".$Message."');";
		
		//on exécute la requete SQL
		$this->Query($sql);
	}
	
	/**
	 * Insert un historique d'action pour le joueur et son adversaire ou sa quête.
	 * @param string $Login <p>Le login du joueur concerné</p>
	 * @param string $Carte <p>La lettre de la carte lors de l'action</p>
	 * @param array $Position <p>une array de la possition lors de l'action</p>
	 * @param string $Type <p>le type d'action. Valeurs possibles: </p>
	 * @param string $Adversaire <p>Le login du joueur attaqué lors de l'action</p>
	 * @param integer $Date <p>TimeStamp de l'action</p>
	 * @param string $Info <p>Message informatif de l'action</p>
	 */
	public function InsertHistory($Login, $Carte, $Position, $Type, $Adversaire, $Date, $Info) {
		if(is_null($Date)){
			$Date = strtotime('now');
		}
		$sql = "INSERT INTO `table_history` (
					`history_id`, 
					`history_login`, 
					`history_position`, 
					`history_type`, 
					`history_adversaire`, 
					`history_date`, 
					`history_info`)
				VALUES (
					NULL, 
					'$Login', 
					'".implode(',', array_merge(array($Carte), $Position))."',
					'$Type', 
					'$Adversaire', 
					'".date('Y-m-d H:i:s', $Date)."',
					'".htmlentities($Info, ENT_QUOTES)."');";
		
		//on exécute la requete SQL
		$this->Query($sql);
	}
	
	/**
	 * Retourne la ressource correspondante à la requete SELECT sur le table $strTable.
	 * @param string $strTable <p>Nom de la table</p>
	 * @param array $arFields <p>Liste des fields à retourner dans une array. Structure: (Name, Surname, &lt;Field3&gt;, ...)</p>
	 * @param array $arCondition <p>Liste des conditions lié avec AND. Structure: (Name='Coucou', ID<>50, Name IN('test', 'test2'),  &lt;Condition4&gt;, ...)</p>
	 */
	Public function Select($strTable, $arFields = self::ALL_FIELDS, $arCondition = NULL){
		$sql = "SELECT %1 FROM %2 %3;";
		
		//on insère le nom de la table
		$sql = str_replace('%2', $strTable, $sql);
		
		//on insère le ou les champs désirés en retour
		if($arFields == self::ALL_FIELDS Or is_null($arFields))
		{
			$sql = str_replace('%1', self::ALL_FIELDS, $sql);
		}else{
			$sql = str_replace('%1', implode(', ', $arFields), $sql);
		}
		
		//On insère une ou des conditions
		if(!is_null($arCondition))
		{
			$sql = str_replace('%3', 'WHERE '.implode(' AND ', $arCondition), $sql);
		}
		
		//on exécute la requete SQL
		return $this->Query($sql);
	}
}