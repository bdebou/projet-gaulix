-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 14 Novembre 2011 à 14:49
-- Version du serveur: 5.1.32
-- Version de PHP: 5.2.9-1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `gaulix_be`
--

-- --------------------------------------------------------

--
-- Structure de la table `table_alliance`
--

CREATE TABLE IF NOT EXISTS `table_alliance` (
  `id_alliance` int(6) NOT NULL AUTO_INCREMENT,
  `chef_clan` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nom_clan` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `membre_clan` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `date_inscription` datetime DEFAULT NULL,
  `membre_actif` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_alliance`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `table_batiment`
--

CREATE TABLE IF NOT EXISTS `table_batiment` (
  `id_batiment` int(11) NOT NULL AUTO_INCREMENT,
  `batiment_type` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `batiment_nom` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `batiment_description` text COLLATE latin1_general_ci,
  `batiment_vie` int(3) DEFAULT NULL,
  `batiment_attaque` int(2) DEFAULT NULL,
  `batiment_defense` int(2) DEFAULT NULL,
  `batiment_distance` int(1) DEFAULT NULL,
  `batiment_niveau` int(2) NOT NULL DEFAULT '0',
  `prix_or` int(4) DEFAULT NULL,
  `prix_bois` int(4) DEFAULT NULL,
  `prix_pierre` int(4) DEFAULT NULL,
  `prix_nourriture` int(4) DEFAULT NULL,
  `batiment_points` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_batiment`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `table_batiment`
--

INSERT INTO `table_batiment` (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `prix_or`, `prix_bois`, `prix_pierre`, `prix_nourriture`, `batiment_points`) VALUES
(1, 'maison', 'Maison', '<p>Votre Maison, sans elle, vous êtes mort</p><p>Protégez la bien!</p>', 1000, 50, 20, NULL, 0, 800, 300, 500, 500, 250),
(2, 'mur', 'Palissade', '<p>Une pallissade de sapin, elle ralentira vos ennemis avant qu''ils ne s''attaquent à votre Quartier Général</p>', 300, NULL, 20, NULL, 0, 200, 100, 500, 300, 50),
(3, 'tour', 'Tour', '<p>Cette tour permet d''attaquer vos ennemis si ils s''approchent trop.</p>', 200, 25, 20, 2, 0, 200, 350, 600, 450, 80),
(4, 'entrepot', 'Entrepot', '<p>Vous pouvez stocker ici tout ce que vous voulez.</p><p>Tout ce qui est stocker ici sera toujours disponible si vous mourrez. Ce qui est quand même utile pour récupérer rapidement des armes, boucliers, ...</p>', 300, NULL, 20, NULL, 0, 300, 600, 200, 500, 150),
(5, 'bank', 'Banque', '<p>C''est la banque de votre village. Comme toute banque, vous pouvez y déposer de l''argent pour le protéger.</p>', 1000, 20, 20, NULL, 0, 500, 300, 600, 700, 200),
(6, 'ferme', 'Ferme', '<p>Vous y cultiverez toute une série de légumes différents pour vous nourrir.</p><p>Ou vous pourrez élever des animaux pour en tiré de la viande, bien plus rentable que les légumes.</p>', 500, NULL, 30, NULL, 0, 350, 350, 200, 500, 150),
(7, 'ressource', 'pierre', 'Une source de pierres.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(8, 'ressource', 'bois', 'Une source de bois.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(9, 'marcher', 'Marcher', '<p>A votre marcher, vous pourrez troquer tout type de matériaux.</p><p>Les troques sont créés par chaque joueur</p>', 400, 10, 30, NULL, 0, 400, 150, 300, 600, 180),
(10, 'carte', 'gh', 'Passer à la carte de gauche ou du haut.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(11, 'carte', 'dh', 'Passer à la carte de droite ou du haut.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(12, 'carte', 'gb', 'Passer à la carte de gauche ou du bas.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(13, 'carte', 'db', 'Passer à la carte de droite ou du bas.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(14, 'carte', 'g', 'Passer à la carte de gauche.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(15, 'carte', 'd', 'Passer à la carte de droite.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(16, 'carte', 'h', 'Passer à la carte du haut.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(17, 'carte', 'b', 'Passer à la carte du bas.', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0),
(18, 'mine', 'Mine', '<p>Votre mine vous permet de récolter de la pierre ou de l''or.</p>', 500, NULL, 30, NULL, 0, 500, 200, 250, 750, 150);

-- --------------------------------------------------------

--
-- Structure de la table `table_carte`
--

CREATE TABLE IF NOT EXISTS `table_carte` (
  `id_case_carte` int(11) NOT NULL AUTO_INCREMENT,
  `coordonnee` varchar(7) COLLATE latin1_general_ci DEFAULT NULL,
  `login` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `id_type_batiment` int(2) DEFAULT NULL,
  `contenu_batiment` text COLLATE latin1_general_ci,
  `res_pierre` int(6) DEFAULT NULL,
  `res_bois` int(6) DEFAULT NULL,
  `res_nourriture` int(6) DEFAULT NULL,
  `date_action_batiment` datetime DEFAULT NULL,
  `etat_batiment` int(3) DEFAULT NULL,
  `date_amelioration` datetime DEFAULT NULL,
  `tmp_amelioration` int(6) DEFAULT NULL,
  `niveau_batiment` int(2) NOT NULL DEFAULT '0',
  `date_last_attaque` datetime DEFAULT NULL,
  `detruit` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_case_carte`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `table_carte`
--

INSERT INTO `table_carte` (`id_case_carte`, `coordonnee`, `login`, `id_type_batiment`, `contenu_batiment`, `res_pierre`, `res_bois`, `res_nourriture`, `date_action_batiment`, `etat_batiment`, `date_amelioration`, `tmp_amelioration`, `niveau_batiment`, `date_last_attaque`, `detruit`) VALUES
-- Carte A
(NULL, 'a,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,13,0', NULL, 17, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,0,13', NULL, 15, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,1,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,0,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,2,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,13,5', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,6,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,6,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte B
(NULL, 'b,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,0,13', NULL, 15, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,0,0', NULL, 14, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,3,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,1,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,3,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,12,7', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,3,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,3,10', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte C
(NULL, 'c,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,0,13', NULL, 15, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,0,0', NULL, 14, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,12,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,4,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,5,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,11,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,5,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,0,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte D
(NULL, 'd,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,0,13', NULL, 15, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,0,0', NULL, 14, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,9,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,8,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,9,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,7,10', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,0,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,5,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte E
(NULL, 'e,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,0,0', NULL, 14, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,13,13', NULL, 17, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,1,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,5,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,4,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,2,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,0,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,8,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte F
(NULL, 'f,2,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,13,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,2,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,5,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,7,0', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,11,0', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,0,0', NULL, 16, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,13,0', NULL, 17, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte G
(NULL, 'g,9,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,1,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,0,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,4,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,1,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,12,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte H
(NULL, 'h,1,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,1,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,12,5', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,6,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,2,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,2,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte I
(NULL, 'i,4,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,10,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,4,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,8,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,6,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,9,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte J
(NULL, 'j,4,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,3,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,11,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,1,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,9,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,2,12', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,0,13', NULL, 16, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,13,13', NULL, 17, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte K
(NULL, 'k,6,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,12,6', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,10,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,5,0', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,10,5', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,0,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,0,0', NULL, 16, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,13,0', NULL, 17, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte L
(NULL, 'l,2,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,1,7', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,2,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,9,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,7,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,6,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte M
(NULL, 'm,7,5', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,9,7', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,4,1', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,8,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,3,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,8,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte N
(NULL, 'n,3,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,10,6', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,8,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,11,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,1,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,6,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte O
(NULL, 'o,5,7', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,12,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,6,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,4,5', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,11,12', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,5,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,0,13', NULL, 16, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,13,13', NULL, 17, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte P
(NULL, 'p,1,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,2,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,9,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,1,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,4,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,7,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,0,0', NULL, 16, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,13,0', NULL, 17, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte Q
(NULL, 'q,3,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,12,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,5,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,2,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,7,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,4,1', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte R
(NULL, 'r,7,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,3,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,8,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,5,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,0,12', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,6,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte S
(NULL, 's,10,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,6,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,12,6', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,10,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,12,0', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,8,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,13,13', NULL, 13, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte T
(NULL, 't,10,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,8,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,10,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,10,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,13,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,11,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,0,13', NULL, 16, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,13,0', NULL, 12, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,13,13', NULL, 17, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte U
(NULL, 'u,5,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,12,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,12,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,2,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,6,5', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,12,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,0,0', NULL, 16, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,13,13', NULL, 15, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte V
(NULL, 'v,0,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,13,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,7,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,13,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,7,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,0,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,13,0', NULL, 14, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,13,13', NULL, 15, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte W
(NULL, 'w,8,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,3,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,8,7', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,9,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,10,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,0,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,13,0', NULL, 14, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,13,13', NULL, 15, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte X
(NULL, 'x,6,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,8,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,1,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,2,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,1,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,5,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,0,13', NULL, 11, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,13,0', NULL, 14, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,13,13', NULL, 15, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte Y
(NULL, 'y,11,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,5,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,4,10', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,9,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,9,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,5,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,0,0', NULL, 10, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,0,13', NULL, 16, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,13,0', NULL, 14, NULL, NULL, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Camp Romain
(NULL, 'm,3,3', 'romain', 3, NULL, NULL, NULL, NULL, NULL, 200, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,9,3', 'romain', 3, NULL, NULL, NULL, NULL, NULL, 200, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,9,9', 'romain', 3, NULL, NULL, NULL, NULL, NULL, 200, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,3,9', 'romain', 3, NULL, NULL, NULL, NULL, NULL, 200, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,6,6', 'romain', 3, NULL, NULL, NULL, NULL, NULL, 200, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,3,5', 'romain', 2, NULL, NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,3,7', 'romain', 2, NULL, NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,5,3', 'romain', 2, NULL, NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,5,9', 'romain', 2, NULL, NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,7,3', 'romain', 2, NULL, NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,7,9', 'romain', 2, NULL, NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,9,5', 'romain', 2, NULL, NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,9,7', 'romain', 2, NULL, NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,5,5', 'romain', 5, 10000, NULL, NULL, NULL, NULL, 1000, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,7,5', 'romain', 5, 10000, NULL, NULL, NULL, NULL, 1000, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,5,7', 'romain', 5, 10000, NULL, NULL, NULL, NULL, 1000, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,7,7', 'romain', 5, 10000, NULL, NULL, NULL, NULL, 1000, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,6,5', 'romain', 4, 'Tissu=300,Sac40=1,LBtS=20,ResPtCentauree=25,ResVerveine=20,ResGui=40,ResAbsinthe=15,ResMiel=100,MlF=250,Arc=1,PPlB=30,GPlB=30,PlF=20', NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,5,6', 'romain', 4, 'Tissu=200,Sac50=2,ResPtCentauree=35,ResAbsinthe=50,ResGui=45,MlF=200,PlF=150,GPlB=80,PrPT=50', NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,6,7', 'romain', 4, 'Tissu=500,Sac50=1,ResAbsinthe=35,ResGui=60,ResVerveine=45,ResMiel=150,MlF=100,PlF=150,Arc=1,CBtD=30,GPlB=100', NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,7,6', 'romain', 4, 'Sac100=1,ResMiel=200,ResAbsinthe=50,ResVerveine=45,ResGui=30,LBtD=25,CBtD=25,GPlB=50,PPlB=45,MlF=150,PlF=50,PtL=10,PtFl=30', NULL, NULL, NULL, NULL, 300, NULL, NULL, 0, NULL, NULL);
-- --------------------------------------------------------

--
-- Structure de la table `table_competence`
--

CREATE TABLE IF NOT EXISTS `table_competence` (
  `cmp_id` int(6) NOT NULL AUTO_INCREMENT,
  `cmp_login` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `cmp_nom` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `cmp_niveau` int(2) DEFAULT NULL,
  `cmp_temp` int(6) DEFAULT NULL,
  `cmp_date` datetime DEFAULT NULL,
  `cmp_finish` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`cmp_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `table_competence_lst`
--

CREATE TABLE IF NOT EXISTS `table_competence_lst` (
  `cmp_lst_id` int(11) NOT NULL AUTO_INCREMENT,
  `cmp_lst_type` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `cmp_lst_nom` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `cmp_lst_description` text COLLATE latin1_general_ci,
  `cmp_lst_niveau` int(2) NOT NULL DEFAULT '0',
  `cmp_lst_temp` int(8) DEFAULT NULL,
  `cmp_lst_prix_or` int(6) DEFAULT NULL,
  `cmp_lst_prix_nourriture` int(6) DEFAULT NULL,
  `cmp_lst_prix_bois` int(6) DEFAULT NULL,
  `cmp_lst_prix_pierre` int(6) DEFAULT NULL,
  `cmp_lst_prix_hydromel` int(6) DEFAULT NULL,
  `cmp_lst_prix_vie` int(6) DEFAULT NULL,
  `cmp_lst_prix_deplacement` int(6) DEFAULT NULL,
  PRIMARY KEY (`cmp_lst_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `table_competence_lst`
--

INSERT INTO `table_competence_lst` (`cmp_lst_id`, `cmp_lst_type`, `cmp_lst_nom`, `cmp_lst_description`, `cmp_lst_niveau`, `cmp_lst_temp`, `cmp_lst_prix_or`, `cmp_lst_prix_nourriture`, `cmp_lst_prix_bois`, `cmp_lst_prix_pierre`, `cmp_lst_prix_hydromel`, `cmp_lst_prix_vie`, `cmp_lst_prix_deplacement`) VALUES
(NULL, 'competence', 'mineur', '<p>Vous allez pouvoir exploiter les carrières en y extrayant de la pierre <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" />.</p>', 1, 3600, 50, NULL, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'mineur', '<p>Vous allez pouvoir mieux exploiter les carrières en y extrayant plus de pierres <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" />.</p>', 2, 86400, 100, 50, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'mineur', '<p>Vous aurez la capacité de trouver des gisement d''or <img src="./img/icones/ic_ResOr.png" alt="Ressource Or" title="Ressource Or" /> en plus de la pierre <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" /> dans les carrières ou dans votre mine.</p>', 3, 108000, 150, 100, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'mineur', '<p>Vous aurez la capacité de trouver en plus dans votre mine des gisements de minerai de fer <img src="./img/icones/ic_ResMinF.png" alt="Ressource minerai de fer" title="Ressource minerai de fer" height="20px" />.</p>', 4, 129600, 300, 150, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'mineur', '<p>Vous aurez la capacité de trouver en plus dans votre mine des gisements de minerai de cuivre <img src="./img/icones/ic_ResMinC.png" alt="Ressource minerai de cuivre" title="Ressource minerai de cuivre" height="20px" />.</p>', 5, 151200, 500, 300, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'bucheron', '<p>Pour couper du <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (50x).</p>', 1, 3600, 50, NULL, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'bucheron', '<p>Pour couper plus de <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (60x).</p>', 2, 86400, 100, 50, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'bucheron', '<p>Pour couper encore plus de <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (70x).</p>', 3, 108000, 150, 100, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'bucheron', '<p>Pour couper toujours plus de <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (80x).</p>', 4, 129600, 300, 150, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'bucheron', '<p>Pour restaurer le cycle de la forêt.</p><p>Grâce à cette compétence, vous n''épuiserez aucune ressource de <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (100x).</p>', 5, 151200, 450, 300, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tailleurp', '<p>Tailleur de pierre pour transformer une <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" /> en pierre pointue <img src="./img/icones/ic_PrP.png" alt="Icone (pierre pointue)" title="Pierre pointue" height="20px" />.</p>', 1, 7200, 200, 100, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tailleurp', '<p>Tailleur de pierre pour tailler une <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" /> en pierre platte tranchante <img src="./img/icones/ic_PrPT.png" alt="Icone (pierre platte tranchante)" title="Pierre platte tranchante" height="20px" />.</p>', 2, 10800, 400, 200, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tailleurp', '<p>Tailleur de pierre pour tailler plus de <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" /> en pierre pointue <img src="./img/icones/ic_PrP.png" alt="Icone (pierre pointue)" title="Pierre pointue" height="20px" /> ou en pierre platte tranchante <img src="./img/icones/ic_PrPT.png" alt="Icone (pierre platte tranchante)" title="Pierre platte tranchante" height="20px" />.</p>', 3, 129600, 600, 400, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'chasseur', '<p>Chasseur de petit gibier (lapin, pigeon, canard).</p>', 1, 86400, 100, 100, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'chasseur', '<p>Chasseur de gibier moyen (sanglier, chêvre).</p>', 2, 108000, 200, 200, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'chasseur', '<p>Chasseur de gros gibier (Cerf, biche).</p>', 3, 129600, 300, 250, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'charpentier', '<p>Vous pourrez fabriquer de court bâton dur <img src="./img/icones/ic_CBtD.png" alt="Icone (court bâton dur)" title="Court bâton dur" height="20px" /> ou souple <img src="./img/icones/ic_CBtS.png" alt="Icone (court bâton souple)" title="Court bâton souple" height="20px" />.</p>', 1, 86400, 200, 50, 200, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'charpentier', '<p>Vous pourrez fabriquer de petite planche de bois <img src="./img/icones/ic_PPlB.png" alt="Icone (petite planche de bois)" title="Petite planche de bois" height="20px" />.</p>', 2, 108000, 300, 50, 250, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'charpentier', '<p>Vous pourrez fabriquer de long bâton dur <img src="./img/icones/ic_LBtD.png" alt="Icone (long bâton dur)" title="Long bâton dur" height="20px" /> ou souple <img src="./img/icones/ic_LBtS.png" alt="Icone (long bâton souple)" title="Long bâton souple" height="20px" /> et de grande planche <img src="./img/icones/ic_GPlB.png" alt="Icone (grande planche de bois)" title="Grande planche de bois" height="20px" />.</p>', 3, 129600, 450, 50, 300, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'boucher', '<p>Pour récupérer 3x plus de <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> sur le gibier.</p>', 1, 86400, 100, 100, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'boucher', '<p>Pour récupérer 6x plus de <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> sur le gibier.</p>', 2, 108000, 200, 200, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'boucher', '<p>Pour récupérer 10x plus de <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> sur le gibier.</p>', 3, 129600, 300, 300, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tanneur', '<p>Pour récupérer la peau du gibier et en faire du cuir <img src="./img/icones/ic_ResCuir.png" alt="Icone (cuir)" title="Cuir" height="20px" />.</p>', 1, 7200, 50, 50, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tanneur', '<p>Pour récupérer 2x plus de peau du gibier et en faire du cuir <img src="./img/icones/ic_ResCuir.png" alt="Icone (cuir)" title="Cuir" height="20px" />.</p>', 2, 86400, 100, 100, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tanneur', '<p>Pour récupérer 3x plus de peau du gibier et en faire du cuir <img src="./img/icones/ic_ResCuir.png" alt="Icone (cuir)" title="Cuir" height="20px" />.</p>', 3, 108000, 150, 150, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tanneur', '<p>Pour récupérer 4x plus de peau du gibier et en faire du cuir <img src="./img/icones/ic_ResCuir.png" alt="Icone (cuir)" title="Cuir" height="20px" />.</p>', 4, 129600, 250, 250, NULL, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tissage', '<p>Pour fabriquer des tissus <img src="./img/icones/ic_Tissu.png" alt="Icone (tissu)" title="Tissu" height="20px" /> et des cordes <img src="./img/icones/ic_Corde.png" alt="Icone (corde)" title="Corde" height="20px" />.</p>', 1, 86400, 100, 50, 20, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tissage', '<p>Pour fabriquer des bolgas.</p>', 2, 108000, 150, 100, 40, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'tissage', '<p>Pour fabriquer des grand bolgas.</p>', 3, 129600, 250, 150, 60, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 1, 86400, 150, 100, 150, 50, NULL, NULL, NULL),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 2, 108000, 250, 200, 200, 75, NULL, NULL, NULL),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 3, 129600, 400, 300, 250, 100, NULL, NULL, NULL),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 4, 151200, 650, 400, 300, 125, NULL, NULL, NULL),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 5, 172800, 1050, 500, 350, 150, NULL, NULL, NULL),
(NULL, 'competence', 'metallurgie', '<p>Pour fabriquer des pointes de flèches <img src="./img/icones/ic_PtFl.png" alt="Icone (pointe de flèche)" title="Pointe de flèche" height="20px" /> et de lances <img src="./img/icones/ic_PtL.png" alt="Icone (pointe de lance)" title="Pointe de lance" height="20px" />.</p>', 1, 86400, 300, 100, 500, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'metallurgie', '<p>Pour fabriquer des plaques de fer <img src="./img/icones/ic_PlF.png" alt="Icone (plaque de fer)" title="Plaque de fer" height="20px" /> et des mailles <img src="./img/icones/ic_MlF.png" alt="Icone (mailles de fer)" title="Mailles de fer" height="20px" />.</p>', 2, 108000, 500, 200, 1000, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'metallurgie', '<p>Pour fabriquer des épées.</p>', 3, 129600, 800, 300, 1500, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'cuisine', '<p>Pour transformer de la Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> en Hydromel <img src="./img/icones/ic_hydromel.png" alt="Icone (hydromel)" title="Hydromel" height="20px" />.</p>', 1, 43200, 250, 150, 150, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'cuisine', '<p>Pour transformer plus de Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> en Hydromel <img src="./img/icones/ic_hydromel.png" alt="Icone (hydromel)" title="Hydromel" height="20px" />.</p>', 2, 86400, 500, 250, 300, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'cuisine', '<p>Pour transformer encore plus de Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> en Hydromel <img src="./img/icones/ic_hydromel.png" alt="Icone (hydromel)" title="Hydromel" height="20px" />.</p>', 3, 108000, 1000, 350, 450, NULL, NULL, NULL, NULL),
(NULL, 'competence', 'chimie', '<p>La Chimie vous permettra de préparer des potions en tout genre.</p>', 1, 86400, 500, 300, 100, NULL, 50, NULL, NULL),
(NULL, 'competence', 'chimie', '<p>La Chimie vous permettra de préparer des potions en tout genre.</p>', 2, 172800, 700, 500, 200, NULL, 100, NULL, NULL),
(NULL, 'competence', 'chimie', '<p>La Chimie vous permettra de préparer des potions en tout genre.</p>', 3, 259200, 1000, 750, 300, NULL, 150, NULL, NULL),
(NULL, 'druide', NULL, NULL, 1, NULL, -50, -30, NULL, NULL, -1, 1, NULL),
(NULL, 'druide', NULL, NULL, 2, NULL, -50, -45, NULL, NULL, -2, 2, NULL),
(NULL, 'druide', NULL, NULL, 3, NULL, -50, -60, NULL, NULL, -3, 3, NULL),
(NULL, 'druide', NULL, NULL, 4, NULL, -50, -75, NULL, NULL, -4, 4, NULL),
(NULL, 'druide', NULL, NULL, 5, NULL, -50, -90, NULL, NULL, -5, 5, NULL),
(NULL, 'druide', NULL, NULL, 1, NULL, -20, -50, NULL, NULL, -1, NULL, 1),
(NULL, 'druide', NULL, NULL, 2, NULL, -40, -50, NULL, NULL, -2, NULL, 2),
(NULL, 'druide', NULL, NULL, 3, NULL, -60, -50, NULL, NULL, -3, NULL, 3),
(NULL, 'druide', NULL, NULL, 4, NULL, -80, -50, NULL, NULL, -4, NULL, 4),
(NULL, 'druide', NULL, NULL, 5, NULL, -100, -50, NULL, NULL, -5, NULL, 5),
(NULL, 'druide', 'SrtQuete', 'Divulguer les coordonnées des quêtes en cours.', 4, NULL, -500, -250, -100, -100, -20, NULL, NULL),
(NULL, 'competence', 'agriculture', '<p>Cela vous permettra de produire plus de Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" />.</p>', 1, 86400, 200, 500, 100, NULL, 10, NULL, NULL),
(NULL, 'competence', 'agriculture', '<p>Cela vous permettra de produire soit de Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" />, soit du Cotton <img src="./img/icones/ic_ResFibC.png" alt="Icone (fibre de cotton)" title="Fibre de cotton" height="20px" />.</p>', 2, 129600, 500, 1000, 200, NULL, 10, NULL, NULL),
(NULL, 'competence', 'agriculture', '<p>Cela vous permettra de produire soit de la Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" />, soit du Cotton <img src="./img/icones/ic_ResFibC.png" alt="Icone (fibre de cotton)" title="Fibre de cotton" height="20px" />, soit du Miel <img src="./img/icones/ic_ResMiel.png" alt="Icone (du miel)" title="Du miel" height="20px" />.</p>', 3, 216000, 600, 1200, 300, NULL, 10, NULL, NULL),
(NULL, 'competence', 'magie', '<p>La compétence magie, vous donnera la possibilité de préparer des sorts en tout genre.</p>', 1, 198720, 500, 700, 300, 200, 150, NULL, NULL),
(NULL, 'competence', 'magie', '<p>La compétence magie, vous donnera la possibilité de préparer des sorts en tout genre.</p>', 2, 397440, 750, 1000, 500, 300, 150, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `table_history`
--

CREATE TABLE IF NOT EXISTS `table_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `history_login` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `history_position` varchar(7) COLLATE latin1_general_ci NOT NULL,
  `history_type` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `history_adversaire` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `history_date` datetime NOT NULL,
  `history_info` text COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `table_joueurs`
--

CREATE TABLE IF NOT EXISTS `table_joueurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `mail` varchar(150) COLLATE latin1_general_ci DEFAULT NULL,
  `dates` datetime NOT NULL,
  `nbr_connect` int(1) DEFAULT NULL,
  `position` varchar(7) COLLATE latin1_general_ci NOT NULL DEFAULT 'a,0,0',
  `argent` int(6) NOT NULL DEFAULT '0',
  `vie` int(4) NOT NULL DEFAULT '100',
  `maison_installe` varchar(7) COLLATE latin1_general_ci DEFAULT NULL,
  `val_attaque` int(5) DEFAULT NULL,
  `val_defense` int(5) DEFAULT NULL,
  `experience` int(5) DEFAULT NULL,
  `niveau` int(2) NOT NULL DEFAULT '0',
  `deplacement` int(4) NOT NULL DEFAULT '10',
  `last_action` datetime DEFAULT NULL,
  `date_last_combat` datetime DEFAULT NULL,
  `attaque_tour` tinyint(1) DEFAULT NULL,
  `date_perf_attaque` datetime DEFAULT NULL,
  `tmp_perf_attaque` int(6) DEFAULT NULL,
  `date_perf_defense` datetime DEFAULT NULL,
  `tmp_perf_defense` int(6) DEFAULT NULL,
  `chk_chasse` tinyint(1) DEFAULT NULL,
  `chk_object` tinyint(1) DEFAULT NULL,
  `last_object` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `code_casque` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `code_arme` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `code_bouclier` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `code_jambiere` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `code_cuirasse` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `code_sac` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `livre_sorts` text COLLATE latin1_general_ci DEFAULT NULL,
  `nb_combats` int(4) NOT NULL DEFAULT '0',
  `nb_victoire` int(3) NOT NULL DEFAULT '0',
  `nb_vaincu` int(3) NOT NULL DEFAULT '0',
  `inventaire` text COLLATE latin1_general_ci,
  `clan` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `date_last_msg_lu` datetime DEFAULT NULL,
  `not_attaque` tinyint(1) DEFAULT NULL,
  `not_combat` tinyint(1) DEFAULT NULL,
  `nb_points` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `table_marcher`
--

CREATE TABLE IF NOT EXISTS `table_marcher` (
  `ID_troc` int(11) NOT NULL AUTO_INCREMENT,
  `vendeur` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `acheteur` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `vente_nourriture` int(4) DEFAULT NULL,
  `vente_bois` int(4) DEFAULT NULL,
  `vente_pierre` int(4) DEFAULT NULL,
  `vente_or` int(4) DEFAULT NULL,
  `achat_nourriture` int(4) DEFAULT NULL,
  `achat_bois` int(4) DEFAULT NULL,
  `achat_pierre` int(4) DEFAULT NULL,
  `achat_or` int(4) DEFAULT NULL,
  `date_vente` datetime DEFAULT NULL,
  `status_vente` tinyint(1) DEFAULT NULL,
  `type_vendeur` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT 'joueur',
  `contenu_vendeur` text COLLATE latin1_general_ci,
  PRIMARY KEY (`ID_troc`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `table_marcher`
--

INSERT INTO `table_marcher` (`ID_troc`, `vendeur`, `acheteur`, `vente_nourriture`, `vente_bois`, `vente_pierre`, `vente_or`, `achat_nourriture`, `achat_bois`, `achat_pierre`, `achat_or`, `date_vente`, `status_vente`, `type_vendeur`, `contenu_vendeur`) VALUES
(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'marchant', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `table_objets`
--

CREATE TABLE IF NOT EXISTS `table_objets` (
  `id_objet` int(5) NOT NULL AUTO_INCREMENT,
  `objet_type` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `objet_quete` int(3) DEFAULT NULL,
  `objet_code` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `objet_nom` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `objet_description` text COLLATE latin1_general_ci,
  `objet_competence` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `objet_niveau` int(2) DEFAULT NULL,
  `objet_nb` int(2) DEFAULT NULL,
  `objet_prix` int(5) DEFAULT NULL,
  `objet_attaque` int(2) DEFAULT NULL,
  `objet_defense` int(2) DEFAULT NULL,
  `objet_distance` int(2) DEFAULT NULL,
  `objet_ressource` text COLLATE latin1_general_ci,
  PRIMARY KEY (`id_objet`),
  UNIQUE KEY `objet_code` (`objet_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `table_objets`
--

INSERT INTO `table_objets` (`id_objet`, `objet_type`, `objet_quete`, `objet_code`, `objet_nom`, `objet_description`, `objet_competence`, `objet_niveau`, `objet_nb`, `objet_prix`, `objet_attaque`, `objet_defense`, `objet_distance`, `objet_ressource`) VALUES
(NULL, 'objet', NULL, 'Tissu', 'Tissu', NULL, 'tissage', 1, 0, 10, NULL, NULL, NULL, 'ResFibC=5,ResNou=5'),
(NULL, 'objet', NULL, 'LBtS', 'Long Bâton souple', '<p>Sera utilisé pour la fabrication des armes.</p>', 'charpentier', 3, 0, 10, NULL, NULL, NULL, 'ResBoi=25,ResNou=5'),
(NULL, 'objet', NULL, 'LBtD', 'Long Bâton dur', '<p>Sera utilisé pour la fabrication des armes.</p>', 'charpentier', 3, 0, 12, NULL, NULL, NULL, 'ResBoi=30,ResNou=5'),
(NULL, 'objet', NULL, 'CBtS', 'Court Bâton souple', '<p>Sera utilisé pour la fabrication des armes.</p>', 'charpentier', 1, 0, 6, NULL, NULL, NULL, 'ResBoi=15,ResNou=5'),
(NULL, 'objet', NULL, 'CBtD', 'Court Bâton dur', '<p>Sera utilisé pour la fabrication des armes.</p>', 'charpentier', 1, 0, 8, NULL, NULL, NULL, 'ResBoi=20,ResNou=5'),
(NULL, 'objet', NULL, 'PrPT', 'Pierre platte tranchante', '<p>Sera utilisé pour la fabrication des armes.</p>', 'tailleurp', 2, 0, 4, NULL, NULL, NULL, 'ResPie=10,ResNou=5'),
(NULL, 'objet', NULL, 'PrP', 'Pierre pointue', '<p>Sera utilisé pour la fabrication des armes.</p>', 'tailleurp', 1, 0, 6, NULL, NULL, NULL, 'ResPie=15,ResNou=5'),
(NULL, 'objet', NULL, 'Corde', 'Corde', NULL, 'tissage', 1, 0, 4, NULL, NULL, NULL, 'ResFibC=2,ResNou=5'),
(NULL, 'objet', NULL, 'GPlB', 'Grande Planche de Bois', '<p>Sera utilisée pour les grands boucliers.</p>', 'charpentier', 3, 0, 8, NULL, NULL, NULL, 'ResBoi=15,ResNou=5'),
(NULL, 'objet', NULL, 'PPlB', 'Petite Planche de Bois', '<p>Sera utilisée pour les boucliers.</p>', 'charpentier', 2, 0, 6, NULL, NULL, NULL, 'ResBoi=10,ResNou=5'),
(NULL, 'objet', NULL, 'PtFl', 'Pointe de Flèche', '<p>Sera utilisée pour les flèches de l''arc ou de l''arbalète.</p>', 'metallurgie', 1, 0, 25, NULL, NULL, NULL, 'ResMinF=5,ResBoi=15,ResNou=5'),
(NULL, 'objet', NULL, 'PtL', 'Pointe de Lance', '<p>Sera utilisée pour les lances.</p>', 'metallurgie', 1, 0, 30, NULL, NULL, NULL, 'ResMinF=10,ResBoi=30,ResNou=5'),
(NULL, 'objet', NULL, 'PlF', 'Plaque de fer', '<p>Sera utilisée pour renforcer ou fabriquer des armures.</p>', 'metallurgie', 2, 0, 15, NULL, NULL, NULL, 'ResMinF=15,ResBoi=45,ResNou=5'),
(NULL, 'objet', NULL, 'MlF', 'Mailles de fer', '<p>Sera utilisée pour créer la cote de mailles.</p>', 'metallurgie', 2, 0, 1, NULL, NULL, NULL, 'ResMinF=1,ResBoi=1,ResNou=1'),
(NULL, 'objet', NULL, 'Hydromel', 'Hydromel', '<p>L''Hydromel sera échangée avec votre druide contre des services.</p>', 'cuisine', 1, 0, 10, NULL, NULL, NULL, 'ResNou=10,ResBoi=1,ResMiel=2'),
(NULL, 'sac', NULL, 'Sac30', 'Bolga de 30', '<p>Un Bolga est un sac. Ce bolga peut contenir jusqu''à 30 objets</p>', 'tissage', 1, 0, 416, 30, NULL, NULL, 'Tissu=30,Corde=5,ResNou=30'),
(NULL, 'sac', NULL, 'Sac40', 'Bolga de 40', '<p>Un Bolga est un sac. Ce bolga peut contenir jusqu''à 40 objets</p>', 'tissage', 2, 0, 816, 40, NULL, NULL, 'Tissu=60,Corde=7,ResNou=40'),
(NULL, 'sac', NULL, 'Sac50', 'Bolga de 50', '<p>Un Bolga est un sac. Ce bolga peut contenir jusqu''à 50 objets</p>', 'tanneur', 2, 0, 546, 50, NULL, NULL, 'ResCuir=120,Corde=15,ResNou=50'),
(NULL, 'sac', NULL, 'Sac100', 'Bolga de 100', '<p>Un Bolga est un sac. Ce bolga peut contenir jusqu''à 100 objets</p>', 'tissage', 3, 0, 2106, 100, NULL, NULL, 'Tissu=100,ResCuir=180,Corde=20,ResNou=100'),
(NULL, 'arme', NULL, 'PLance', 'Petite Lance', NULL, 'armurier', 1, 0, 10, 2, 1, 1, 'CBtD=1,PrP=1,ResNou=5'),
(NULL, 'arme', NULL, 'GLance', 'Grande Lance', NULL, 'armurier', 2, 0, 20, 4, 1, 2, 'LBtD=1,PtL=1,ResNou=10'),
(NULL, 'arme', NULL, 'Hache', 'Hache', NULL, 'armurier', 2, 0, 22, 4, 1, 0, 'CBtD=1,Corde=1,PrPT=1,ResNou=10'),
(NULL, 'arme', NULL, 'GHache', 'Grande Hache', NULL, 'armurier', 3, 0, 30, 6, 2, 0, 'LBtD=1,Corde=1,PrPT=2,ResNou=15'),
(NULL, 'arme', NULL, 'Arc', 'Arc à flèches', NULL, 'armurier', 2, 0, 194, 6, 0, 2, 'CBtD=10,CBtS=1,Corde=1,PtFl=10,ResNou=10'),
(NULL, 'arme', NULL, 'Arbal', 'Arbalète', NULL, 'armurier', 3, 0, 234, 7, 0, 3, 'CBtD=12,CBtS=2,Corde=2,PtFl=10,ResCuir=1,ResNou=15'),
(NULL, 'arme', NULL, 'ArbalLd', 'Arbalète lourde', NULL, 'armurier', 4, 0, 264, 9, 0, 4, 'CBtD=14,CBtS=2,Corde=3,PtFl=10,ResCuir=2,ResNou=20'),
(NULL, 'arme', NULL, 'GArc', 'Grand arc à flèches', NULL, 'armurier', 4, 0, 244, 8, 0, 3, 'LBtD=10,LBtS=1,Corde=2,PtFl=10,ResNou=20'),
(NULL, 'arme', NULL, 'Epee', 'Epée', NULL, 'metallurgie', 3, 0, 56, 5, 0, 0, 'ResMinF=10,ResBoi=20,ResCuir=1,ResNou=15'),
(NULL, 'arme', 5, 'EpeeV', 'Épée de la Vérité', '<p>Cette épée triomphera de la vérité</p>', NULL, 4, 0, 350, 12, 4, 1, NULL),
(NULL, 'bouclier', NULL, 'PBcB', 'Petit Bouclier en bois', NULL, 'armurier', 1, 0, 36, 0, 3, 0, 'PPlB=5,Corde=1,ResNou=5'),
(NULL, 'bouclier', NULL, 'GBcB', 'Grand Bouclier en bois', NULL, 'armurier', 1, 0, 72, 0, 5, 0, 'GPlB=8,Corde=4,ResNou=5'),
(NULL, 'bouclier', NULL, 'ScutumF', 'Scutum en Fer', NULL, 'armurier', 2, 0, 133, 0, 8, 0, 'PlF=5,PPlB=6,Corde=2,ResNou=10'),
(NULL, 'bouclier', NULL, 'TargeC', 'Targe en Cuir', NULL, 'armurier', 2, 0, 110, 0, 7, 0, 'ResCuir=6,GPlB=10,Corde=5,ResNou=10'),
(NULL, 'bouclier', NULL, 'ScutumA', 'Scutum en acier', NULL, 'armurier', 3, 0, 339, 0, 10, 0, 'PlF=15,PPlB=8,Corde=6,ResNou=15'),
(NULL, 'bouclier', NULL, 'HoplonF', 'Hoplon en fer', NULL, 'armurier', 3, 0, 442, 1, 11, 0, 'PlF=20,ResCuir=6,Corde=6,PPlB=6,ResNou=15'),
(NULL, 'bouclier', NULL, 'HoplonC', 'Hoplon en cuivre', NULL, 'armurier', 4, 0, 770, 2, 14, 0, 'PlF=20,ResMinC=20,PPlB=6,Corde=4,ResCuir=6,ResNou=20'),
(NULL, 'bouclier', NULL, 'HoplonA', 'Hoplon en Acier', NULL, 'armurier', 4, 0, 734, 3, 15, 0, 'PlF=40,ResCuir=6,Corde=4,ResNou=20'),
(NULL, 'jambiere', NULL, 'Sand', 'Sandales', NULL, 'armurier', 1, 0, 94, 0, 1, 0, 'ResCuir=2,Tissu=5,Corde=4,ResNou=5'),
(NULL, 'jambiere', NULL, 'ChausC', 'Chaussures en Cuir', NULL, 'armurier', 1, 0, 110, 0, 2, 0, 'ResCuir=6,Tissu=4,Corde=4,ResNou=5'),
(NULL, 'jambiere', NULL, 'CaligC', 'Caligae en cuir', NULL, 'armurier', 2, 0, 136, 0, 3, 0, 'ResCuir=20,Corde=6,ResNou=10'),
(NULL, 'jambiere', NULL, 'CaligCR', 'Caligae en cuir renforcée', NULL, 'armurier', 3, 0, 214, 0, 5, 0, 'ResCuir=30,Corde=6,PlF=8,ResNou=15'),
(NULL, 'jambiere', NULL, 'BotR', 'Bottes de cuir renforcées', NULL, 'armurier', 2, 0, 178, 0, 6, 0, 'ResCuir=25,Corde=6,PlF=4,ResNou=10'),
(NULL, 'jambiere', NULL, 'JmbCD', 'Jambière en cuir dur', NULL, 'armurier', 3, 0, 136, 0, 8, 0, 'ResCuir=50,Corde=8,ResNou=15'),
(NULL, 'jambiere', NULL, 'JmbF', 'Jambière en fer', NULL, 'armurier', 4, 0, 406, 2, 8, 0, 'PlF=20,ResCuir=10,Corde=8,ResNou=20'),
(NULL, 'jambiere', NULL, 'JmbA', 'Jambière en Acier', NULL, 'armurier', 5, 0, 576, 3, 10, 0, 'PlF=40,ResCuir=12,Corde=8,ResNou=25'),
(NULL, 'cuirasse', NULL, 'VtE', 'Veste épaisse', NULL, 'armurier', 1, 0, 276, 0, 3, 0, 'Tissu=20,Corde=6,ResNou=5'),
(NULL, 'cuirasse', NULL, 'VtM', 'Veste matelassée', NULL, 'armurier', 2, 0, 380, 0, 4, 0, 'Tissu=20,Corde=10,ResFibC=20,ResNou=10'),
(NULL, 'cuirasse', NULL, 'VtC', 'Veste de cuir', NULL, 'armurier', 2, 0, 256, 0, 5, 0, 'ResCuir=20,Tissu=10,Corde=6,ResNou=10'),
(NULL, 'cuirasse', NULL, 'VtCD', 'Veste de cuir dur', NULL, 'armurier', 3, 0, 355, 0, 7, 0, 'ResCuir=35,Tissu=12,Corde=6,ResNou=15'),
(NULL, 'cuirasse', NULL, 'VtCR', 'Veste de cuir renforcée', NULL, 'armurier', 4, 0, 445, 0, 8, 0, 'ResCuir=35,Tissu=12,Corde=6,PlF=4,ResNou=20'),
(NULL, 'cuirasse', NULL, 'CtM', 'Cotte de mailles', NULL, 'armurier', 5, 0, 499, 0, 10, 0, 'MlF=150,ResCuir=5,Corde=6,ResNou=25'),
(NULL, 'casque', NULL, 'CasqueC', 'Casque en Cuir', NULL, 'armurier', 1, 0, 62, 0, 2, 0, 'ResCuir=4,Tissu=3,Corde=1,ResNou=5'),
(NULL, 'casque', NULL, 'CasqueF', 'Casque en Fer', NULL, 'armurier', 2, 0, 94, 0, 4, 0, 'ResCuir=4,Corde=1,PlF=5,ResNou=10'),
(NULL, 'casque', NULL, 'ElmoF', 'Elmo en Fer', NULL, 'armurier', 2, 0, 111, 0, 6, 0, 'ResCuir=4,Corde=1,PlF=8,ResNou=10'),
(NULL, 'casque', NULL, 'SalletA', 'Sallet en Acier', NULL, 'armurier', 3, 0, 207, 1, 10, 0, 'PlF=10,ResCuir=5,Corde=2,ResNou=15'),
(NULL, 'casque', NULL, 'BarbutF', 'Barbute en Fer', NULL, 'armurier', 3, 0, 234, 2, 13, 0, 'PlF=10,Corde=2,ResCuir=4,PtL=1,ResNou=15'),
(NULL, 'casque', NULL, 'CelataA', 'Celata en Acier', NULL, 'armurier', 4, 0, 341, 5, 15, 0, 'PlF=15,ResCuir=6,Corde=4,PtL=1,ResNou=20'),
(NULL, 'casque', NULL, 'BarbutA', 'Barbute en Acier', NULL, 'armurier', 5, 0, 501, 5, 18, 0, 'PlF=20,ResMinC=5,PtL=1,ResCuir=4,Corde=4,ResNou=25'),
(NULL, 'ressource', NULL, 'ResVie1', 'Gagne 1pt de vie', NULL, NULL, 0, 1, 5, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResVie2', 'Gagne 2pts de vie', NULL, NULL, 0, 2, 10, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResVie3', 'Gagne 3pts de vie', NULL, NULL, 0, 3, 15, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResVie4', 'Gagne 4pts de vie', NULL, NULL, 0, 4, 20, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResVie5', 'Gagne 5pts de vie', NULL, NULL, 0, 5, 25, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResDep1', 'Gagne 1 déplacement', NULL, NULL, 0, 1, 1, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResDep2', 'Gagne 2 déplacements', NULL, NULL, 0, 2, 2, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResDep3', 'Gagne 3 déplacements', NULL, NULL, 0, 3, 3, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResDep4', 'Gagne 4 déplacements', NULL, NULL, 0, 4, 4, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResDep5', 'Gagne 5 déplacements', NULL, NULL, 0, 5, 5, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResOr5', '5 pièces d''or', NULL, NULL, 0, 5, 5, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResOr10', '10 pièces d''or', NULL, NULL, 0, 10, 10, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResOr15', '15 pièces d''or', NULL, NULL, 0, 15, 15, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResOr20', '20 pièces d''or', NULL, NULL, 0, 20, 20, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResOr25', '25 pièces d''or', NULL, NULL, 0, 25, 25, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResOr50', '50 pièces d''or', NULL, NULL, 0, 50, 50, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResNou20', 'des Glands', '<p>Des glands, c''est rempli de vitamines.</p>', NULL, 0, 20, 10, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResNou30', 'des Champignons', '<p>Des champignons comestibles, très bon dans une omelette</p>', NULL, 0, 30, 15, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResNou10', 'des Oeufs', '<p>Des oeufs, très bien pour un apport en protéine.</p>', NULL, 0, 10, 5, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResNou40', 'des Céréales', '<p>Des céréales pour se revitaliser.</p>', NULL, 0, 40, 20, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResNou50', 'Pannier de Légumes', '<p>Un pannier rempli de légumes variés et délicieux.</p>', NULL, 0, 50, 25, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResBoi5', '5 Buches', '<p>Du bois, bien utile pour construire toute sorte de choses</p>', NULL, 0, 5, 1, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResBoi10', '10 Buches', '<p>Du bois, bien utile pour construire toute sorte de choses</p>', NULL, 0, 10, 2, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResBoi15', '15 Buches', '<p>Du bois, bien utile pour construire toute sorte de choses</p>', NULL, 0, 15, 3, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResBoi20', '20 Buches', '<p>Du bois, bien utile pour construire toute sorte de choses</p>', NULL, 0, 20, 4, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResBoi25', '25 Buches', '<p>Du bois, bien utile pour construire toute sorte de choses</p>', NULL, 0, 25, 5, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResPie5', '5 pierres', '<p>Des pierres, bien utile pour construire toute sorte de bâtiments </p>', NULL, 0, 5, 1, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResPie10', '10 pierres', '<p>Des pierres, bien utile pour construire toute sorte de bâtiments </p>', NULL, 0, 10, 2, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResPie15', '15 pierres', '<p>Des pierres, bien utile pour construire toute sorte de bâtiments </p>', NULL, 0, 15, 3, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResPie20', '20 pierres', '<p>Des pierres, bien utile pour construire toute sorte de bâtiments </p>', NULL, 0, 20, 4, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResPie25', '25 pierres', '<p>Des pierres, bien utile pour construire toute sorte de bâtiments </p>', NULL, 0, 25, 5, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResMinF', 'Minerai de fer', '<p>Du minerai de fer, que vous pourrez utiliser pour fabriquer des objets.</p>', NULL, 2, 1, 10, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResMinC', 'Minerai de Cuivre', '<p>Le minerai de cuivre est utilisé en métallurgie pour fabriquer des armes et armures.</p>', NULL, 3, 1, 15, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResFibC', 'Fibre de cotton', '<p>Fibre de cotton sera utilisé pour les tissus et cordes.</p>', NULL, 1, 1, 5, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResCuir', 'Cuir', '<p>Le cuir est utilisé pour fabriquer des habits, chaussures, ...</p>', NULL, 1, 1, 3, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResMiel', 'du Miel', '<p>Le miel sera utilisé dans la fabrication d''hydromel.</p>', NULL, 1, 1, 5, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResAbsinthe', 'Absinthe', '<p>L''Absinthe sera utilisée pour fabriquer des potions.</p>', NULL, 3, 1, 10, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResGui', 'du Gui', '<p>Le Gui sera utilisé pour fabriquer des potions.</p>', NULL, 1, 1, 7, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResVerveine', 'de la Verveine', '<p>La Verveine sera utilisée pour des potions.</p>', NULL, 2, 1, 8, NULL, NULL, NULL, NULL),
(NULL, 'ressource', NULL, 'ResPtCentauree', 'de la Petite Centaurée', '<p>La Petite Centaurée sera utilisée pour fabriquer des potions.</p>', NULL, 2, 1, 6, NULL, NULL, NULL, NULL),
(NULL, 'gibier', NULL, 'Gib01', 'Lapin', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 'ResNou=10,ResCuir=1'),
(NULL, 'gibier', NULL, 'Gib02', 'Lévrier', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 'ResNou=15,ResCuir=2'),
(NULL, 'gibier', NULL, 'Gib03', 'Sanglier', NULL, NULL, 2, NULL, NULL, 10, NULL, NULL, 'ResNou=30,ResCuir=5'),
(NULL, 'gibier', NULL, 'Gib04', 'Cerf', NULL, NULL, 3, NULL, NULL, 5, NULL, NULL, 'ResNou=100,ResCuir=20'),
(NULL, 'gibier', NULL, 'Gib05', 'Biche', NULL, NULL, 3, NULL, NULL, 3, NULL, NULL, 'ResNou=90,ResCuir=15'),
(NULL, 'gibier', NULL, 'Gib06', 'Canard', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 'ResNou=10,ResCuir=2'),
(NULL, 'gibier', NULL, 'Gib07', 'Chèvre', NULL, NULL, 2, NULL, NULL, 3, NULL, NULL, 'ResNou=20,ResCuir=10'),
(NULL, 'gibier', NULL, 'Gib08', 'Mouton', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, 'ResNou=25,ResCuir=10'),
(NULL, 'gibier', NULL, 'Gib09', 'Perdrix', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 'ResNou=11,ResCuir=2'),
(NULL, 'gibier', NULL, 'Gib10', 'Faisan', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 'ResNou=13,ResCuir=2'),
(NULL, 'gibier', NULL, 'Gib11', 'Renard', NULL, NULL, 2, NULL, NULL, 5, NULL, NULL, 'ResNou=18,ResCuir=10'),
(NULL, 'gibier', NULL, 'Gib12', 'Chevreuil', NULL, NULL, 3, NULL, NULL, 2, NULL, NULL, 'ResNou=50,ResCuir=25'),
(NULL, 'sort', NULL, 'SrtMaison', 'Sort "Retour à la Maison"', '<p>Grâce à ce sort, vous pourrez retourner directement à votre maison et ce de n''importe où sur la carte.</p>', 'magie', 1, 0, 250, NULL, NULL, NULL, 'ResAbsinthe=10,ResGui=30,ResPtCentauree=5,ResNou=100,ResOr=50,ResMinC=5,ResMinF=10'),
(NULL, 'sort', NULL, 'SrtAttaque10', 'Sort "Attaque +10"', '<p>Grâce à ce sort, vous augmenterez votre attaque de 10pts mais pour une durée de 5 jours.</p>', 'magie', 2, NULL, 300, 10, NULL, NULL, 'ResGui=30,ResPtCentauree=10,ResNou=150,ResBoi=150,ResAbsinthe=5'),
(NULL, 'sort', NULL, 'SrtDefense10', 'Sort "Défense +10"', '<p>Grâce à ce sort, vous augmenterez votre défense de 10pts mais pour une durée de 5 jours.</p>', 'magie', 2, NULL, 300, NULL, 10, NULL, 'ResGui=30,ResPtCentauree=10,ResNou=150,ResBoi=150,ResAbsinthe=5'),
(NULL, 'sort', NULL, 'SrtDistance1', 'Sort "Distance +1"', '<p>Grâce à ce sort, vous augmenterez votre distance d''attaque de 1pt mais pour une durée de 5 jours.</p>', 'magie', 2, NULL, 350, NULL, NULL, 1, 'ResGui=30,ResPtCentauree=10,ResNou=150,ResBoi=150,ResAbsinthe=5'),
(NULL, 'sort', NULL, 'SrtQuete', 'Sort "Quêtes divulguées"', '<p>Ce sort vous fournira les coordonnées de toutes vos quêtes en cours.</p>', 'magie', 1, NULL, 500, NULL, NULL, NULL, 'ResGui=30,ResPtCentauree=10,ResAbsinthe=5,ResMinC=150,Hydromel=5,ResNou=800'),
(NULL, 'sort', 8, 'LvrDruides', 'Livre des Druides', '<p>Grâce à ce livre, vous pourrez utiliser jusque''à 5 sorts en même temps.</p>', NULL, 4, 5, 800, NULL, NULL, NULL, NULL),
(NULL, 'potion', NULL, 'PotVie10', 'Gagne 10pts de vie', '<p>Vous allez pouvoir vous soigner de 10pts <img src="./img/icones/ic_vie.png" alt="Icone (vie)" title="Vie" height="20px" /></p>', 'chimie', 1, 10, 50, NULL, NULL, NULL, 'ResGui=10,ResPtCentauree=5,Hydromel=10,ResNou=50'),
(NULL, 'potion', NULL, 'PotVie15', 'Gagne 15pts de vie', '<p>Vous allez pouvoir vous soigner de 15pts <img src="./img/icones/ic_vie.png" alt="Icone (vie)" title="Vie" height="20px" /></p>', 'chimie', 2, 15, 75, NULL, NULL, NULL, 'ResGui=15,ResPtCentauree=8,Hydromel=10,ResNou=150'),
(NULL, 'potion', NULL, 'PotVie20', 'Gagne 20pts de vie', '<p>Vous allez pouvoir vous soigner de 20pts <img src="./img/icones/ic_vie.png" alt="Icone (vie)" title="Vie" height="20px" /></p>', 'chimie', 3, 20, 100, NULL, NULL, NULL, 'ResGui=20,ResPtCentauree=10,Hydromel=10,ResNou=200'),
(NULL, 'potion', NULL, 'PotDep10', 'Gagne 10pts de déplacement', '<p>Vous allez pouvoir vous déplacer de 10 <img src="./img/icones/ic_deplacement.png" alt="Icone (deplacement)" title="Deplacement" height="20px" /> en plus.</p>', 'chimie', 1, 10, 10, NULL, NULL, NULL, 'ResAbsinthe=10,ResVerveine=5,Hydromel=5,ResNou=50'),
(NULL, 'potion', NULL, 'PotDep15', 'Gagne 15pts de déplacement', '<p>Vous allez pouvoir vous déplacer de 15 <img src="./img/icones/ic_deplacement.png" alt="Icone (deplacement)" title="Deplacement" height="20px" /> en plus.</p>', 'chimie', 2, 15, 15, NULL, NULL, NULL, 'ResAbsinthe=15,ResVerveine=7,Hydromel=5,ResNou=150'),
(NULL, 'potion', NULL, 'PotDep20', 'Gagne 20pts de déplacement', '<p>Vous allez pouvoir vous déplacer de 20 <img src="./img/icones/ic_deplacement.png" alt="Icone (deplacement)" title="Deplacement" height="20px" /> en plus.</p>', 'chimie', 3, 20, 20, NULL, NULL, NULL, 'ResAbsinthe=20,ResVerveine=10,Hydromel=5,ResNou=200');


-- --------------------------------------------------------

--
-- Structure de la table `table_quetes`
--

CREATE TABLE IF NOT EXISTS `table_quetes` (
  `id_quete_en_cours` int(11) NOT NULL AUTO_INCREMENT,
  `quete_login` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `quete_id` int(11) NOT NULL,
  `quete_position` varchar(7) COLLATE latin1_general_ci DEFAULT NULL,
  `quete_vie` int(4) DEFAULT NULL,
  `quete_reussi` tinyint(1) DEFAULT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  PRIMARY KEY (`id_quete_en_cours`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `table_quete_lst`
--

CREATE TABLE IF NOT EXISTS `table_quete_lst` (
  `id_quete` int(11) NOT NULL AUTO_INCREMENT,
  `quete_type` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `quete_groupe` tinyint(1) DEFAULT NULL,
  `nom` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `description` text COLLATE latin1_general_ci,
  `niveau` int(2) NOT NULL DEFAULT '0',
  `gain_or` int(5) DEFAULT NULL,
  `gain_experience` int(5) DEFAULT NULL,
  `gain_points` int(2) NOT NULL DEFAULT '0',
  `id_objet` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `quete_vie` int(4) DEFAULT NULL,
  `quete_force` int(4) DEFAULT NULL,
  `quete_duree` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_quete`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=8 ;

--
-- Contenu de la table `table_quete_lst`
--

INSERT INTO `table_quete_lst` (`id_quete`, `quete_type`, `quete_groupe`, `nom`, `description`, `niveau`, `gain_or`, `gain_experience`, `gain_points`, `id_objet`, `quete_vie`, `quete_force`, `quete_duree`) VALUES
(1, 'monstre', NULL, 'Le Pasjeanti', '<p>Trouvez et tuez le Pasjeanti.</p><p>C''est un monstre sans pitié et il se déplace rapidement.</p><p>Faites donc attention à vous!</p>', 1, 250, 25, 35, NULL, 150, 15, NULL),
(2, 'recherche', NULL, 'Princesse Beltro', '<p>Retrouvez la Princesse Beltro.</p><p>Elle a disparu il y a déjà plusieurs jours.</p>', 0, 200, 15, 10, NULL, NULL, NULL, NULL),
(3, 'monstre', 1, 'Le Pasbo', '<p>Trouvez et tuez le Pasbo!</p><p>Il rode pas loin. Je vous conseille de l''attaquer à plusieurs car il est vraiment pas beau et très fort en plus.</p>', 3, 500, 50, 50, NULL, 300, 25, NULL),
(4, 'recherche', NULL, 'Le mage Cèplu', '<p>Retrouvez notre mage Céplu.</p><p>Il est amnésique et se perd à chaque fois qu''il part en foret.</p>', 1, 500, 10, 30, 'SrtMaison', NULL, NULL, 604800),
(5, 'objet', NULL, 'Epée de la vérité', '<p>Retrouvez l''épée de la vérité!</p><p>Grâce à cette épée, la véritée triomphera!</p>', 2, 50, 5, 75, 'EpeeV', NULL, NULL, NULL),
(6, 'romains', NULL, 'Garnison Romaine de Cracus', '<p>Trouvez, pourchassez et détruisez la garnison romaine de Cracus.</p><p>Elle est visible sur la carte et se déplace en même temps que vous.</p><p>Bonne chance!</p>', 3, 500, 20, 25, NULL, 350, 30, NULL),
(7, 'romains', NULL, 'Légionnaire Marcus', '<p>Légionnaire romain perdu. Dommage pour lui car ça va mal se passé pour lui. Faites lui sa fête!</p><p>Pour votre information, il se déplace d''une case toutes les heures et est visible sur la carte.</p><p>Bonne Chance!</p>', 2, 250, 20, 10, NULL, 150, 20, NULL),
(8, 'objet', NULL, 'Livre des Druides', '<p>Retrouvez le livre sacré des Druides de la forêt des Gui.</p>', 4, 150, 10, 15, 'LvrDruides', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `table_chat`
--

CREATE TABLE IF NOT EXISTS `table_chat` (
  `id_chat` int(11) NOT NULL AUTO_INCREMENT,
  `clan_chat` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `member_chat` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `date_chat` datetime DEFAULT NULL,
  `text_chat` text COLLATE latin1_general_ci,
  PRIMARY KEY (`id_chat`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;
