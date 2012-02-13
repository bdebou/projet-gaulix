<?php 
global 	$NumVersion, $nbLigneCarte, $nbColonneCarte, $db, $nbDeplacement, $temp_combat, $NiveauMaxBatiment, 
		$TempMaxTransaction, $VieMaximum, $arCouleurs, $lstBatimentConstructionUnique, $MAX_essai, $lstPoints,
		$objManager, $CodeCouleurQuete, $lstNonBatiment, $chkDebug;

$DB_serveur			= 'localhost'; 			// Nom du serveur
$DB_utilisateur		= 'gaulix_be'; 			// Nom de l'utilisateur de la base
$DB_motdepasse		= 'A3hwDpwdVTPpC3B9'; 	// Mot de passe pour accèder à la base
$DB_base			= 'gaulix_be'; 			// Nom de la base

$NumVersion			= '3.4';				// Num Version
$MAX_essai			= 3;					// Nombre maximum d'essai de connection
$nbLigneCarte		= 13;					// Nombre de ligne de la carte
$nbColonneCarte		= 13;					// Nombre de colonne de la carte
$nbCarteH			= 5;					//nombre de carte horizontale
$nbCarteV			= 5;					//nombre de carte Verticale
$nbDeplacement		= 1;					// Nombre de point de déplacement gagné tout les x temp
$temp_combat		= 3600 * 1;				// temp entre chaque combat
$NiveauMaxBatiment	= 5;					// Niveau Maximum pour chaque batiment.
$TempMaxTransaction	= 3600 * 24 * 7;		// Temp maximum pour une transaction. Passé ce délai, la transactionj est annulée.
$VieMaximum			= 300;					// Limite de vie maximum

$chkDebug			= false;

//les couleurs
$arCouleurs			= array('Attaque'		=> '#FF0000',					// Red 
							'Defense'		=> '#32CD32',					// LimeGreen
							'Nourriture'	=> '#808000',					// Olive
							'Pierre'		=> '#708090',					// SlateGray
							'Bois'			=> '#8B4513',					// SaddleBrown
							'Hydromel'		=> '#F0E68C',					// Khaki
							'Vie'			=> '#FFA07A',					// LightSalmon
							'Experience'	=> '#6495ED',					// CornflowerBlue
							'Or'			=> '#FFD700');					// Gold

//Listes des batiments que l'on ne peut construire que 1 seule fois
$lstBatimentConstructionUnique	= array(4,		//Entrepot
										5,		//Banque
										6,		//Ferme
										9,		//Marcher
										18);	//Mine

//Liste des batiments disponible
$lstNonBatiment		= array(7,	//Ressource Pierre
							8,	//Ressource Bois
							10, 11, 12, 13, 14, 15, 16, 17);	//Case direction

//Liste des couleurs pour les quetes
$CodeCouleurQuete	= array('monstre'	=> '#ff6464',
							'recherche'	=> '#82ff82',
							'objet'		=> '#8a8aff',
							'livre'		=> '#8a8aff',
							'romains'	=> '#ff6464');

//Liste des Points
$lstPoints	= array('CombatGagné'	=>	array(10,	'Combat gagné'),
					'CombatPerdu'	=> array(-10,	'Combat perdu'),
					'AttBatAdvers'	=> array(3,		'Attaque de batiment adverse'),
					'BatAbimé'		=> array(-3,	'Un de vos batiment a été attaqué'),
					'AttTour'		=> array(-2,	'Attaque d\'une tour'),
					'CmpTerminé'	=> array(5,		'1 Niveau de compétence terminé'),
					'NivTerminé'	=> array(15,	'Passé 1 niveau'),
					'BatRéparé'		=> array(8,		'Batiment réparé à 100%'),
					'ObjFabriqué'	=> array(1,		'Objet fabriqué'),
					'BatDetruit'	=> array(20,	''),
					'PersoTué'		=> array(-150,	'Personnage tué'));


// On se connecte au serveur MySQL
	$connection = mysql_connect($DB_serveur, $DB_utilisateur, $DB_motdepasse) 
		or die ('MySQL error '.mysql_errno().': '.mysql_error());

// On se connecte à la BDD
mysql_select_db($DB_base, $connection)
		or die ('MySQL error '.mysql_errno().': '.mysql_error());

		
	$db = new PDO('mysql:host='.$DB_serveur.';dbname='.$DB_base, $DB_utilisateur, $DB_motdepasse);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué
	
$objManager = new PersonnagesManager($db);
?>

