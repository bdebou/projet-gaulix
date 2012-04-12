<?php 
global 	$NumVersion, $nbLigneCarte, $nbColonneCarte, $db, $temp_combat, $arCouleurs, $lstBatimentConstructionUnique, $MAX_essai, $lstPoints,
		$objManager, $CodeCouleurQuete, $lstNonBatiment, $chkDebug;

date_default_timezone_set('Europe/Brussels');

$DB_serveur			= 'localhost'; 			// Nom du serveur
$DB_utilisateur		= 'gaulix_be'; 			// Nom de l'utilisateur de la base
$DB_motdepasse		= 'A3hwDpwdVTPpC3B9'; 	// Mot de passe pour acc�der � la base
$DB_base			= 'gaulix_be'; 			// Nom de la base

$NumVersion			= '4.0';				// Num Version
$MAX_essai			= 3;					// Nombre maximum d'essai de connection
$nbLigneCarte		= 13;					// Nombre de ligne de la carte
$nbColonneCarte		= 13;					// Nombre de colonne de la carte
$nbCarteH			= 5;					// Nombre de carte horizontale
$nbCarteV			= 5;					// Nombre de carte Verticale
$temp_combat		= 3600 * 1;				// Temp entre chaque combat

$chkDebug			= true;

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
										9,		//Marche
										10);	//Mine

//Liste des batiments disponible
$lstNonBatiment		= array(11 /*Mer du Nord*/);	

//Liste des couleurs pour les quetes
$CodeCouleurQuete	= array('monstre'	=> '#ff6464',
							'recherche'	=> '#82ff82',
							'objet'		=> '#8a8aff',
							'livre'		=> '#8a8aff',
							'romains'	=> '#ff6464');

//Liste des Points
$lstPoints	= array('CombatGagn�'	=>	array(10,	'Combat gagn�'),
					'CombatPerdu'	=> array(-10,	'Combat perdu'),
					'AttBatAdvers'	=> array(3,		'Attaque de batiment adverse'),
					'BatAbim�'		=> array(-3,	'Un de vos batiment a �t� attaqu�'),
					'AttTour'		=> array(-2,	'Attaque d\'une tour'),
					'CmpTermin�'	=> array(5,		'1 Niveau de comp�tence termin�'),
					'NivTermin�'	=> array(15,	'Pass� 1 niveau'),
					'BatR�par�'		=> array(8,		'Batiment r�par� � 100%'),
					'ObjFabriqu�'	=> array(1,		'Objet fabriqu�'),
					'BatDetruit'	=> array(20,	'Vous avez d�truit un batiment adverse'),
					'PersoTu�'		=> array(-150,	'Personnage tu�'));


// On se connecte au serveur MySQL
	$connection = mysql_connect($DB_serveur, $DB_utilisateur, $DB_motdepasse) 
		or die ('MySQL error '.mysql_errno().': '.mysql_error());

// On se connecte � la BDD
mysql_select_db($DB_base, $connection)
		or die ('MySQL error '.mysql_errno().': '.mysql_error());

		
	$db = new PDO('mysql:host='.$DB_serveur.';dbname='.$DB_base, $DB_utilisateur, $DB_motdepasse);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On �met une alerte � chaque fois qu'une requ�te a �chou�
	
$objManager = new PersonnagesManager($db);
?>

