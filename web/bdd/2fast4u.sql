-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `2fast4u` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `2fast4u`;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `Pseudo` VARCHAR(64) NOT NULL,
  `Mot_de_passe` VARCHAR(64) NOT NULL,
  `Admin` TINYINT(1) NOT NULL,
  `Nombre_niveau` INT(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `classement`
--

CREATE TABLE `classement` (
  `ID_niveau` INT(11) NOT NULL AUTO_INCREMENT,
  `ID_utilisateur` INT(11) DEFAULT NULL,
  `Score` INT(11) NOT NULL,
  PRIMARY KEY (`ID_niveau`),
  KEY `fk_utilisateur` (`ID_utilisateur`),
  CONSTRAINT `classement_ibfk_1`
    FOREIGN KEY (`ID_utilisateur`)
    REFERENCES `utilisateur` (`ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;