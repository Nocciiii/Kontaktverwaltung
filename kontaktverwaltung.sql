-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 13. Jun 2019 um 03:03
-- Server-Version: 10.1.37-MariaDB
-- PHP-Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `kontaktverwaltung`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kontakt`
--

CREATE TABLE `kontakt` (
  `ID` int(5) NOT NULL,
  `Vorname` varchar(15) COLLATE utf8_german2_ci NOT NULL,
  `Nachname` varchar(15) COLLATE utf8_german2_ci NOT NULL,
  `Adresse` int(5) NOT NULL,
  `Telefonnummer` int(10) NOT NULL,
  `Email` varchar(30) COLLATE utf8_german2_ci NOT NULL,
  `Nutzer` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logindaten`
--

CREATE TABLE `logindaten` (
  `ID` int(5) NOT NULL,
  `Benutzername` varchar(17) COLLATE utf8_german2_ci NOT NULL,
  `Email` varchar(30) COLLATE utf8_german2_ci NOT NULL,
  `Passwort` varchar(100) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ort`
--

CREATE TABLE `ort` (
  `ID` int(5) NOT NULL,
  `Ort` varchar(15) COLLATE utf8_german2_ci NOT NULL,
  `Postleitzahl` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `strasse`
--

CREATE TABLE `strasse` (
  `ID` int(5) NOT NULL,
  `StrassenName` varchar(30) COLLATE utf8_german2_ci NOT NULL,
  `Hausnummer` int(5) NOT NULL,
  `OrtNr` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `kontakt`
--
ALTER TABLE `kontakt`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `logindaten`
--
ALTER TABLE `logindaten`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Benutzername` (`Benutzername`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Passwort` (`Passwort`);

--
-- Indizes für die Tabelle `ort`
--
ALTER TABLE `ort`
  ADD PRIMARY KEY (`ID`,`Postleitzahl`);

--
-- Indizes für die Tabelle `strasse`
--
ALTER TABLE `strasse`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `kontakt`
--
ALTER TABLE `kontakt`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `logindaten`
--
ALTER TABLE `logindaten`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `ort`
--
ALTER TABLE `ort`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `strasse`
--
ALTER TABLE `strasse`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
