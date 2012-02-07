<?php
$arBtMenu =	array(  array('name' => 'Principale',								'link' => './'),
					array('name' => 'Bolga',									'link' => './index.php?page=inventaire'),
					array('name' => 'Equipements',								'link' => './index.php?page=equipement'),
					array('name' => 'Compétences',								'link' => './index.php?page=competences'),
					array('name' => 'Bricolage',								'link' => './index.php?page=bricolage'),
					array('name' => 'Quêtes',									'link' => './index.php?page=quete'),
					array('name' => 'Scores',									'link' => './index.php?page=scores'),
					array('name' => 'Oppidum',									'link' => './index.php?page=village'),
					array('name' => 'Alliances',								'link' => './index.php?page=alliance'),
					array('name' => 'Carte',									'link' => './index.php?page=cartes'),
					array('name' => 'Règles',									'link' => './index.php?page=regles'),
					array('name' => AfficheIcone('options', 15) . ' Options',	'link' => './index.php?page=options')
				);

include('view/menu.php');    
?>