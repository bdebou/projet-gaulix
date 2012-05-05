<?php 
global 	$NumVersion, $nbLigneCarte, $nbColonneCarte, $db, $temp_combat, $arCouleurs, $lstBatimentConstructionUnique, $MAX_essai, $lstPoints,
		$objManager, $CodeCouleurQuete, $lstNonBatiment, $chkDebug, $lstBatimentsNonConstructible, $lstTypeObjets;

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
$lstBatimentConstructionUnique	= array(1,		//Maison
										4,		//Entrepot
										5,		//Potager
										6,		//Ferme
										9,		//Marche
										10,		//Mine
										16);	//Carri�re de pierre

//Liste des batiments Non constructible
$lstBatimentsNonConstructible	= array(7/*Res Pierre*/, 8/*Res Bois*/);
$lstNonBatiment					= array(11 /*Mer*/, 12 /*C�tes*/, 13 /*Rivi�re*/, 14/*Montagne*/, 15/*Dolmen*/);	

//Liste des type d'objets
$lstTypeObjets					= array('Ressource', 'Divers', 'Armement', 'Construction');
//Liste des couleurs pour les quetes
$CodeCouleurQuete	= array('monstre'	=> '#ff6464',
							'recherche'	=> '#82ff82',
							'objet'		=> '#8a8aff',
							'livre'		=> '#8a8aff',
							'romains'	=> '#ff6464');

//Liste des Points
$lstPoints	= array('CombatGagn�'	=>	array(abs(personnage::POINT_COMBAT),				'Combat gagn�'),
					'CombatPerdu'	=> array((abs(personnage::POINT_COMBAT) * -1),			'Combat perdu'),
					'AttBatAdvers'	=> array(abs(batiment::POINT_BATIMENT_ATTAQUE),			'Attaque de batiment adverse'),
					'BatAbim�'		=> array((abs(batiment::POINT_BATIMENT_ATTAQUE) * -1),	'Un de vos batiment a �t� attaqu�'),
					'AttTour'		=> array((abs(tour::POINT_TOUR_ATTAQUE) * -1),			'Attaque d\'une tour'),
					'CmpTermin�'	=> array(abs(personnage::POINT_COMPETENCE_TERMINE),		'1 Niveau de comp�tence termin�'),
					'NivTermin�'	=> array(abs(personnage::POINT_NIVEAU_TERMINE),			'Pass� 1 niveau'),
					'BatR�par�'		=> array(abs(batiment::POINT_BATIMENT_REPARE),			'Batiment r�par� � 100%'),
					'ObjFabriqu�'	=> array(abs(personnage::POINT_OBJET_FABRIQUE),			'Objet fabriqu�'),
					'BatDetruit'	=> array(abs(batiment::POINT_BATIMENT_DETRUIT),			'Vous avez d�truit un batiment adverse'),
					'PersoTu�'		=> array((abs(personnage::POINT_PERSO_TUE) * -1),		'Personnage tu�'));


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

