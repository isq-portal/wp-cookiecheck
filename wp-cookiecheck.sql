-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 30. Mai 2023 um 17:01
-- Server-Version: 10.4.19-MariaDB
-- PHP-Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `isq_www_wp`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_isq_cookiecheck_config`
--

CREATE TABLE `wp_isq_cookiecheck_config` (
  `id` mediumint(9) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `wp_isq_cookiecheck_config`
--

INSERT INTO `wp_isq_cookiecheck_config` (`id`, `name`, `value`) VALUES
(1, 'cookie_policy_url', '/das_isq/datenschutzerklaerung/'),
(2, 'panel_toggle_position', 'left'),
(3, 'default_setting', 'disabled'),
(4, 'domain_name', 'isqbb_wp');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_isq_cookiecheck_snippets`
--

CREATE TABLE `wp_isq_cookiecheck_snippets` (
  `id` mediumint(9) NOT NULL,
  `title` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `display` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `wp_isq_cookiecheck_snippets`
--

INSERT INTO `wp_isq_cookiecheck_snippets` (`id`, `title`, `slug`, `label`, `display`) VALUES
(1, 'Google Analytics', 'google_analytics', 'Google Analytics', 0),
(2, 'Matomo', 'matomo', 'Webanalyse (Matomo)', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_isq_cookiecheck_snippet_cookies`
--

CREATE TABLE `wp_isq_cookiecheck_snippet_cookies` (
  `id` mediumint(9) NOT NULL,
  `snippet_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(50) NOT NULL,
  `domain` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `wp_isq_cookiecheck_snippet_cookies`
--

INSERT INTO `wp_isq_cookiecheck_snippet_cookies` (`id`, `snippet_id`, `name`, `path`, `domain`) VALUES
(1, 1, '_ga', '/', 'isqbb_wp');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_isq_cookiecheck_snippet_variables`
--

CREATE TABLE `wp_isq_cookiecheck_snippet_variables` (
  `id` mediumint(9) NOT NULL,
  `snippet_id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `wp_isq_cookiecheck_snippet_variables`
--

INSERT INTO `wp_isq_cookiecheck_snippet_variables` (`id`, `snippet_id`, `label`, `slug`, `description`, `value`) VALUES
(1, 1, 'Tracking ID', 'tracking_id', 'Your unique tracking ID provided by Google', 'qwerty');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `wp_isq_cookiecheck_config`
--
ALTER TABLE `wp_isq_cookiecheck_config`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `wp_isq_cookiecheck_snippets`
--
ALTER TABLE `wp_isq_cookiecheck_snippets`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `wp_isq_cookiecheck_snippet_cookies`
--
ALTER TABLE `wp_isq_cookiecheck_snippet_cookies`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `wp_isq_cookiecheck_snippet_variables`
--
ALTER TABLE `wp_isq_cookiecheck_snippet_variables`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `wp_isq_cookiecheck_config`
--
ALTER TABLE `wp_isq_cookiecheck_config`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `wp_isq_cookiecheck_snippets`
--
ALTER TABLE `wp_isq_cookiecheck_snippets`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `wp_isq_cookiecheck_snippet_cookies`
--
ALTER TABLE `wp_isq_cookiecheck_snippet_cookies`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `wp_isq_cookiecheck_snippet_variables`
--
ALTER TABLE `wp_isq_cookiecheck_snippet_variables`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
