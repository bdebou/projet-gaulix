<?php 
global 	$arTailleCarte, $db, $temp_combat, $lstBatimentConstructionUnique, $MAX_essai, $lstPoints,
		$objManager, $CodeCouleurQuete, $lstNonBatiment, $lstBatimentsNonConstructible, $lstTypeObjets, $lstRessources, $LstBatimentMultiConstruction,
		$lstBatimentConstructible, $arCartes, $oDB;

date_default_timezone_set('Europe/Brussels');

$DB_serveur			= 'localhost'; 				// Nom du serveur
$DB_utilisateur		= 'gaulix_be'; 				// Nom de l'utilisateur de la base
$DB_motdepasse		= 'A3hwDpwdVTPpC3B9'; 		// Mot de passe pour acc�der � la base
$DB_base			= 'gaulix_be'; 				// Nom de la base

$NumVersion			= '4.1';					// Num Version
$MAX_essai			= 3;						// Nombre maximum d'essai de connection
$arTailleCarte		= array('NbLigne' => 13,	// Nombre de ligne sur chaque carte
							'NbColonne' => 13);	// Nombre de colonne sur chaque carte
$nbCarteH			= 5;						// Nombre de carte horizontale
$nbCarteV			= 5;						// Nombre de carte Verticale
$temp_combat		= 3600 * 1;					// Temp entre chaque combat

$chkDebug			= false;

require_once 'arColor.config.php';

//$oDB = new DBManage();

$arCartes = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y');

//Listes des batiments que l'on ne peut construire que 1 seule fois
$lstBatimentConstructionUnique	= array(maison::ID_BATIMENT,
										potager::ID_BATIMENT,
										scierie::ID_BATIMENT,
										ferme::ID_BATIMENT,
										marche::ID_BATIMENT,
										mine::ID_BATIMENT,
										carriere::ID_BATIMENT);

//Liste des b�timents que l'on peut contruire plusieurs fois.
$LstBatimentMultiConstruction	= array(tour::ID_BATIMENT,
										mur::ID_BATIMENT);

//Liste des b�timents constructible
$lstBatimentConstructible		= array_merge($lstBatimentConstructionUnique, $LstBatimentMultiConstruction);

//Liste des ressources
$lstRessources					= array(maison::TYPE_RES_EAU_POTABLE,
										maison::TYPE_RES_NOURRITURE,
										personnage::TYPE_RES_MONNAIE);

//Liste des batiments Non constructible
$lstBatimentsNonConstructible	= array(ressource::ID_BATIMENT_BOIS, ressource::ID_BATIMENT_EAU, ressource::ID_BATIMENT_PIERRE);
$lstNonBatiment					= array(mer::ID_BATIMENT, cote::ID_BATIMENT, riviere::ID_BATIMENT, montagne::ID_BATIMENT, dolmen::ID_BATIMENT);	

//Liste des type d'objets
$lstTypeObjets					= array('Ressource', 'Armement', 'Construction', 'Divers');
//Liste des couleurs pour les quetes
$CodeCouleurQuete	= array('livre'		=> '#8a8aff',
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

