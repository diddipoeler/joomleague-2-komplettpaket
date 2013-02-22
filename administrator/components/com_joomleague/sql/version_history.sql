--
-- Tabellenstruktur für Tabelle `#__joomleague_version_history`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_version_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

TRUNCATE TABLE  `#__joomleague_version_history`;

INSERT INTO `#__joomleague_version_history` (`id`, `date`, `text`) VALUES
(NULL, '2013-02-20', 'kleinere bugs (wie immer), landesverband f&uuml;r vereine, landesverband f&uuml;r spieler, grafische bearbeitung der spielsysteme, l&auml;nder sql file steht jetzt zur verf&uuml;gung'),
(NULL, '2013-02-14', 'mod_joomleague_sports_type_statistics, rosterpositionen mit spielfeld im matchreport'),
(NULL, '2013-02-13', 'tabellen nach divisionen, matrix nach divisionen, tabs im matchreport, tabs in nextmatch, bilder in den divisionen, textvariablen angepasst');