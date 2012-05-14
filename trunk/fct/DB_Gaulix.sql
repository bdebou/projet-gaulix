--
-- Base de données: `gaulix_be`
--
-- +------------------------------------------------------+
-- |                      STRUCTURES                      |
-- +------------------------------------------------------+

--
-- Structure de la table `table_joueurs`
--

CREATE TABLE IF NOT EXISTS `table_joueurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `mail` varchar(150) COLLATE latin1_general_ci DEFAULT NULL,
  `dates` datetime NOT NULL,
  `civilisation` varchar(8) COLLATE latin1_general_ci NOT NULL,
  `village` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `carriere` varchar(10) COLLATE latin1_general_ci NOT NULL,
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
  `nb_victoire` int(4) NOT NULL DEFAULT '0',
  `nb_vaincu` int(4) NOT NULL DEFAULT '0',
  `nb_mort` int(4) NOT NULL DEFAULT '0',
  `inventaire` text COLLATE latin1_general_ci,
  `clan` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `date_last_msg_lu` datetime DEFAULT NULL,
  `not_attaque` tinyint(1) DEFAULT NULL,
  `not_combat` tinyint(1) DEFAULT NULL,
  `nb_points` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Structure de la table `table_carrieres_lst`
--

CREATE TABLE IF NOT EXISTS `table_carrieres_lst` (
  `carriere_id` int(11) NOT NULL AUTO_INCREMENT,
  `carriere_class` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `carriere_civilisation` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `carriere_niveau` int(1) DEFAULT NULL,
  `carriere_code` varchar(8) COLLATE latin1_general_ci DEFAULT NULL,
  `carriere_nom` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `carriere_competences` text COLLATE latin1_general_ci,
  `carriere_debouchees` text COLLATE latin1_general_ci,
  `carriere_apprentissage` text COLLATE latin1_general_ci,
  PRIMARY KEY (`carriere_id`),
  UNIQUE KEY `carriere_code` (`carriere_code`,`carriere_nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

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
  `batiment_prix` text COLLATE latin1_general_ci,
  `batiment_points` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_batiment`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

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
  `cmp_lst_prix` text COLLATE latin1_general_ci,
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

--
-- Structure de la table `table_marche`
--

CREATE TABLE IF NOT EXISTS `table_marche` (
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
-- Structure de la table `table_objets`
--

CREATE TABLE IF NOT EXISTS `table_objets` (
  `id_objet` int(5) NOT NULL AUTO_INCREMENT,
  `objet_civilisation` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `objet_rarete` int(1) NOT NULL DEFAULT '0',
  `objet_type` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `objet_quete` int(3) DEFAULT NULL,
  `objet_code` varchar(15) COLLATE latin1_general_ci NOT NULL,
  `objet_nom` varchar(30) COLLATE latin1_general_ci DEFAULT NULL,
  `objet_description` text COLLATE latin1_general_ci,
  `objet_competence` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  `objet_niveau` int(2) DEFAULT NULL,
  `objet_ressource` text COLLATE latin1_general_ci,
  `objet_prix` int(5) DEFAULT NULL,
  `objet_attaque` int(2) DEFAULT NULL,
  `objet_defense` int(2) DEFAULT NULL,
  `objet_distance` int(2) DEFAULT NULL,
  `objet_cout` text COLLATE latin1_general_ci,
  PRIMARY KEY (`id_objet`),
  UNIQUE KEY `objet_code` (`objet_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

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

-- +------------------------------------------------------+
-- |                       CONTENU                        |
-- +------------------------------------------------------+

--
-- Contenu de la table `table_carrieres_lst`
--
INSERT INTO `table_carrieres_lst` (`carriere_id`, `carriere_class`, `carriere_civilisation`, `carriere_niveau`, `carriere_code`, `carriere_nom`, `carriere_competences`, `carriere_debouchees`, `carriere_apprentissage`) VALUES
(NULL, 'guerrier', 'gaulois', 0, 'GGEc', 'Eclaireur', NULL, 'GGGlg', NULL),
(NULL, 'guerrier', 'gaulois', 1, 'GGGlg', 'Guerrier léger', NULL, 'GGGld', NULL),
(NULL, 'guerrier', 'gaulois', 2, 'GGGld', 'Guerrier lourd', NULL, 'GGSc', NULL),
(NULL, 'guerrier', 'gaulois', 3, 'GGSc', 'Sonneur de Cornyx', NULL, 'GGCm', NULL),
(NULL, 'guerrier', 'gaulois', 4, 'GGCm', 'Chef de meute', NULL, 'GGGen', NULL),
(NULL, 'guerrier', 'gaulois', 5, 'GGGen', 'Général', NULL, 'GGVe', NULL),
(NULL, 'guerrier', 'gaulois', 6, 'GGVe', 'Vergobret', NULL, NULL, NULL),
(NULL, 'pretre', 'gaulois', 0, 'GPOv', 'Ovate', NULL, 'GPDcl', NULL),
(NULL, 'pretre', 'gaulois', 1, 'GPDcl', 'Druide de clan', NULL, 'GPDtr', NULL),
(NULL, 'pretre', 'gaulois', 2, 'GPDtr', 'Druide de tribu', NULL, 'GPVe', NULL),
(NULL, 'pretre', 'gaulois', 3, 'GPVe', 'Vénérable', NULL, 'GPPr', NULL),
(NULL, 'pretre', 'gaulois', 4, 'GPPr', 'Prophète', NULL, 'GPDe', NULL),
(NULL, 'pretre', 'gaulois', 5, 'GPDe', 'Devin', NULL, 'GPAa', NULL),
(NULL, 'pretre', 'gaulois', 6, 'GPAa', 'Archidruidre d''Albion', NULL, NULL, NULL),
(NULL, 'artisan', 'gaulois', 0, 'GAEs', 'Esclave', NULL, 'GAEsl', NULL),
(NULL, 'artisan', 'gaulois', 1, 'GAEsl', 'Esclave libre', NULL, 'GAHl', NULL),
(NULL, 'artisan', 'gaulois', 2, 'GAHl', 'Homme libre', NULL, 'GACcl', NULL),
(NULL, 'artisan', 'gaulois', 3, 'GACcl', 'Chef de clan', NULL, 'GAMc', NULL),
(NULL, 'artisan', 'gaulois', 4, 'GAMc', 'Membre du conseil', NULL, 'GAR', NULL),
(NULL, 'artisan', 'gaulois', 5, 'GAR', 'Roi', NULL, 'GAAr', NULL),
(NULL, 'artisan', 'gaulois', 6, 'GAAr', 'Archi roi', NULL, NULL, NULL),
-- Romains
(NULL, 'guerrier', 'romains', 0, 'RGLg', 'Légionnaire', NULL, 'RGDc', NULL),
(NULL, 'guerrier', 'romains', 1, 'RGDc', 'Décurion', NULL, 'RGCt', NULL),
(NULL, 'guerrier', 'romains', 2, 'RGCt', 'Centurion', NULL, 'RGPr', NULL),
(NULL, 'guerrier', 'romains', 3, 'RGPr', 'Prétorien', NULL, 'RGPpr', NULL),
(NULL, 'guerrier', 'romains', 4, 'RGPpr', 'Prefet du prétoire', NULL, 'RGTlg', NULL),
(NULL, 'guerrier', 'romains', 5, 'RGTlg', 'Tribun légionnaire', NULL, '', NULL),
(NULL, 'guerrier', 'romains', 6, 'RGMmi', 'Magister militium', NULL, NULL, NULL),
(NULL, 'pretre', 'romains', 0, 'RPQ', 'Questeur', NULL, 'RPE', NULL),
(NULL, 'pretre', 'romains', 1, 'RPE', 'Edile', NULL, 'RPT', NULL),
(NULL, 'pretre', 'romains', 2, 'RPT', 'Tribun', NULL, 'RPC', NULL),
(NULL, 'pretre', 'romains', 3, 'RPC', 'Censeur', NULL, 'RPP', NULL),
(NULL, 'pretre', 'romains', 4, 'RPP', 'Propreteur', NULL, 'RPD', NULL),
(NULL, 'pretre', 'romains', 5, 'RPD', 'Decemvir', NULL, 'RPTr', NULL),
(NULL, 'pretre', 'romains', 6, 'RPTr', 'Trecemvir', NULL, NULL, NULL),
(NULL, 'artisan', 'romains', 0, 'RAApp', 'Apprenti', NULL, 'RAAr', NULL),
(NULL, 'artisan', 'romains', 1, 'RAAr', 'Artisan', NULL, 'RAMar', NULL),
(NULL, 'artisan', 'romains', 2, 'RAMar', 'Maître artisan', NULL, 'RACh', NULL),
(NULL, 'artisan', 'romains', 3, 'RACh', 'Chevalier', NULL, 'RAChg', NULL),
(NULL, 'artisan', 'romains', 4, 'RAChg', 'Chef de guilde', NULL, 'RATpl', NULL),
(NULL, 'artisan', 'romains', 5, 'RATpl', 'Tribun de la plèbe', NULL, 'RATr', NULL),
(NULL, 'artisan', 'romains', 6, 'RATr', 'Trétarque', NULL, NULL, NULL);
--
-- Contenu de la table `table_batiment`
--

INSERT INTO `table_batiment` (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`, `batiment_points`) VALUES
(1, 'maison', 'Maison', '<p>Votre Maison, sans elle, vous êtes mort</p><p>Protégez la bien!</p>', 1000, 50, 20, 0, 0, NULL, 250),
(2, 'mur', 'Palissade', '<p>Une pallissade de sapin, elle ralentira vos ennemis avant qu''ils ne s''attaquent à votre Quartier Général</p>', 300, NULL, 20, NULL, 0, 'ResOr=200,ResBoi=100,ResPie=500,ResNou=300', 50),
(3, 'tour', 'Tour', '<p>Cette tour permet d''attaquer vos ennemis si ils s''approchent trop.</p>', 200, 25, 20, 2, 0, 'ResOr=200,ResBoi=350,ResPie=600,ResNou=450', 80),
(4, 'entrepot', 'Entrepot', '<p>Vous pouvez stocker ici tout ce que vous voulez.</p><p>Tout ce qui est stocker ici sera toujours disponible si vous mourrez. Ce qui est quand même utile pour récupérer rapidement des armes, boucliers, ...</p>', 300, NULL, 20, NULL, 0, 'ResOr=300,ResBoi=600,ResPie=200,ResNou=500', 150),
(5, 'potager', 'Potager', '<p>Le Potager est un bâtiment de production de plantes et de nourritures spécialisées. Au plus le potager est de niveau supérieur au plus les plantes et les ressources sont de niveaux supérieurs. Les compétences élevage et alchimie à des niveaux supérieurs permettent d''augmenter la production. Les esclaves permettent aussi d''augmenter le nombre de ressources produites. Vous devrez choisir le type de production que vous souhaitez produire. Si vous stoppez la production avant la fin du délai, la production commencée est perdue.</p>', 1000, 20, 20, NULL, 0, 'ResOr=500,ResBoi=300,ResPie=600,ResNou=700', 200),
(6, 'ferme', 'Ferme', '<p>Vous y cultiverez toute une série de légumes différents pour vous nourrir.</p><p>Ou vous pourrez élever des animaux pour en tiré de la viande, bien plus rentable que les légumes.</p>', 500, NULL, 30, NULL, 0, 'ResOr=350,ResBoi=350,ResPie=200,ResNou=500', 150),
(7, 'ressource', 'pierre', 'Une source de pierres.', NULL, NULL, NULL, NULL, 0, NULL, 0),
(8, 'ressource', 'bois', 'Une source de bois.', NULL, NULL, NULL, NULL, 0, NULL, 0),
(9, 'marche', 'Marché', '<p>A votre marché, vous pourrez troquer tout type de matériaux.</p><p>Les troques sont créés par chaque joueur</p>', 400, 10, 30, NULL, 0, 'ResOr=400,ResBoi=150,ResPie=300,ResNou=600', 180),
(10, 'mine', 'Mine', '<p>La mine est le bâtiment de production de métal par excellence. Les matériaux sont des ressources brutes et pour la plupart des objets il vous faudra les raffiner. Les compétences tailleurs de pierres et métallurgie peuvent augmenter votre production ainsi que le nombre d''esclave que vous pouvez mettre dedans. La mine tout comme les carrières peuvent produire des ressources annexes. Vous devrez choisir lors de la production le type de production que vous souhaitez produire. Si vous stoppez la production avant la fin du délai, la production commencée sera perdue.</p>', 500, NULL, 30, NULL, 1, 'ResOr=500,ResBoi=200,ResPie=250,ResNou=750', 150),
(11, 'mer', 'Mer du Nord', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'cote', 'Côtes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'eau', 'Rivière', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'montagne', 'Montagne', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'dolmen', 'Cromlech', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'carriere', 'Carrière de pierre', '<p>La carrière de pierre est le bâtiment de production de pierre. Les matériaux sont des ressources brutes et pour la plupart des objets il vous faudra les raffiner. Les compétences tailleur de pierre et ingénierie peuvent augmenter votre production ainsi que le nombre d''esclave y travaillant.  La carrière peut produire des ressources annexes. Vous devrez choisir le type de production que vous souhaitez produire. Si vous stoppez la production avant la fin du délai, la production commencée sera perdue.</p>', 450, NULL, 30, NULL, 1, 'ResOr=500', 150),
-- (17, 'maison', `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, 3, `batiment_prix`,`batiment_points`),
-- (18, 'maison', `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, 4, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
-- (`id_batiment`, `batiment_type`, `batiment_nom`, `batiment_description`, `batiment_vie`, `batiment_attaque`, `batiment_defense`, `batiment_distance`, `batiment_niveau`, `batiment_prix`,`batiment_points`),
;

--
-- Contenu de la table `table_competence_lst`
--

INSERT INTO `table_competence_lst` (`cmp_lst_id`, `cmp_lst_type`, `cmp_lst_nom`, `cmp_lst_description`, `cmp_lst_niveau`, `cmp_lst_temp`, `cmp_lst_prix`) VALUES
(NULL, 'competence', 'mineur', '<p>Vous allez pouvoir exploiter les carrières en y extrayant de la pierre <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" />.</p>', 1, 3600, 'ResOr=50'),
(NULL, 'competence', 'mineur', '<p>Vous allez pouvoir mieux exploiter les carrières en y extrayant plus de pierres <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" />.</p>', 2, 86400, 'ResOr=100,ResNou=50'),
(NULL, 'competence', 'mineur', '<p>Vous aurez la capacité de trouver des gisement d''or <img src="./img/icones/ic_ResOr.png" alt="Ressource Or" title="Ressource Or" /> en plus de la pierre <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" /> dans les carrières ou dans votre mine.</p>', 3, 108000, 'ResOr=150,ResNou=100'),
(NULL, 'competence', 'mineur', '<p>Vous aurez la capacité de trouver en plus dans votre mine des gisements de minerai de fer <img src="./img/icones/ic_ResMinF.png" alt="Ressource minerai de fer" title="Ressource minerai de fer" height="20px" />.</p>', 4, 129600, 'ResOr=300,ResNou=150'),
(NULL, 'competence', 'mineur', '<p>Vous aurez la capacité de trouver en plus dans votre mine des gisements de minerai de cuivre <img src="./img/icones/ic_ResMinC.png" alt="Ressource minerai de cuivre" title="Ressource minerai de cuivre" height="20px" />.</p>', 5, 151200, 'ResOr=500,ResNou=300'),
(NULL, 'competence', 'bucheron', '<p>Pour couper du <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (50x).</p>', 1, 3600, 'ResOr=50'),
(NULL, 'competence', 'bucheron', '<p>Pour couper plus de <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (60x).</p>', 2, 86400, 'ResOr=100,ResNou=50'),
(NULL, 'competence', 'bucheron', '<p>Pour couper encore plus de <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (70x).</p>', 3, 108000, 'ResOr=150,ResNou=100'),
(NULL, 'competence', 'bucheron', '<p>Pour couper toujours plus de <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (80x).</p>', 4, 129600, 'ResOr=300,ResNou=150'),
(NULL, 'competence', 'bucheron', '<p>Pour restaurer le cycle de la forêt.</p><p>Grâce à cette compétence, vous n''épuiserez aucune ressource de <img src="./img/icones/ic_ResBoi.png" alt="Ressource Bois" title="Ressource Bois" height="20px" /> (100x).</p>', 5, 151200, 'ResOr=450,ResNou=300'),
(NULL, 'competence', 'tailleurp', '<p>Tailleur de pierre pour transformer une <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" /> en pierre pointue <img src="./img/icones/ic_PrP.png" alt="Icone (pierre pointue)" title="Pierre pointue" height="20px" />.</p>', 1, 7200, 'ResOr=200,ResNou=100'),
(NULL, 'competence', 'tailleurp', '<p>Tailleur de pierre pour tailler une <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" /> en pierre platte tranchante <img src="./img/icones/ic_PrPT.png" alt="Icone (pierre platte tranchante)" title="Pierre platte tranchante" height="20px" />.</p>', 2, 10800, 'ResOr=400,ResNou=200'),
(NULL, 'competence', 'tailleurp', '<p>Tailleur de pierre pour tailler plus de <img src="./img/icones/ic_ResPie.png" alt="Ressource Pierre" title="Ressource Pierre" height="20px" /> en pierre pointue <img src="./img/icones/ic_PrP.png" alt="Icone (pierre pointue)" title="Pierre pointue" height="20px" /> ou en pierre platte tranchante <img src="./img/icones/ic_PrPT.png" alt="Icone (pierre platte tranchante)" title="Pierre platte tranchante" height="20px" />.</p>', 3, 129600, 'ResOr=600,ResNou=400'),
(NULL, 'competence', 'chasseur', '<p>Chasseur de petit gibier (lapin, pigeon, canard).</p>', 1, 86400, 'ResOr=100,ResNou=100'),
(NULL, 'competence', 'chasseur', '<p>Chasseur de gibier moyen (sanglier, chêvre).</p>', 2, 108000, 'ResOr=200,ResNou=200'),
(NULL, 'competence', 'chasseur', '<p>Chasseur de gros gibier (Cerf, biche).</p>', 3, 129600, 'ResOr=300,ResNou=250'),
(NULL, 'competence', 'charpentier', '<p>Vous pourrez fabriquer de court bâton dur <img src="./img/icones/ic_CBtD.png" alt="Icone (court bâton dur)" title="Court bâton dur" height="20px" /> ou souple <img src="./img/icones/ic_CBtS.png" alt="Icone (court bâton souple)" title="Court bâton souple" height="20px" />.</p>', 1, 86400, 'ResOr=200,ResNou=50,ResBoi=200'),
(NULL, 'competence', 'charpentier', '<p>Vous pourrez fabriquer de petite planche de bois <img src="./img/icones/ic_PPlB.png" alt="Icone (petite planche de bois)" title="Petite planche de bois" height="20px" />.</p>', 2, 108000, 'ResOr=300,ResNou=50,ResBoi=250'),
(NULL, 'competence', 'charpentier', '<p>Vous pourrez fabriquer de long bâton dur <img src="./img/icones/ic_LBtD.png" alt="Icone (long bâton dur)" title="Long bâton dur" height="20px" /> ou souple <img src="./img/icones/ic_LBtS.png" alt="Icone (long bâton souple)" title="Long bâton souple" height="20px" /> et de grande planche <img src="./img/icones/ic_GPlB.png" alt="Icone (grande planche de bois)" title="Grande planche de bois" height="20px" />.</p>', 3, 129600, 'ResOr=450,ResNou=50,ResBoi=300'),
(NULL, 'competence', 'boucher', '<p>Pour récupérer 3x plus de <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> sur le gibier.</p>', 1, 86400, 'ResOr=100,ResNou=100'),
(NULL, 'competence', 'boucher', '<p>Pour récupérer 6x plus de <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> sur le gibier.</p>', 2, 108000, 'ResOr=200,ResNou=200'),
(NULL, 'competence', 'boucher', '<p>Pour récupérer 10x plus de <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> sur le gibier.</p>', 3, 129600, 'ResOr=300,ResNou=300'),
(NULL, 'competence', 'tanneur', '<p>Pour récupérer la peau du gibier et en faire du cuir <img src="./img/icones/ic_ResCuir.png" alt="Icone (cuir)" title="Cuir" height="20px" />.</p>', 1, 7200, 'ResOr=50,ResNou=50'),
(NULL, 'competence', 'tanneur', '<p>Pour récupérer 2x plus de peau du gibier et en faire du cuir <img src="./img/icones/ic_ResCuir.png" alt="Icone (cuir)" title="Cuir" height="20px" />.</p>', 2, 86400, 'ResOr=100,ResNou=100'),
(NULL, 'competence', 'tanneur', '<p>Pour récupérer 3x plus de peau du gibier et en faire du cuir <img src="./img/icones/ic_ResCuir.png" alt="Icone (cuir)" title="Cuir" height="20px" />.</p>', 3, 108000, 'ResOr=150,ResNou=150'),
(NULL, 'competence', 'tanneur', '<p>Pour récupérer 4x plus de peau du gibier et en faire du cuir <img src="./img/icones/ic_ResCuir.png" alt="Icone (cuir)" title="Cuir" height="20px" />.</p>', 4, 129600, 'ResOr=250,ResNou=250'),
(NULL, 'competence', 'tissage', '<p>Pour fabriquer des tissus <img src="./img/icones/ic_Tissu.png" alt="Icone (tissu)" title="Tissu" height="20px" /> et des cordes <img src="./img/icones/ic_Corde.png" alt="Icone (corde)" title="Corde" height="20px" />.</p>', 1, 86400, 'ResOr=100,ResNou=50,ResBoi=20'),
(NULL, 'competence', 'tissage', '<p>Pour fabriquer des bolgas.</p>', 2, 108000, 'ResOr=150,ResNou=100,ResBoi=40'),
(NULL, 'competence', 'tissage', '<p>Pour fabriquer des grand bolgas.</p>', 3, 129600, 'ResOr=250,ResNou=150,ResBoi=60'),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 1, 86400, 'ResOr=150,ResNou=100,ResBoi=150,ResBoi=50'),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 2, 108000, 'ResOr=250,ResNou=200,ResBoi=200,ResPie=75'),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 3, 129600, 'ResOr=400,ResNou=300,ResBoi=250,ResPie=100'),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 4, 151200, 'ResOr=650,ResNou=400,ResBoi=300,ResPie=125'),
(NULL, 'competence', 'armurier', '<p>Pour fabriquer des armes, boucliers, cuirasses, jambières ou casques.</p>', 5, 172800, 'ResOr=1050,ResNou=500,ResBoi=350,ResPie=150'),
(NULL, 'competence', 'metallurgie', '<p>Pour fabriquer des pointes de flèches <img src="./img/icones/ic_PtFl.png" alt="Icone (pointe de flèche)" title="Pointe de flèche" height="20px" /> et de lances <img src="./img/icones/ic_PtL.png" alt="Icone (pointe de lance)" title="Pointe de lance" height="20px" />.</p>', 1, 86400, 'ResOr=300,ResNou=100,ResBoi=500'),
(NULL, 'competence', 'metallurgie', '<p>Pour fabriquer des plaques de fer <img src="./img/icones/ic_PlF.png" alt="Icone (plaque de fer)" title="Plaque de fer" height="20px" /> et des mailles <img src="./img/icones/ic_MlF.png" alt="Icone (mailles de fer)" title="Mailles de fer" height="20px" />.</p>', 2, 108000, 'ResOr=500,ResNou=200,ResBoi=1000'),
(NULL, 'competence', 'metallurgie', '<p>Pour fabriquer des épées.</p>', 3, 129600, 'ResOr=800,ResNou=300,ResBoi=1500'),
(NULL, 'competence', 'cuisine', '<p>Pour transformer de la Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> en Hydromel <img src="./img/icones/ic_hydromel.png" alt="Icone (hydromel)" title="Hydromel" height="20px" />.</p>', 1, 43200, 'ResOr=250,ResNou=150,ResBoi=150'),
(NULL, 'competence', 'cuisine', '<p>Pour transformer plus de Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> en Hydromel <img src="./img/icones/ic_hydromel.png" alt="Icone (hydromel)" title="Hydromel" height="20px" />.</p>', 2, 86400, 'ResOr=500,ResNou=250,ResBoi=300'),
(NULL, 'competence', 'cuisine', '<p>Pour transformer encore plus de Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" /> en Hydromel <img src="./img/icones/ic_hydromel.png" alt="Icone (hydromel)" title="Hydromel" height="20px" />.</p>', 3, 108000, 'ResOr=1000,ResNou=350,ResBoi=450'),
(NULL, 'competence', 'chimie', '<p>La Chimie vous permettra de préparer des potions en tout genre.</p>', 1, 86400, 'ResOr=500,ResNou=300,ResBoi=100,ResHydromel=50'),
(NULL, 'competence', 'chimie', '<p>La Chimie vous permettra de préparer des potions en tout genre.</p>', 2, 172800, 'ResOr=700,ResNou=500,ResBoi=200,ResHydromel=100'),
(NULL, 'competence', 'chimie', '<p>La Chimie vous permettra de préparer des potions en tout genre.</p>', 3, 259200, 'ResOr=1000,ResNou=750,ResBoi=300,ResHydromel=150'),
(NULL, 'competence', 'agriculture', '<p>Cela vous permettra de produire plus de Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" />.</p>', 1, 86400, 'ResOr=200,ResNou=500,ResBoi=100,ResHydromel=10'),
(NULL, 'competence', 'agriculture', '<p>Cela vous permettra de produire soit de Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" />, soit du Cotton <img src="./img/icones/ic_ResFibC.png" alt="Icone (fibre de cotton)" title="Fibre de cotton" height="20px" />.</p>', 2, 129600, 'ResOr=500,ResNou=1000,ResBoi=200,ResHydromel=10'),
(NULL, 'competence', 'agriculture', '<p>Cela vous permettra de produire soit de la Nourriture <img src="./img/icones/ic_ResNou.png" alt="Icone (nourriture)" title="Nourriture" height="20px" />, soit du Cotton <img src="./img/icones/ic_ResFibC.png" alt="Icone (fibre de cotton)" title="Fibre de cotton" height="20px" />, soit du Miel <img src="./img/icones/ic_ResMiel.png" alt="Icone (du miel)" title="Du miel" height="20px" />.</p>', 3, 216000, 'ResOr=600,ResNou=1200,ResBoi=300,ResHydromel=10'),
(NULL, 'competence', 'magie', '<p>La compétence magie, vous donnera la possibilité de préparer des sorts en tout genre.</p>', 1, 198720, 'ResOr=500,ResNou=700,ResBoi=300,ResPie=200,ResHydromel=150'),
(NULL, 'competence', 'magie', '<p>La compétence magie, vous donnera la possibilité de préparer des sorts en tout genre.</p>', 2, 397440, 'ResOr=750,ResNou=1000,ResBoi=500,ResPie=300,ResHydromel=150');
--
-- Contenu de la table `table_marche`
--

INSERT INTO `table_marche` (`ID_troc`, `vendeur`, `acheteur`, `vente_nourriture`, `vente_bois`, `vente_pierre`, `vente_or`, `achat_nourriture`, `achat_bois`, `achat_pierre`, `achat_or`, `date_vente`, `status_vente`, `type_vendeur`, `contenu_vendeur`) VALUES
(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'marchant', NULL);

--
-- Contenu de la table `table_objets`
--

INSERT INTO `table_objets` (`id_objet`, `objet_civilisation`, `objet_rarete`, `objet_type`, `objet_quete`, `objet_code`, `objet_nom`, `objet_description`, `objet_competence`, `objet_niveau`, `objet_ressource`, `objet_prix`, `objet_attaque`, `objet_defense`, `objet_distance`, `objet_cout`) VALUES
(1, 'gaulois', 0, 'Ressource', NULL, 'ResNou40', 'Nourriture', 'test nourriture', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'gaulois', 3, 'Ressource', NULL, 'ResNou10', '10 de Nourriture', '10 de nourriture en test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'gaulois', 0, 'Construction', NULL, 'CBtD', 'Court Bâton Dur', 'Court bâton dur pour la fabrication d''autre objets', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'gaulois', 0, 'Construction', NULL, 'CBtS', 'Court Bâton Souple', 'Court bâton souple pour la fabrication d''autre objets', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'gaulois', 0, 'Divers', NULL, 'ResDep4', '4 déplacements', '4 points de déplacements en plus.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'gaulois', 0, 'Ressource', NULL, 'ResCuir', 'du Cuir', 'Le cuir sera utilisé pour la fabrication d''autres objets', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);


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

--
-- Contenu de la table `table_carte`
--

INSERT INTO `table_carte` (`id_case_carte`, `coordonnee`, `login`, `id_type_batiment`, `contenu_batiment`, `res_pierre`, `res_bois`, `res_nourriture`, `date_action_batiment`, `etat_batiment`, `date_amelioration`, `tmp_amelioration`, `niveau_batiment`, `date_last_attaque`, `detruit`) VALUES
-- Carte A
(NULL, 'a,1,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,0,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,2,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,13,5', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,6,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,6,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'a,0,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,2', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,3', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,4', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,5', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,6', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,7', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,8', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,9', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,10', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,11', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,12', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,0,13', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,2', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,3', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,4', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,5', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,6', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,7', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,8', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,9', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,10', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,11', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,12', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,1,13', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,2', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,3', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,4', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,5', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,6', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,7', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,8', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,9', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,10', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,11', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,12', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,2,13', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,2', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,3', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,4', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,5', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,6', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,7', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,8', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,9', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,10', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,3,11', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,4,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,4,2', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,4,3', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,4,4', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,4,5', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,4,6', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,4,7', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,4,8', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,4,9', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,5,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,5,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,5,2', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,5,3', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,5,4', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,5,5', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,5,6', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,6,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,6,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,6,2', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,6,3', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,7,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,7,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,8,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,8,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,9,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,9,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,10,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,10,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,11,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,11,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,12,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,12,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,13,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'a,13,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
-- Carte B
(NULL, 'b,3,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,1,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,3,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,12,7', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,3,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,3,10', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'b,0,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'b,0,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'b,0,2', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'b,0,3', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'b,1,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'b,1,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
-- Carte C
(NULL, 'c,12,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,4,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,5,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,11,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,5,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'c,0,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte D
(NULL, 'd,9,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,8,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,9,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,7,10', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,0,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'd,5,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte E
(NULL, 'e,1,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,5,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,4,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,2,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,0,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,8,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'e,0,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'e,0,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'e,1,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'e,1,1', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
(NULL, 'e,2,0', NULL, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL),
-- Carte F
(NULL, 'f,2,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,13,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,2,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,5,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,7,0', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'f,11,0', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte G
(NULL, 'g,9,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,1,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,0,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,4,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,1,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'g,12,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte H
(NULL, 'h,1,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,1,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,12,5', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,6,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,2,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'h,2,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte I
(NULL, 'i,4,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,10,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,4,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,8,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,6,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'i,9,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte J
(NULL, 'j,4,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,3,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,11,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,1,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,9,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'j,2,12', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte K
(NULL, 'k,6,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,12,6', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,10,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,5,0', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,10,5', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'k,0,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte L
(NULL, 'l,2,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,1,7', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,2,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,9,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,7,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'l,6,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte M
(NULL, 'm,8,5', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,11,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,4,1', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,8,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,3,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'm,8,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte N
(NULL, 'n,3,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,10,6', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,8,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,11,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,1,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'n,6,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte O
(NULL, 'o,5,7', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,12,0', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,6,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,4,5', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,11,12', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'o,5,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte P
(NULL, 'p,1,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,2,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,9,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,1,3', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,4,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'p,7,13', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte Q
(NULL, 'q,3,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,12,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,5,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,2,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,7,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'q,4,1', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte R
(NULL, 'r,7,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,3,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,8,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,5,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,0,12', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'r,6,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte S
(NULL, 's,10,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,6,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,12,6', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,10,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,12,0', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 's,8,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte T
(NULL, 't,10,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,8,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,10,2', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,10,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,13,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 't,11,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte U
(NULL, 'u,5,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,12,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,12,3', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,2,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,6,5', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'u,12,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte V
(NULL, 'v,0,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,13,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,7,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,13,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,7,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'v,0,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte W
(NULL, 'w,8,9', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,3,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,8,7', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,9,7', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,10,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'w,0,6', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte X
(NULL, 'x,6,4', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,8,8', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,1,12', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,2,4', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,1,2', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'x,5,8', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
-- Carte Y
(NULL, 'y,11,11', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,5,13', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,4,10', NULL, 7, NULL, 5000, NULL, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,9,11', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,9,9', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL),
(NULL, 'y,5,10', NULL, 8, NULL, NULL, 5000, NULL, '1970-01-01 01:00:00', NULL, NULL, NULL, 0, NULL, NULL);

