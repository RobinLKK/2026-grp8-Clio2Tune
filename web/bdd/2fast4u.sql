-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 28 mai 2026 à 13:11
-- Version du serveur : 5.7.24
-- Version de PHP : 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `2fast4u`
--

-- --------------------------------------------------------

--
-- Structure de la table `classement`
--

CREATE TABLE `classement` (
  `ID` int(11) NOT NULL,
  `ID_utilisateur` int(11) DEFAULT NULL,
  `ID_Niveau` int(11) NOT NULL,
  `Points` int(11) NOT NULL DEFAULT '0',
  `Date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `niveau_cree`
--

CREATE TABLE `niveau_cree` (
  `ID` int(11) NOT NULL,
  `Taille` int(11) NOT NULL,
  `Nombre_coups` int(11) NOT NULL DEFAULT '0',
  `Difficulte` int(11) NOT NULL DEFAULT '1',
  `grille` text,
  `Nom_du_niveau` text,
  `ID_Utilisateur` int(11) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `niveau_cree`
--

INSERT INTO `niveau_cree` (`ID`, `Taille`, `Nombre_coups`, `Difficulte`, `grille`, `Nom_du_niveau`, `ID_Utilisateur`, `code`) VALUES
(1, 5, 0, 1, '[\r\n            [0, 0, 1, 1, 1],\r\n            [0, 0, 1, 2, 2],\r\n            [4, 4, 1, 2, 2],\r\n            [4, 4, 3, 3, 3],\r\n            [4, 4, 3, 3, 3]\r\n        ]', 'Entrance exam', NULL, NULL),
(2, 5, 0, 1, '[\r\n            [0, 0, 1, 1, 1],\r\n            [0, 2, 2, 1, 1],\r\n            [0, 2, 3, 3, 3],\r\n            [4, 2, 4, 4, 3],\r\n            [4, 4, 4, 3, 3]\r\n        ]', 'Learn the basics', NULL, NULL),
(3, 6, 0, 1, '[\r\n            [0, 0, 0, 1, 1, 1],\r\n            [0, 0, 0, 1, 1, 1],\r\n            [2, 2, 2, 3, 3, 1],\r\n            [2, 4, 4, 3, 3, 3],\r\n            [4, 4, 4, 4, 3, 5],\r\n            [4, 4, 5, 5, 5, 5]\r\n        ]', 'Beginner race', NULL, NULL),
(4, 6, 0, 1, '[          [0, 0, 1, 1, 2, 2],\r\n            [0, 0, 1, 1, 1, 2],\r\n            [3, 4, 4, 4, 4, 2],\r\n            [3, 3, 4, 4, 5, 5],\r\n            [3, 3, 4, 5, 5, 5],\r\n            [3, 3, 5, 5, 5, 5]\r\n        ]', 'Next level', NULL, NULL),
(5, 7, 0, 2, '[\r\n            [0, 0, 0, 0, 1, 1, 1],\r\n            [2, 2, 0, 0, 1, 1, 1],\r\n            [2, 2, 2, 3, 3, 1, 1],\r\n            [2, 3, 3, 3, 3, 4, 4],\r\n            [5, 5, 3, 3, 6, 6, 4],\r\n            [5, 5, 5, 6, 6, 6, 4],\r\n            [5, 5, 6, 6, 6, 4, 4]\r\n        ]', 'Potential GOAT', NULL, NULL),
(6, 8, 0, 2, '[\r\n            [0, 0, 0, 0, 1, 1, 1, 1],\r\n            [2, 0, 0, 1, 1, 1, 1, 5],\r\n            [2, 2, 0, 3, 3, 1, 5, 5],\r\n            [2, 2, 3, 3, 3, 5, 5, 5],\r\n            [4, 4, 3, 3, 6, 6, 5, 5],\r\n            [4, 4, 4, 6, 6, 6, 6, 5],\r\n            [4, 4, 7, 6, 6, 6, 7, 7],\r\n            [4, 7, 7, 7, 7, 7, 7, 7]\r\n        ]', 'Certified MVP', NULL, NULL),
(7, 9, 0, 3, '[\r\n            [5, 5, 5, 5, 8, 8, 8, 8, 8],\r\n            [5, 5, 5, 5, 8, 8, 8, 8, 8],\r\n            [0, 0, 5, 5, 2, 2, 2, 8, 8],\r\n            [0, 0, 0, 2, 2, 2, 2, 7, 7],\r\n            [0, 0, 1, 2, 2, 2, 7, 7, 7],\r\n            [1, 1, 1, 2, 2, 4, 4, 4, 7],\r\n            [1, 1, 1, 3, 3, 4, 4, 4, 4],\r\n            [1, 1, 3, 3, 3, 3, 6, 6, 6],\r\n            [1, 3, 3, 3, 3, 6, 6, 6, 6]\r\n        ]', 'A Step Further', NULL, NULL),
(8, 10, 0, 4, '[\r\n            [3, 3, 3, 8, 8, 8, 2, 2, 2, 2],\r\n            [3, 3, 3, 8, 8, 2, 2, 2, 2, 0],\r\n            [3, 5, 5, 8, 8, 7, 7, 2, 0, 0],\r\n            [5, 5, 5, 9, 9, 7, 7, 0, 0, 0],\r\n            [3, 9, 9, 9, 9, 7, 7, 0, 0, 0],\r\n            [3, 9, 9, 1, 1, 7, 4, 4, 4, 6],\r\n            [3, 3, 9, 1, 1, 1, 4, 4, 4, 6],\r\n            [3, 3, 3, 1, 1, 4, 4, 4, 6, 6],\r\n            [3, 3, 3, 1, 4, 4, 4, 6, 6, 6],\r\n            [3, 3, 3, 1, 4, 6, 6, 6, 6, 6]\r\n        ]', 'Breaking The Speed Limit', NULL, NULL),
(9, 11, 0, 4, '[\r\n            [7, 7, 2, 2, 2, 2, 5, 5, 5, 5, 5],\r\n            [7, 7, 2, 2, 2, 5, 5, 5, 5, 5, 8],\r\n            [0, 0, 2, 2, 3, 3, 6, 6, 8, 8, 8],\r\n            [0, 0, 0, 3, 3, 3, 6, 6, 8, 8, 8],\r\n            [0, 1, 1, 3, 3, 3, 6, 6, 9, 9, 9],\r\n            [1, 1, 1, 3, 4, 4, 6, 6, 9, 9, 9],\r\n            [1, 1, 1, 4, 4, 4, 6, 6, 9, 9, 9],\r\n            [1, 1, 4, 4, 4, 4, 7, 7, 7, 9, 10],\r\n            [1, 1, 4, 4, 4, 4, 7, 7, 7, 10, 10],\r\n            [1, 1, 4, 4, 7, 7, 7, 7, 7, 10, 10],\r\n            [1, 1, 4, 4, 7, 7, 7, 7, 10, 10, 10]\r\n        ]', 'One More Race', NULL, NULL),
(10, 12, 0, 5, ' [\r\n            [4, 4, 0, 0, 0, 1, 1, 1, 2, 2, 2, 2],\r\n            [4, 4, 4, 0, 0, 1, 1, 1, 2, 2, 2, 2],\r\n            [4, 4, 4, 5, 5, 5, 1, 2, 2, 2, 3, 3],\r\n            [8, 4, 4, 5, 5, 5, 6, 6, 2, 2, 3, 3],\r\n            [8, 4, 4, 5, 5, 5, 6, 6, 7, 7, 3, 3],\r\n            [8, 8, 5, 5, 5, 6, 6, 6, 7, 7, 3, 3],\r\n            [8, 8, 9, 9, 9, 6, 6, 6, 7, 7, 7, 3],\r\n            [8, 8, 9, 9, 9, 10, 10, 7, 7, 7, 7, 7],\r\n            [8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 7, 7],\r\n            [8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 11, 11],\r\n            [8, 8, 9, 10, 10, 10, 10, 10, 11, 11, 11, 11],\r\n            [8, 8, 10, 10, 10, 10, 10, 11, 11, 11, 11, 11]\r\n        ]', 'The ULTIMATE Challenge', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `score`
--

CREATE TABLE `score` (
  `ID` int(11) NOT NULL,
  `ID_Niveau` int(11) NOT NULL,
  `ID_Utilisateur` int(11) NOT NULL,
  `Chrono` int(11) NOT NULL,
  `Date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `ID` int(11) NOT NULL,
  `Pseudo` varchar(64) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  `Admin` tinyint(1) NOT NULL DEFAULT '0',
  `Email` varchar(255) DEFAULT NULL,
  `Nombre_niveau` int(11) NOT NULL DEFAULT '0',
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`ID`, `Pseudo`, `Mot_de_passe`, `Admin`, `Email`, `Nombre_niveau`, `avatar`) VALUES
(1, 'Rob1', '$2y$10$Elbhc5mHX3YtdrK.2kELdu7/aDMOgaAInmiYIwMCg8P29JVSa6F7O', 1, 'rob@rob.fr', 0, 'media/avatars/avatar_1.jpg');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `classement`
--
ALTER TABLE `classement`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_classement_utilisateur` (`ID_utilisateur`),
  ADD KEY `fk_classement_niveau` (`ID_Niveau`);

--
-- Index pour la table `niveau_cree`
--
ALTER TABLE `niveau_cree`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_niveau_utilisateur` (`ID_Utilisateur`);

--
-- Index pour la table `score`
--
ALTER TABLE `score`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_score_niveau` (`ID_Niveau`),
  ADD KEY `fk_score_utilisateur` (`ID_Utilisateur`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Pseudo` (`Pseudo`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `classement`
--
ALTER TABLE `classement`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `niveau_cree`
--
ALTER TABLE `niveau_cree`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `score`
--
ALTER TABLE `score`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `classement`
--
ALTER TABLE `classement`
  ADD CONSTRAINT `classement_ibfk_1` FOREIGN KEY (`ID_utilisateur`) REFERENCES `utilisateur` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `classement_ibfk_2` FOREIGN KEY (`ID_Niveau`) REFERENCES `niveau_cree` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `niveau_cree`
--
ALTER TABLE `niveau_cree`
  ADD CONSTRAINT `niveau_cree_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `score`
--
ALTER TABLE `score`
  ADD CONSTRAINT `score_ibfk_1` FOREIGN KEY (`ID_Niveau`) REFERENCES `niveau_cree` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `score_ibfk_2` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
