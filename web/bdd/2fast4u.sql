-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 15 mai 2026 à 08:00
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
  `Score` int(11) NOT NULL,
  `Date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `classement`
--

INSERT INTO `classement` (`ID`, `ID_utilisateur`, `Score`, `Date`) VALUES
(1, 1, 9070, '2026-05-13 15:20:56'),
(2, 1, 10350, '2026-05-13 15:25:10'),
(3, 1, 10440, '2026-05-13 15:28:20'),
(4, 1, 10350, '2026-05-13 15:33:08'),
(5, 1, 10480, '2026-05-13 15:33:13'),
(6, 1, 10410, '2026-05-13 15:34:00');

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
  `ID_Utilisateur` int(11) DEFAULT NULL
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
  `Email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`ID`, `Pseudo`, `Mot_de_passe`, `Admin`, `Email`) VALUES
(1, 'Rob1', '$2y$10$Elbhc5mHX3YtdrK.2kELdu7/aDMOgaAInmiYIwMCg8P29JVSa6F7O', 1, 'rob@rob.fr');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `classement`
--
ALTER TABLE `classement`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_utilisateur` (`ID_utilisateur`);

--
-- Index pour la table `niveau_cree`
--
ALTER TABLE `niveau_cree`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_niveau_utilisateur` (`ID_Utilisateur`);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `niveau_cree`
--
ALTER TABLE `niveau_cree`
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
  ADD CONSTRAINT `classement_ibfk_1` FOREIGN KEY (`ID_utilisateur`) REFERENCES `utilisateur` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `niveau_cree`
--
ALTER TABLE `niveau_cree`
  ADD CONSTRAINT `niveau_cree_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
